<?php

namespace app\modules\models\search;
use app\models\Post;
use yii\data\ActiveDataProvider;

class PostSearch extends Post{
    public $keyword;
    public $category_name;
    public function rules(){
        return [
            [['id', 'category_id'], 'integer'],
            [['category_name','title', 'body', 'keyword'], 'safe']
        ];
    }

    public function search($params){
        $query = Post::find()->joinWith('category');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);


        $query->andFilterWhere(["or", ["LIKE", "title", $this->keyword], 
            ["LIKE", "category_name", $this->category_name]]);

        return $dataProvider;

    }   
}
