<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var ActiveForm $form */
?>
<div class="Register">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'username')?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'fullname') ?>
        <?= $form->field($model, 'telephone') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- Register -->
