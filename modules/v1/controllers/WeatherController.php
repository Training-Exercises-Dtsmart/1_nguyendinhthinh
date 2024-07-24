<?php

namespace app\modules\v1\controllers;

use app\controllers\Controller;
use Yii;

class WeatherController extends Controller
{
    public function actionIndex($city)
    {
//        $city = Yii::$app->request->get('city');
        $weather = Yii::$app->weather->getWeather($city);
        return $this->json(true, ['weather' => $weather], '');
    }
}