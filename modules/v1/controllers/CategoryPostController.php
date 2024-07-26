<?php

namespace app\modules\v1\controllers;

use app\controllers\Controller;
use app\modules\v1\models\form\CategoryPostForm;
use Yii;
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
        $model = new CategoryPostForm();
        $model->load(Yii::$app->request->post());
        if (!$model->validate() || !$model->save()) {
            return $this->json(false, ["errors" => $model->getErrors()], "Can't create new category post", 400);
        }
        return $this->json(true, ["category_post" => $model], "Create category post successfully");
    }

    public function actionDelete($id)
    {

    }
}