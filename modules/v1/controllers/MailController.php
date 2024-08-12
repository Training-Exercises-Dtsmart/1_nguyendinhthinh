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
     */
    public function actionCheck(): array
    {
//        $mailbox = new Mailbox(
//            env('MAIL_BOX'),
//            env('MAIL_BOX_USERNAME'),
//            env('MAIL_BOX_PASSWORD'),
//        );
        $mailbox = imap_open(env('MAIL_BOX'), env('MAIL_BOX_USERNAME'), env('MAIL_BOX_PASSWORD'));

        if (!$mailbox) {
            return $this->json(false, [], 'Fail to connect mail', HttpStatus::BAD_REQUEST);
        }

        $search_criteria = 'FROM "' . env('MAIL_ACB_ALERT') . '" Subject "ACB-Dich vu bao so du tu dong"';
//        $emails = $mailbox->searchMailbox($search_criteria);
        $emails = imap_search($mailbox, $search_criteria);

        if (!$emails) {
            return $this->json(false, [], 'Fail to get mail', HttpStatus::NOT_FOUND);
        }

        $emailData = [];

        rsort($emails);

        foreach ($emails as $email_number) {
//            $overview = imap_fetch_overview($mailbox, $email_number, 0);
//            var_dump($email_number);
//            $body = imap_num_msg($mailbox);
//            $body = imap_uid($mailbox, $email_number);
//            $body = imap_body($mailbox, 2739, 1);
//            foreach ($overview as $email) {
//                echo "Subject: {$email->subject}\n";
//                echo "From: {$email->from}\n";
//                echo "Date: {$email->date}\n";
//            }
//            $body = imap_bodystruct($mailbox, $email_number, 1);
            $body = imap_fetchbody($mailbox, $email_number, 1, FT_INTERNAL);
//            $body = imap_fetchmime($mailbox, $email_number, 1);
//            $body = imap_fetchstructure($mailbox, $email_number, 1);
//            $body = imap_fetchtext($mailbox, $email_number, 1);
//            $body = imap_thread($mailbox, $email_number);
//            $body = imap_uid($mailbox, $email_number);
//            $body = imap_msgno($mailbox, 2739);
//            $body = imap_mailboxmsginfo($mailbox);
//            $message = imap_base64($body);
//            $message = quoted_printable_decode($body);
//            $message = strip_tags($body);
//            var_dump($body);
//            die;
            $body = html_entity_decode($body);
            $body = strip_tags($body);
            $body = str_replace(array("\n", "\r"), '', $body);
            $body = str_replace('=', '', $body);
            $emailData[] = $this->parseEmailContent($body);
//            $email = $mailbox->getMail($email_number);
//            $body = $email->textHtml;
//
//            $content = strip_tags($body);
//            $content = html_entity_decode($content);
//
//            $parsedData = $this->parseEmailContent($content);
//            $emailData[] = [
//                'account' => $parsedData['account'],
//                'transaction' => $parsedData['transaction'],
//                'description' => $parsedData['description'],
//            ];
        }
        if ($emailData) {
            return $this->json(true, ['mails' => $emailData], 'Successfully get mail', HttpStatus::OK);
        }
        return $this->json(false, [], 'Unknown error', HttpStatus::BAD_REQUEST);
    }

//    public function parseEmailContent($content)
//    {
//        preg_match('/tài khoản *(\d+)/i', $content, $accountMatches);
//        preg_match('/Giao dịch mới nhất:(Ghi nợ|Ghi có)\s*([+-]?[\d,]+\.\d+\s*VND)/i', $content, $transactionMatches);
//        preg_match('/Nội\sdung\sgiao\sdịch:\s*([^\d]+)\s*(\d{6,})/im', $content, $descriptionMatches);
//        $description = preg_replace('/[-]+/', '', $descriptionMatches[1]);
//        return [
//            'account' => $accountMatches[1] ?? 'Not Found',
//            'transaction' => $transactionMatches[2] ?? 'Not Found',
//            'description' => $description ?? 'Not Found',
//        ];
//    }

    public function parseEmailContent($content): array
    {
        preg_match('/updates your (?<account>\d+) account balance/i', $content, $accountMatches);
        preg_match('/Latest transaction:\s*(?<type>Debit|Credit)\s*(?<amount>[+-]?[\d,]+\.\d+)\s*VND/i', $content, $transactionMatches);
        if ($transactionMatches['type'] == "Debit") {
            preg_match('/Content:\s*(?<description>[^0-9]+)\s*(\d{6,})/im', $content, $descriptionMatches);
        }
        if ($transactionMatches['type'] == "Credit") {
            preg_match('/Content:\s*(?<description>.*?)\sGD/im', $content, $descriptionMatches);
        }

        $description = isset($descriptionMatches['description']) ? preg_replace('/-+/', '', $descriptionMatches[1]) : 'Not Found';
        return [
            'account' => $accountMatches['account'] ?? 'Not Found',
            'transaction' => $transactionMatches['amount'] ?? 'Not Found',
            'description' => $description ?? 'Not Found',
        ];
    }
}