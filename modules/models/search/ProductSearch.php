<?php

namespace app\modules\models\search;

use app\modules\models\Product;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product{
    public $keyword;
    public $category_name;
    public function rules(){
        return [
            [['id', 'category_id'], 'integer'],
            [['category_name','product_name', 'keyword'], 'safe']
        ];
    }

    public function search($params){
        $query = Product::find()->joinWith('category');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'product_name' => $this->product_name,
        ]);

        $query->andFilterWhere(["or", ["LIKE", "product_name", $this->keyword], 
            ["LIKE", "category_name", $this->category_name]]);


        return $dataProvider;

    }
}