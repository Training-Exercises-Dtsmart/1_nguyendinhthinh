<?php

namespace app\modules\controllers;

use Yii;
use app\controllers\Controller;
use app\modules\jobs\TestQueue;

class MailController extends Controller
{
    public function actionSendmail()
    {
        Yii::$app->queue->push(new TestQueue('abc'));
    }
}