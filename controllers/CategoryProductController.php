<?php

namespace app\controllers;

use app\models\form\CategoryProductForm;
use yii\rest\Controller;
use Yii;

class CategoryProductController extends Controller{
    public function actionIndex(){
        return 1;
    }

    public function actionCreate(){
        $categoryProductForm = new CategoryProductForm();
        $categoryProductForm->load(Yii::$app->request->post());

        if(!$categoryProductForm->validate()){
            return $categoryProductForm->getErrors();
        }

        $categoryProductForm->save();
        
        return [
            'status' => true,
            'data' => [
                'now' => date('d/m/Y'),
                'user' => $categoryProductForm
            ],
            'message' => 'Create user success'
        ];
    }
}