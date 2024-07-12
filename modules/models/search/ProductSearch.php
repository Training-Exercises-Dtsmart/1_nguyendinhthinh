<?php

namespace app\modules\models\search;

use app\modules\models\Product;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    public $keyword;
    public $category_product_name;

    public function rules()
    {
        return [
            [['id', 'category_product_id'], 'integer'],
            [['category_product_name', 'name', 'keyword'], 'safe']
        ];
    }

    public function search($params)
    {
        $query = Product::find()->joinWith('categoryProduct');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product.id' => $this->id,
            'product.name' => $this->name,
        ]);

        $query->andFilterWhere(["or", ["LIKE", "product.name", $this->keyword],
            ["LIKE", "category_product.name", $this->category_product_name],
            ["LIKE", "category_product.name", $this->keyword],]);

        return $dataProvider;

    }
}