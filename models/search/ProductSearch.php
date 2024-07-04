<?php

namespace app\models\search;

use app\models\Product;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product{
    public $keyword;

    public function rules(){
        return [
            [['id', 'category_id'], 'integer'],
            [['product_name', 'keyword'], 'safe']
        ];
    }

    public function search($params){
        $query = Product::find();

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

        $query->andFilterWhere(["or",["LIKE", "product_name", $this->keyword]]);

        return $dataProvider;

    }
}