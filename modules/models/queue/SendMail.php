<?php

namespace app\modules\models\queue;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SendMail extends BaseObject implements JobInterface
{
    public $email;
    /**
     * @var mixed
     */
    private $content;
    /**
     * @var mixed
     */
    private $subject;

    public function execute($queue)
    {
        try {
            Yii::$app->mailer->compose()
                ->setFrom('kissuo6@gmail.com')
                ->setTo($this->email)
                ->setSubject($this->subject)
                ->setTextBody($this->content)
                ->send();

            Yii::info('Đã gửi email cho' . $this->email . ' thành công.', 'queue');
        } catch (\Exception $e) {
            Yii::error('Lỗi khi gửi email cho ' . $this->email . ': ' . $e->getMessage(), 'queue');
        }
    }
}