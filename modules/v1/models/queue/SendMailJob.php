<?php

namespace app\modules\v1\models\queue;

use Yii;
use yii\queue\JobInterface;

class SendMailJob implements JobInterface
{
    public function execute($queue)
    {
        $emails = Yii::$app->db->createCommand('SELECT email FROM user')->queryColumn();
        foreach ($emails as $email) {
            $job = new SendMail([
                'email' => $email,
            ]);
            Yii::$app->queue->push($job);
            Yii::info('Đã đưa công việc gửi email cho ' . $email . ' vào hàng đợi.', 'queue');
        }
    }
}