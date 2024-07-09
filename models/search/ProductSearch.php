<?php

namespace app\models\search;

use app\models\Product;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    public $keyword;
    public $pageSize;

    public function rules()
    {
        return [
            [['id', 'category_id', 'pageSize'], 'integer'],
            [['product_name', 'keyword'], 'safe']
        ];
    }

    public function search($params)
    {
        $query = Product::find();
        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ]
        ]);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'product_name' => $this->product_name,
        ]);

        $query->andFilterWhere(["or", ["LIKE", "product_name", $this->keyword]]);

        return $dataProvider;
    }
}