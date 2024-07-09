<?php

namespace app\modules\models;

use app\models\Product as BaseProduct;

class Product extends BaseProduct
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            "category_name" => "categoryName",
        ]);
    }

    public function getCategoryName()
    {
        return isset($this->categoryProduct) ? $this->categoryProduct->name : null;
    }
}