<?php

namespace app\modules\v1\controllers;

use app\components\ZaloPayComponent;
use app\controllers\Controller;
use app\models\Order;
use app\models\OrderItem;
use app\modules\HttpStatus;
use app\modules\v1\models\Product;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class OrderController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['callback', 'index', 'query', 'refund', 'query-refund'],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionGenerateQr()
    {
//        $json = file_get_contents('php://input');
//        $data = json::decode($json, true);
//        if (empty($data['accountNo']) || empty($data['accountName']) || empty($data['acqId']) || empty($data['amount'])) {
//            return $this->asJson([
//                'code' => '01',
//                'desc' => 'Thiếu dữ liệu đầu vào'
//            ]);
//        }

        $data = Yii::$app->request->post();

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.vietqr.io/v2/generate')
            ->setHeaders([
                'x-client-id' => '55e2537a-e411-488d-93b1-959223743505',
                'x-api-key' => '7a658b20-fdd4-432b-a4c5-44bf720a5193',
                'Content-Type' => 'application/json',
            ])
            ->setContent(Json::encode([
                'accountNo' => $data['accountNo'],
                'accountName' => $data['accountName'],
                'acqId' => $data['acqId'],
                'amount' => $data['amount'], // Số tiền cần điều chỉnh theo nhu cầu
                'addInfo' => $data['addInfo'],
                'template' => 'compact'
            ]))
            ->send();
        if ($response->isOk) {
            return $response->data;
        }
        return null; // Hoặc xử lý lỗi theo yêu cầu
    }

    public function actionCallback()
    {
        $params = json_decode(file_get_contents('php://input'), true);
        $result = Yii::$app->zaloPay->callBack($params);
        return $result;
    }


    public function actionCreate()
    {
        $user = Yii::$app->user->identity;
        $items = Yii::$app->request->post('items', []);

        if (!$items) {
            return $this->json(false, [], 'No items provided', HttpStatus::BAD_REQUEST);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = new Order();
            $order->load(Yii::$app->request->post());
            $order->user_id = $user->id;
            $order->total = Order::TOTAL_DEFAULT;
            $order->status = Order::STATUS_PENDING;

            if (!$order->save()) {
                return $this->json(false, [], "Failed to create order", HttpStatus::BAD_REQUEST);
            }

            $totalAmount = 0;

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $product = Product::find()->where(["id" => $productId])->one();
                if (!$product || $quantity > $product->stock) {
                    return $this->json(false, [], "Invalid product or insufficient stock", HttpStatus::BAD_REQUEST);
                }

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $productId;
                $orderItem->quantity = $quantity;
                $orderItem->price = $product->price;
                if (!$orderItem->save()) {
                    return $this->json(false, [], "Failed to create order item", HttpStatus::BAD_REQUEST);
                }

                $totalAmount += $product->price * $quantity;
            }
            $order->total = $totalAmount;
            if (!$order->save()) {
                return $this->json(false, [], "Failed to create order", HttpStatus::BAD_REQUEST);
            }
            $transaction->commit();

            if ($order->payment_method == Order::PAYMENT_METHOD_ZALO_PAY) {
                $orderParams = [
                    'orderId' => $order->id,
                    'amount' => $order->total,
                    'description' => 'Payment for order #' . $order->id,
                ];
                $orderCheckout = Yii::$app->zaloPay->createOrder($orderParams);
                if ($orderCheckout) {
                    return $this->json(true, ['order' => $order, 'payment' => $orderCheckout], 'Order create successfully', HttpStatus::OK);
                }
                return $this->json(false, [], 'Failed to create order');
            }
            return $this->json(true, ['order' => $order], 'Order create successfully', HttpStatus::OK);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->json(false, [], $e->getMessage(), HttpStatus::BAD_REQUEST);
        }
    }

    public function actionQuery($id)
    {
        $order = Order::find()->where(["id" => $id])->one();
        if (!$order) {
            return $this->json(false, [], "Order not found", HttpStatus::NOT_FOUND);
        }

        $queryOrder = Yii::$app->zaloPay->queryOrder($order->app_trans_id);
        if ($queryOrder['return_code'] != 1) {
            return $this->json(false, ['order' => $queryOrder], "Failed to query order", HttpStatus::BAD_REQUEST);
        }
        return $this->json(true, ['order' => $queryOrder], 'Order query successfully', HttpStatus::OK);
    }

    public function actionRefund($id)
    {
        $order = Order::find()->where(["id" => $id])->one();
        if (!$order) {
            return $this->json(false, [], "Order not found", HttpStatus::NOT_FOUND);
        }

        $refundOrder = Yii::$app->zaloPay->refundOrder($order->zp_trans_id, $order->total);
        return $this->json(true, ['order' => $refundOrder], 'Refund order successfully', HttpStatus::OK);
    }

    public function actionQueryRefund($id)
    {
        $order = Order::find()->where(["id" => $id])->one();
        if (!$order) {
            return $this->json(false, [], "Order not found", HttpStatus::NOT_FOUND);
        }
        $refundOrder = Yii::$app->zaloPay->queryRefund($order->m_refund_id);
        return $this->json(true, ['order' => $refundOrder], 'Refund order successfully', HttpStatus::OK);
    }
}