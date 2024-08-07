<?php

namespace app\modules\v1\controllers;

use app\modules\HttpStatus;
use PhpImap\Exceptions\InvalidParameterException;
use PhpImap\Mailbox;
use Yii;
use app\controllers\Controller;
use app\modules\v1\jobs\TestQueue;
use Google\Client;
use Google\Service\Gmail;
use yii\filters\auth\HttpBearerAuth;

class MailController extends Controller
{
    /**
     * @throws InvalidParameterException
     */
    public function actionCheck()
    {
        $mailbox = new Mailbox(
            env('MAIL_BOX'),
            env('MAIL_BOX_USERNAME'),
            env('MAIL_BOX_PASSWORD'),
        );
        if (!$mailbox) {
            return $this->json(false, [], 'Fail to connect mail', HttpStatus::BAD_REQUEST);
        }

        $search_criteria = 'FROM "' . env('MAIL_ACB_ALERT') . '" Subject "ACB-Dich vu bao so du tu dong"';
        $emails = $mailbox->searchMailbox($search_criteria);

        if (!$emails) {
            return $this->json(false, [], 'Fail to get mail', HttpStatus::NOT_FOUND);
        }

        $emailData = [];

        $emails = array_reverse($emails);

        foreach ($emails as $email_number) {
            $email = $mailbox->getMail($email_number);
            $body = $email->textHtml;

            $content = strip_tags($body);
            $content = html_entity_decode($content);

            $parsedData = $this->parseEmailContent($content);

            $emailData[] = [
                'account' => $parsedData['account'],
                'transaction' => $parsedData['transaction'],
                'description' => $parsedData['description'],
            ];
        }
        if ($emailData) {
            return $this->json(true, ['mails' => $emailData], 'Successfully get mail', HttpStatus::OK);
        }
        return $this->json(false, [], 'Unknown error', HttpStatus::BAD_REQUEST);
    }

    public function parseEmailContent($content)
    {
        preg_match('/tài khoản *(\d+)/i', $content, $accountMatches);
        preg_match('/Giao dịch mới nhất:(Ghi nợ|Ghi có)\s*([+-]?[\d,]+\.\d+\s*VND)/i', $content, $transactionMatches);
        preg_match('/Nội\sdung\sgiao\sdịch:\s*([^\d]+)\s*(\d{6,})/im', $content, $descriptionMatches);
        $description = preg_replace('/[-]+/', '', $descriptionMatches[1]);
        return [
            'account' => $accountMatches[1] ?? 'Not Found',
            'transaction' => $transactionMatches[2] ?? 'Not Found',
            'description' => $description ?? 'Not Found',
        ];
    }
}