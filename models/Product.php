<?php

namespace app\models;

use \app\models\base\Product as BaseProduct;

/**
 * This is the model class for table "product".
 */
class Product extends BaseProduct
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = 6;

    public function formName()
    {
        return "";
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            "category_name" => "categoryName",
            "product_images" => "productImages"
        ]);
    }

    public function getCategoryName()
    {
        return isset($this->categoryProduct) ? $this->categoryProduct->name : null;
    }
    
}
