<?php

namespace app\modules\v1\jobs;

use Yii;

class VerifyMailQueue implements \yii\queue\JobInterface
{
    public $username;
    public $email;
    public $verify_link;

    public function __construct($username, $email, $verify_link)
    {
        $this->username = $username;
        $this->email = $email;
        $this->verify_link = $verify_link;
    }

    public function execute($queue)
    {
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                    ['user' => $this->username, 'verifyLink' => $this->verify_link]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($this->email)
                ->setSubject('Email verification for ' . Yii::$app->name)
                ->send();
            Yii::info('Đã gửi email cho' . $this->email . ' thành công.', 'queue');
        } catch (\Exception $e) {
            Yii::error('Lỗi khi gửi email cho ' . $this->email . ': ' . $e->getMessage(), 'queue');
        }
    }
}