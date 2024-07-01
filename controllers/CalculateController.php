<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class CalculateController extends Controller{

    public function actionTotal(){
        if(!Yii::$app->request->isPost){
            throw new NotFoundHttpException('The requested resource was not found.',404);
        }
        $data = Yii::$app->request->post();
        $a = $data['a'];
        $b = $data['b'];

        if(is_numeric($a) == false or is_numeric($b) == false){
            return [
                "status" => false,
                "data" => [
                    "now" => date('d/m/Y'),
                ],
                "message" => 'a or b is not a number'
            ];
        }

        $result = $a + $b;
        return [
            "status" => true,
            "data" => [
                "now" => date('d/m/Y'),
                "result" => $result,
            ],
            "message" => 'Success'
        ];
    }

    public function actionDivide(){
        if(!Yii::$app->request->isPost){
            throw new NotFoundHttpException("The request resource was not found", 404);
        }

        $data = Yii::$app->request->post();
        $a = $data['a'];
        $b = $data['b'];

        if(is_numeric($a) == false or is_numeric($b) == false){
            return [
                "status" => false,
                "data" => [
                    "now" => date('d/m/Y')
                ],
                "message" => "a or b is not a number"
            ];
        }
        if($b == 0){
            return [
                "status" => false,
                "data" => [
                    "now" => date('d/m/Y')
                ],
                "message" => "Number b have to different zero"
            ];
        }

        $result = $a / $b;
        return [
            "status" => true,
            "data" => [
                "now" => date('d/m/Y'),
                "result" => $result,
            ],
            "message" => 'Success'
        ];
    }

    public function actionAverage(){
        if(!Yii::$app->request->isPost){
            throw new NotFoundHttpException("The request resource was not found", 404);
        }

        $request = Yii::$app->request;
        $quantity = count($request->post());
        $arrayNumber = [];

        #input name="number_i"
        for($i = 1; $i <= $quantity; $i++){
            $number = $request->post('number_'. $i);
            if(is_numeric($number) == false){
                return [
                    "status" => false,
                    "data" => [
                        "now" => date('d/m/Y')
                    ],
                    "message" => "Position ".$i." is not a number"
                ];
            }
            array_push($arrayNumber, $number);
        }

        $sum = array_sum($arrayNumber);
        $result = round($sum/$quantity,1);
        return [
            "status" => true,
            "data" => [
                "now" => date('d/m/Y'),
                "result" => $result
            ],
            "message" => "Success"
        ];
    }
}