<?php

namespace app\controllers;

use yii\rest\Controller as BaseController;

class Controller extends BaseController{
    public function json($status=true, $data=[], $message="", $code=200){
        return [
            "status" => $status,
            "data" => $data,
            "message" => $message,
            "code" => $code
        ];
    }
}