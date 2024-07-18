<?php

namespace app\modules\jobs;

use Yii;

class VerifyMailQueue extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $user;
    public $verify_link;

    public function __construct($user, $verify_link)
    {
        $this->user = $user;
        $this->verify_link = $verify_link;
    }

    public function execute($queue)
    {
        try {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                    ['user' => $this->user, 'verifyLink' => $this->verify_link]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($this->user->email)
                ->setSubject('Email verification for ' . Yii::$app->name)
                ->send();
            Yii::info('Đã gửi email cho' . $this->user->email . ' thành công.', 'queue');
        } catch (\Exception $e) {
            Yii::error('Lỗi khi gửi email cho ' . $this->user->email . ': ' . $e->getMessage(), 'queue');
        }
    }
}