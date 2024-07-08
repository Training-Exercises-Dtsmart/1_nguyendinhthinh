<?php

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\controllers\Controller;

class CalculateController extends Controller
{

    public function actionTotal()
    {
        $a = Yii::$app->request->post('a');
        $b = Yii::$app->request->post('b');

        if (!is_numeric($a) or !is_numeric($b)) {
            //bad request, number invalid => status code = 400
            return $this->json(false, ["now" => date('d/m/y')], 'a or b is not a number', 400 );
        }

        $result = $a + $b;
        return $this->json(true, ["now" => date('d/m/y'), 'result' => $result], 'Success');
    }

    public function actionDivide()
    {
        $a = Yii::$app->request->post('a');
        $b = Yii::$app->request->post('b');

        //bad request, number invalid => status code = 400
        if (!is_numeric($a) or !is_numeric($b)) {
            return $this->json(false, ["now" => date('d/m/Y')], 'a or b is not a number', 400);
        }
        if ($b == 0) {
            return $this->json(false, ["now" => date('d/m/Y')], "Number b can't be zero", 400);
        }

        $result = $a / $b;
        return $this->json(true, ["now" => date('d/m/y'), 'result' => $result], 'Success');
    }

    public function actionAverage()
    {
        $numbers = Yii::$app->request->post('numbers');
        $arrayNumber = explode(',', $numbers);

        //bad request, number invalid => status code = 400
        foreach ($arrayNumber as $number) {
            if (!is_numeric($number)) {
                return $this->json(false, ["now" => date('d/m/Y')], 'Number is not a number', 400);
            }
        }

        $result = round(array_sum($arrayNumber) / count($arrayNumber), 2);
        return $this->json(true, ["now" => date('d/m/y'), 'result' => $result], 'Success');
    }
}