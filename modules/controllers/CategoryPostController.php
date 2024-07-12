<?php

namespace app\modules\controllers;

use Yii;
use app\controllers\Controller;
use app\modules\models\form\CategoryPostForm;
use yii\filters\auth\HttpBearerAuth;

class CategoryPostController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['index', 'view', 'search'],
        ];
        return $behaviors;
    }

    public function actionCreate()
    {
        $user = Yii::$app->user->identity;

        $model = new CategoryPostForm();
        $model->load(Yii::$app->request->post());
        $model->created_by = $user->id;
        if (!$model->validate() || !$model->save()) {
            return $this->json(false, ["errors" => $model->getErrors()], "Can't create new category post", 400);
        }
        return $this->json(true, ["category_post" => $model], "Create category post successfully");

    }
}