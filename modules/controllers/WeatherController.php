<?php

namespace app\modules\controllers;

use Yii;
use app\controllers\Controller;

class WeatherController extends Controller
{
    public function actionIndex()
    {
        $city = Yii::$app->request->get('city');
        $weather = Yii::$app->weather->getWeather($city);
        return $this->json(true, ['weather' => $weather], '');
    }
}