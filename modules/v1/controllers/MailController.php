<?php

namespace app\modules\v1\controllers;

use app\controllers\Controller;
use app\modules\v1\jobs\TestQueue;
use Yii;

class MailController extends Controller
{
    public function actionSendmail()
    {
        Yii::$app->queue->push(new TestQueue('abc'));
    }
}