<?php

namespace app\modules\controllers;

use app\controllers\Controller;
use app\modules\models\User;
use Yii;
use app\modules\models\queue\SendMailJob;

class SiteController extends Controller
{
    public function actionVerifyEmail($token)
    {
        try {
            $user = User::findByVerificationToken($token);
            if (!$user) {
                return $this->json(false, [], 'Verification token is invalid or expired. Please request again.');
            }

            $user->verifyEmail();
            $user->save(false);

            return $this->json(true, [], 'Your email has been confirmed!');
//            Yii::$app->session->setFlash('success', 'Your email has been successfully verified.');
//            return $this->goHome();
        } catch (\RuntimeException $e) {
            return $this->json(false, [], $e->getMessage());
//            Yii::$app->session->setFlash('error', $e->getMessage());
//            return $this->goHome();
        }
    }


    public function actionSendmail()
    {
        Yii::$app->mailer->compose()
            ->setFrom('no-reply@domain.com')
            ->setTo('daominhhung2203@gmail.com')
            ->setSubject('Xin chào')
            ->setTextBody('Hello')
            ->setHtmlBody('<b>HTML content</b>')
            ->send();
    }

    public function actionSendmailqueue()
    {
        $job = new SendMailJob();
        Yii::$app->queue->push($job);

        return ['status' => 'success', 'message' => 'Đã đưa công việc gửi email vào hàng đợi.'];

    }
}