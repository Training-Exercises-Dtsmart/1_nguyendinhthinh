<?php

namespace app\controllers;

use app\models\form\CategoryProductForm;
use app\models\CategoryProduct;
use app\controllers\Controller;
use Yii;
use yii\data\ActiveDataProvider;

class CategoryProductController extends Controller
{
    public function actionIndex()
    {
        $query = CategoryProduct::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 2
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]
        ]);

        return $this->json(true, ["category_product" => $dataProvider->getModels()], "Success");
    }

    public function actionCreate()
    {
        $categoryProductForm = new CategoryProductForm();
        $categoryProductForm->load(Yii::$app->request->post());

        if (!$categoryProductForm->validate() || !$categoryProductForm->save()) {
            return $this->json(false, ["errors" => $categoryProductForm->getErrors()], "Can't create category product", 400);
        }
        return $this->json(true, ["category_product" => $categoryProductForm], "Create category product successfully");
    }
}