<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $username string */
/* @var $verifyLink string */

?>
<p>Hello <?= Html::encode($username) ?>,</p>
<p>Follow the link below to verify your email:</p>
<p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>