<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $username string */
/* @var $resetLink string */

?>
<p>Hello <?= Html::encode($username) ?>,</p>

<p>Follow the link below to reset your password:</p>

<p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
