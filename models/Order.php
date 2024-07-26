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
