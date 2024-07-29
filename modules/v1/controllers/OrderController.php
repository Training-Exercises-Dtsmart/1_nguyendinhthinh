<?php

namespace app\modules\v1\controllers;

use app\controllers\Controller;
use app\models\Order;
use app\models\OrderItem;
use app\modules\HttpStatus;
use app\modules\v1\models\Product;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;

class OrderController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['index', 'query', 'refund', 'query-refund'],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $config = [
            "app_id" => 2553,
            "key1" => "PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL",
            "key2" => "kLtgPl8HHhfvMuDHPwKfgfsY4Ydm9eIz",
            "endpoint" => "https://sb-openapi.zalopay.vn/v2/create"
        ];

        $embeddata = '{}'; // Merchant's data
        $items = '[]'; // Merchant's data
        $transID = rand(0, 1000000); //Random trans id
        $order = [
            "app_id" => $config["app_id"],
            "app_time" => round(microtime(true) * 1000), // miliseconds
            "app_trans_id" => date("ymd") . "_" . $transID, // translation missing: vi.docs.shared.sample_code.comments.app_trans_id
            "app_user" => "user123",
            "item" => $items,
            "embed_data" => $embeddata,
            "amount" => 100000,
            "description" => "Lazada - Payment for the order #$transID",
            "bank_code" => "zalopayapp"
        ];

        // appid|app_trans_id|appuser|amount|apptime|embeddata|item
        $data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"]
            . "|" . $order["app_time"] . "|" . $order["embed_data"] . "|" . $order["item"];


        $order["mac"] = hash_hmac("sha256", $data, $config["key1"]);
        $context = stream_context_create([
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($order)
            ]
        ]);

        $resp = file_get_contents($config["endpoint"], false, $context);
        $result = json_decode($resp, true);

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
            $order->user_id = $user->id;
            $order->total = Order::TOTAL_DEFAULT;
            $order->status = Order::STATUS_PENDING;

            if (!$order->save()) {
                throw new \Exception('Failed to create order');
            }

            $totalAmount = 0;

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $product = Product::find()->where(["id" => $productId])->one();
                if (!$product || $quantity > $product->stock) {
                    throw new \Exception('Invalid product or insufficient stock');
                }

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $productId;
                $orderItem->quantity = $quantity;
                $orderItem->price = $product->price;
                if (!$orderItem->save()) {
                    throw new \Exception('Failed to create order item');
                }

                $totalAmount += $product->price * $quantity;
            }
            $order->total = $totalAmount;
            if (!$order->save()) {
                throw new \Exception('Failed to update order total amount');
            }

            $orderParams = [
                'orderId' => $order->id,
                'amount' => $order->total,
                'description' => 'Payment for order #' . $order->id,
            ];
            $orderCheckout = Yii::$app->zaloPay->createOrder($orderParams);
            if (!$orderCheckout) {
                return $this->json(false, [], 'Failed to create order');
            }
            $transaction->commit();
            return $this->json(true, ['order' => $order, 'payment' => $orderCheckout], 'Order created successfully', HttpStatus::OK);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->json(false, [], $e->getMessage(), HttpStatus::BAD_REQUEST);
        }
//        $session = Yii::$app->session;
//        $session->open();
//        var_dump($session['cart']);
//        die;
    }

    public function actionQuery($app_trans_id)
    {
        $order = Yii::$app->zaloPay->queryOrder($app_trans_id);
        return $order;
    }

    public function actionRefund($zp_trans_id, $amount)
    {
        $refundOrder = Yii::$app->zaloPay->refundOrder($zp_trans_id, $amount);
        return $refundOrder;
    }

    public function actionQueryRefund($m_refund_id)
    {
        $refundOrder = Yii::$app->zaloPay->queryRefund($m_refund_id);
        return $refundOrder;
    }
}