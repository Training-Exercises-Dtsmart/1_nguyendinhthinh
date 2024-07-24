<?php

namespace app\modules\v1\jobs;

use Yii;
use yii\queue\JobInterface;

class SendForgotPasswordJob implements JobInterface
{
    public $username;
    public $email;
    public $verificationLink;

    public function __construct($username, $email, $verificationLink)
    {
        $this->username = $username;
        $this->email = $email;
        $this->verificationLink = $verificationLink;
    }

    public function execute($queue)
    {
        try {
            Yii::$app->mailer->compose(
                ['html' => 'resetPasswordToken'],
                ['username' => $this->username, 'resetLink' => $this->verificationLink])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($this->email)
                ->setSubject('Email restart password')
                ->send();
            Yii::info('Đã gửi email cho' . $this->email . ' thành công.', 'queue');
        } catch (\Exception $e) {
            Yii::error('Lỗi khi gửi email cho ' . $this->email . ': ' . $e->getMessage(), 'queue');
        }
    }
}