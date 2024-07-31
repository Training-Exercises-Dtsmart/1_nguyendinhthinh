<?php

namespace app\modules\v1\controllers;

use app\components\ZaloPayComponent;
use app\controllers\Controller;
use app\models\Order;
use app\models\OrderItem;
use app\modules\HttpStatus;
use app\modules\v1\models\Product;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Console;

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