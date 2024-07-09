<?php

namespace app\modules\controllers;

use Yii;
use app\controllers\Controller;
use app\modules\models\Post;
use app\modules\models\form\PostForm;
use app\modules\models\search\PostSearch;
use app\modules\HTTP_STATUS;

class PostController extends Controller
{
    public function actionIndex()
    {
        $posts = Post::find()->all();
        return $this->json(true, ["posts" => $posts], 'All post');
    }

    public function actionCreate()
    {
        $postForm = new PostForm();
        $postForm->load(Yii::$app->request->post());

        if (!$postForm->validate() || !$postForm->save()) {
            return $this->json(false, ["errors" => $postForm->getErrors()], "Can't create new product", HTTP_STATUS::BAD_REQUEST);
        }

        return $this->json(true, ["product" => $postForm], "Create product successfully");
    }

    public function actionView($id)
    {
        $post = Post::find()->where(["id" => $id])->one();
        if (empty($post)) {
            return $this->json(false, [], 'Post not found', HTTP_STATUS::NOT_FOUND);
        }
        return $this->json(true, ["post" => $post], 'Find post successfully');
    }

    public function actionUpdate($id)
    {
        $post = Post::find()->where(['id' => $id])->one();
        if (empty($post)) {
            return $this->json(false, [], 'Post not found', HTTP_STATUS::NOT_FOUND);
        }

        $post->load(Yii::$app->request->post());
        if (!$post->validate() || !$post->save()) {
            return $this->json(false, ["errors" => $post->getErrors()], "Can't update post", HTTP_STATUS::BAD_REQUEST);
        }
        return $this->json(true, ["post" => $post], "Update post successfully");
    }

    public function actionDelete($id)
    {
        $post = Post::find()->where(["id" => $id])->one();
        if (empty($post)) {
            return $this->json(false, [], "Post not found", HTTP_STATUS::NOT_FOUND);
        }

        $post->load(Yii::$app->request->post());
        if (!$post->delete()) {
            return $this->json(false, ['errors' => $post->getErrors()], "Can't delete post", HTTP_STATUS::BAD_REQUEST);
        }
        return $this->json(true, [], 'Delete post successfully');
    }

    public function actionSearch()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->json(true, ["posts" => $dataProvider->getModels()], "Find successfully");
    }

}