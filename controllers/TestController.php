<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;

class TestController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}