<?php

namespace app\models;

use \app\models\base\Order as BaseOrder;

/**
 * This is the model class for table "order".
 */
class Order extends BaseOrder
{
    const TOTAL_DEFAULT = 0;
    const STATUS_PENDING = 0;
    const PAYMENT_METHOD_COD = 0;
    const PAYMENT_METHOD_ZALO_PAY = 1;
    const PAYMENT_STATUS_PENDING = 0;
    const PAYMENT_STATUS_PAID = 1;
    const PAYMENT_STATUS_CANCELLED = 2;


    public function formName()
    {
        return "";
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'items' => 'orderItems'
        ]);
    }
}
