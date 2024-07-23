<?php

namespace app\modules\v1\models\form;

use app\modules\HttpStatus;
use app\modules\v1\jobs\VerifyMailQueue;
use app\modules\v1\models\User;
use Yii;

class RegisterForm extends User
{
    const EMAIL_VERIFY_PENDING = 0;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['username', 'trim'],
            [['username', 'password', 'email'], 'required'],
            ['email', 'unique'],
            ['email', 'email'],
        ]);
    }

    public function register()
    {
        if ($this->validate()) {
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $this->generateVerificationToken();
            $this->email_verified = RegisterForm::EMAIL_VERIFY_PENDING;

            if ($this->save()) {
                $this->sendVerificationEmail();

                $auth = Yii::$app->authManager;
                $author = $auth->getRole('author');
                $auth->assign($author, $this->id);
                return true;
            }
            return false;
        }
        return false;
    }

    public function sendVerificationEmail()
    {
//        $verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['/api/v1/auth/verify-email', 'token' => $registerForm->verification_token]);
//        Yii::$app->queue->push(new VerifyMailQueue($registerForm->username, $registerForm->email, $verifyLink));
        try {
            $verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['/api/v1/auth/verify-email', 'token' => $this->verification_token]);
            Yii::$app->queue->push(new VerifyMailQueue($this->username, $this->email, $verifyLink));

        } catch (\Exception $e) {
            Yii::error('Error sending verification email: ' . $e->getMessage(), __METHOD__);
        }
    }
}