<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\EntryForm;
use yii\web\NotFoundHttpException;

class UserController extends Controller{
    private $username = 'dinhthinh';
    private $password = 'e10adc3949ba59abbe56e057f20f883e'; #123456

    public function actionIndex(){
        return "User Page";
    }

    public function actionLogin(){
        if(Yii::$app->request->method != "POST"){
            throw new NotFoundHttpException('The requested resource was not found.',404);
        }
        $data = Yii::$app->request->post();
        $username = $data['username'];
        $password = $data['password'];

        if($username != $this->username or md5($password) != $this->password){
            return [
                "status" => false,
                "data" => [
                    "now" => date("d/m/Y")
                ],
                "message" => "Username or password wrong"
            ];
        }

        return [
            "status" => true,
            "data" => [
                "now" => date("d/m/Y")
            ],
            "message" => "Success"
        ];
    }
}

