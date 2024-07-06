<?php 

namespace app\modules\models;

use app\models\Product as BaseProduct;

class Product extends BaseProduct{

    public function fields(){
        return array_merge(parent::fields(), [
            "category_name" => "categoryName",
        ]);
    }

    public function getCategoryName(){
        // if(isset($this->category->category_name)){
        //     return $this->category->category_name;
        // }
        // return '';

        return isset($this->category->category_name) ? $this->category->category_name : '';
    }
}