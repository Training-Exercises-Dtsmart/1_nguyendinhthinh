<?php

namespace app\modules\v1\models\search;

use app\modules\v1\models\Product;
use yii\data\ActiveDataProvider;
use yii\rest\Serializer;

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
//        \Yii::$container->set('yii\data\Pagination', [
//            'pageSizeLimit' => [1, 5],
//        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
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

        $serializer = new Serializer(['collectionEnvelope' => 'items']);
        $data = $serializer->serialize($dataProvider);

        return $data;

    }
}