<?php

namespace app\modules\v1\models\form;

use app\modules\v1\jobs\SendForgotPasswordJob;
use app\modules\v1\models\User;
use Yii;
use yii\db\Exception;

class PasswordResetRequestForm extends User
{
    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function sendMailResetPassword()
    {
        $this->reset_password_token = Yii::$app->security->generateRandomString() . '_' . time();
        if ($this->save()) {
            $verificationLink = Yii::$app->urlManager->createAbsoluteUrl([
                'api/v1/auth/reset-password',
                'token' => $this->reset_password_token
            ]);
            Yii::$app->queue->push(new SendForgotPasswordJob($this->username, $this->email, $verificationLink));
            return true;
        }
        return false;
    }
}