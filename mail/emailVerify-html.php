<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $verifyLink string */

?>
<p>Hello <?= Html::encode($user->username) ?>,</p>
<p>Follow the link below to verify your email:</p>
<p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>