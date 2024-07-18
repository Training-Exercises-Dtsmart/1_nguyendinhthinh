<?php

namespace app\modules\v1\controllers;

use app\controllers\Controller;
use app\modules\HTTP_STATUS;
use app\modules\v1\jobs\TestQueue;
use app\modules\v1\models\form\PostForm;
use app\modules\v1\models\Post;
use app\modules\v1\models\queue\SendMailJob;
use app\modules\v1\models\search\PostSearch;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class PostController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['view', 'search', 'sendmail', 'sendmail-queue', 'job', 'queue'],
        ];
//        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = true;
        return $behaviors;
    }

    public function actionSendmailQueue()
    {
        $job = new SendMailJob();
        Yii::$app->queue->push($job);
        return $this->json(true, [], 'Queue successfully sent');
    }

    public function actionQueue()
    {
        Yii::$app->queue->push(new TestQueue('abc'));
    }

    public function actionJob()
    {
        $job = new TestQueue('dinhthinh');
        Yii::$app->queue->push($job);
        return $this->json();
    }

    public function actionSendmail()
    {
        //Send mail
        Yii::$app->mailer->compose()
            ->setFrom('from@example.com')
            ->setTo('toan70868@gmail.com')
            ->setSubject('Link queue')
            ->setTextBody('Mail: https://voixanh.net/post/28/gui-mail-trong-yii2-framework-p13')
            ->setHtmlBody('<b>Queue: https://voixanh.net/post/29/tim-hieu-ve-queue-trong-yii2-framework-p14</b>')
            ->send();

//        $messages = [];
//        $user = 'kissuot2@gmail.com';
//        for ($i = 0; $i < 100; $i++) {
//            $messages[] = Yii::$app->mailer->compose()
//                ->setFrom('yii-basic@gmail.com')
//                ->setTo($user)
//                ->setSubject('Demo gửi multi mail trong Yii2')
//                ->setHtmlBody('<b>Nội dung gửi multi mail trong Yii2</b>');
//        }
//        Yii::$app->mailer->sendMultiple($messages);
    }

    public function actionIndex()
    {
        $posts = Post::find()->all();
        return $this->json(true, ["posts" => $posts], 'All post');
    }

    public function actionCreate()
    {
        $user = Yii::$app->user->identity;
        $postForm = new PostForm();
        $postForm->load(Yii::$app->request->post());
        $postForm->user_id = $user->id;

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

        if (!Yii::$app->user->can('updatePost', ['post' => $post])) {
            return $this->json(false, [], 'Permission denied', HTTP_STATUS::FORBIDDEN);
        }

        if (empty($post)) {
            return $this->json(false, [], 'Post not found', HTTP_STATUS::NOT_FOUND);
        }

        $post->load(Yii::$app->request->post());
        if (!$post->validate() || !$post->save()) {
            return $this->json(false, ["errors" => $post->getErrors()], "Can't update post", HTTP_STATUS::BAD_REQUEST);
        }
        return $this->json(true, ["post" => $post], "Update post successfully", HTTP_STATUS::OK);
    }

    public function actionDelete($id)
    {
        $post = Post::find()->where(["id" => $id])->one();
        if (!Yii::$app->user->can('deletePost', ['post' => $post])) {
            return $this->json(false, [], 'Permission denied', HTTP_STATUS::FORBIDDEN);
        }

        if (empty($post)) {
            return $this->json(false, [], "Post not found", HTTP_STATUS::NOT_FOUND);
        }

        $post->status = Post::DELETE;
        $post->save();
//        if (!$post->delete()) {
//            return $this->json(false, ['errors' => $post->getErrors()], "Can't delete post", HTTP_STATUS::BAD_REQUEST);
//        }

        return $this->json(true, [], 'Delete post successfully');
    }

    public function actionSearch()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->json(true, ["posts" => $dataProvider->getModels()], "Find successfully");
    }

}