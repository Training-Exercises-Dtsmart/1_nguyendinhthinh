<?php

namespace app\models;

use \app\models\base\OrderItem as BaseOrderItem;

/**
 * This is the model class for table "order_item".
 */
class OrderItem extends BaseOrderItem
{
    public function formName()
    {
        return "";
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'product_name' => 'productName'
        ]);
    }

    public function getProductName(): string
    {
        return isset($this->product) ? $this->product->name : '';
    }
}
