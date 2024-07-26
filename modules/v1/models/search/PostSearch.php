<?php

namespace app\modules\v1\models\search;

use app\modules\v1\models\Post;
use yii\data\ActiveDataProvider;
use yii\rest\Serializer;

class PostSearch extends Post
{
    public $keyword;
    public $category_name;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['category_post_name', 'title', 'body', 'keyword'], 'safe']
        ];
    }

    public function search($params)
    {
        $query = Post::find()->joinWith('categoryPost');

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
            'id' => $this->id,
        ]);


        $query->andFilterWhere(["or", ["LIKE", "title", $this->keyword],
            ["LIKE", "category_name", $this->category_name]]);

        $serializer = new Serializer(['collectionEnvelope' => 'items']);
        $data = $serializer->serialize($dataProvider);

        return $data;

    }
}
