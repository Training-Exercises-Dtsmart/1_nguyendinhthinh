<?php

namespace app\controllers;

use yii\rest\Controller as BaseController;
use Yii;

class Controller extends BaseController{
    public function json($status=true, $data=[], $message="", $code=200)
    {
        Yii::$app->response->statusCode = $code;

        return [
            "status" => $status,
            "data" => $data,
            "message" => $message,
            "code" => $code
        ];
    }
}