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
        ];
        return $behaviors;
    }

    public function actionIndex()
    {

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
                $product = Product::find()->where("id = $productId")->one();
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

            $transaction->commit();

            return $this->json(true, ['order' => $order], 'Order created successfully', HttpStatus::OK);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->json(false, [], $e->getMessage(), HttpStatus::BAD_REQUEST);
        }
//        $session = Yii::$app->session;
//        $session->open();
//        var_dump($session['cart']);
//        die;
    }
}