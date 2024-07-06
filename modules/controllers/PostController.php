<?php

namespace app\modules\controllers;

use app\controllers\Controller;
use app\models\Post;
use app\modules\models\form\PostForm;
use Yii;
use app\modules\models\search\PostSearch;

class PostController extends Controller{
    public function actionIndex(){   
        $post = Post::find()->all();
        return $this->json(true, ["posts" => $post]);
    }

    public function actionCreate(){
        $postForm = new PostForm();
        $postForm->load(Yii::$app->request->post());

        if(!$postForm->validate() || !$postForm->save()){
            return $this->json(false, ["errors" => $postForm->getErrors()], "Can't create new product");
        }

        return $this->json(true, ["product" => $postForm], "Create product successfully");
    }

    public function actionView($id){
        $post = Post::find()->where(["id" => $id])->one();
        
        $post->load(Yii::$app->request->post());
        return $this->json(true, ["post" => $post]);
    }

    public function actionUpdate($id){
        $post = Post::find()->where(['id' => $id])->one();

        $post->load(Yii::$app->request->post());
        if(!$post->validate() || !$post->save()){
            return $this->json(false, ["errors" => $post->getErrors()], "Can't update post");
        }
        return $this->json(true, ["post" => $post], "Update post successfully");
    }

    public function actionDelete($id){
        $post = Post::find()->where(["id" => $id])->one();
        if(!$post){
            return $this->json(false, [], "Post not found");
        }
        $post->load(Yii::$app->request->post());
        if(!$post->delete()){
            return $this->json(false, ['errors' => $post->getErrors()], "Can't delete post");
        }
        return $this->json(true, [], 'Delete post successfully');
    }

    public function actionSearch(){
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        if($dataProvider->getCount() == 0){
            return $this->json(false, [], "Not found");
        }

        return $this->json(true, ["posts" => $dataProvider->getModels()], "Find successfully");
    }

}