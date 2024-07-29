<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;

class ZaloPayComponent extends Component
{
    public $appId;
    public $key1;
    public $key2;
    public $endpoint;

    public function createOrder($params)
    {
        $client = new Client();
        $order = [
            'app_id' => (integer)$this->appId,
            "app_time" => round(microtime(true) * 1000),
            "app_trans_id" => date("ymd") . "_" . $params['orderId'],
            "app_user" => Yii::$app->user->identity->username,
            "item" => '[]',
            "embed_data" => '{}',
            "amount" => $params['amount'],
            "description" => $params['description'],
            "bank_code" => "zalopayapp"
        ];

        $data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"] . "|" . $order["app_time"] . "|" . $order["embed_data"] . "|" . $order["item"];

        $order["mac"] = hash_hmac("sha256", $data, $this->key1);
        $response = $client->post("{$this->endpoint}/create", $order)->send();
        if ($response->isOk) {
            return $response->data;
        } else {
            return null;
        }
    }

    public function queryOrder($app_trans_id)
    {
        $client = new Client();
        $data = $this->appId . "|" . $app_trans_id . "|" . $this->key1;
        $params = [
            "app_id" => $this->appId,
            "app_trans_id" => $app_trans_id,
            "mac" => hash_hmac("sha256", $data, $this->key1)
        ];

        $response = $client->post("{$this->endpoint}/query", $params)->send();
        if ($response->isOk) {
            return $response->data;
        }
        return null;
    }

    public function refundOrder($zp_trans_id, $amount)
    {
        $client = new Client();
        $timestamp = round(microtime(true) * 1000); // miliseconds
        $uid = "$timestamp" . rand(111, 999); // unique id

        $params = [
            "app_id" => $this->appId,
            "m_refund_id" => date("ymd") . "_" . $this->appId . "_" . $uid,
            "timestamp" => $timestamp,
            "zp_trans_id" => $zp_trans_id,
            "amount" => (integer)$amount,
            "description" => "ZaloPay Intergration Demo"
        ];

        // app_id|zp_trans_id|amount|description|timestamp
        $data = $params["app_id"] . "|" . $params["zp_trans_id"] . "|" . $params["amount"]
            . "|" . $params["description"] . "|" . $params["timestamp"];
        $params["mac"] = hash_hmac("sha256", $data, $this->key1);
        var_dump($params);
        $response = $client->post("{$this->endpoint}/refund", $params)->send();

        if ($response->isOk) {
            return $response->data;
        }
        return null;
    }

    public function queryRefund($m_refund_id)
    {
        $client = new Client();
        $timestamp = round(microtime(true) * 1000); // miliseconds
        $data = $this->appId . "|" . $m_refund_id . "|" . $timestamp; // app_id|m_refund_id|timestamp
        $params = [
            "app_id" => $this->appId,
            "timestamp" => $timestamp,
            "m_refund_id" => $m_refund_id,
            "mac" => hash_hmac("sha256", $data, $this->key1)
        ];


        $response = $client->post("{$this->endpoint}/query_refund", $params)->send();
        if ($response->isOk) {
            return $response->data;
        }
        return null;
    }
}