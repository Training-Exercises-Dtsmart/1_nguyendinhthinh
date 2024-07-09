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
            [['id', 'category_product_id', 'pageSize'], 'integer'],
            [['name', 'keyword'], 'safe']
        ];
    }

    public function search($params)
    {
        $query = Product::find()->joinWith('categoryProduct');
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
            'product.id' => $this->id,
            'product.name' => $this->name,
        ]);

        $query->andFilterWhere(["or", ["LIKE", "product.name", $this->keyword]]);
        return $dataProvider;
    }
}