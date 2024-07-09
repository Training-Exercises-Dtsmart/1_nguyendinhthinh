<?php

namespace app\modules\controllers;

use app\controllers\Controller;
use app\modules\models\form\CategoryPostForm;

use Yii;

class CategoryPostController extends Controller
{
    public function actionCreate()
    {
        $model = new CategoryPostForm();
        $model->load(Yii::$app->request->post());
        if (!$model->validate() || !$model->save()) {
            return $this->json(false, ["errors" => $model->getErrors()], "Can't create new category post", 400);
        }
        return $this->json(true, ["category_post" => $model], "Create category post successfully");

    }
}