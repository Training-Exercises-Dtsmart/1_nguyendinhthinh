<?php

namespace app\commands;

use yii\base\Model;
use yii\console\Controller;
use Yii;

class MycronController extends Controller
{
    public function actionIndex()
    {
        Yii::warning("Nguyen Dinh Thinh");
    }

}