<?php

namespace app\components;

use app\models\Order;
use Yii;
use yii\base\Component;
use yii\httpclient\Client;

class ZaloPayComponent extends Component
{
    public $appId;
    public $key1;
    public $key2;
    public $endpoint;

    public function callBack($params)
    {
        $result = [];
        if (empty($params)) {
            $result["return_code"] = -1;
            $result["return_message"] = "No data received";
            return $result;
        }

        if (!isset($params["data"]) || !isset($params["mac"])) {
            $result["return_code"] = -1;
            $result["return_message"] = "Missing data or mac";
            return $result;
        }

        $data = $params["data"];

        $mac = hash_hmac("sha256", $data, $this->key2);
        $requestmac = $params["mac"];

        Yii::info('Mac:' . $mac);
        Yii::info('Request mac:' . $requestmac);

        if (strcmp($mac, $requestmac) != 0) {
            $result["return_code"] = -1;
            $result["return_message"] = "MAC not equal";
            return $result;
        }

        $datajson = json_decode($data, true);
        $app_trans_id = $datajson["app_trans_id"];
        $order = Order::find()->where(['app_trans_id' => $app_trans_id])->one();
        if (!$order) {
            $result["return_code"] = -1;
            $result["return_message"] = "Order not found";
            return $result;
        }

        $order->payment_status = Order::PAYMENT_STATUS_PAID;
        $order->zp_trans_id = $datajson["zp_trans_id"];
        $order->save(false);
        $result["return_code"] = 1;
        $result["return_message"] = "Success";
        return $result;
    }

    public function createOrder($params)
    {
        $app_trans_id = date("ymd") . "_" . $params['orderId'];
        $app_time = round(microtime(true) * 1000);

        $client = new Client();
        $order = [
            'app_id' => (integer)$this->appId,
            "app_time" => $app_time,
            "app_trans_id" => $app_trans_id,
            "app_user" => Yii::$app->user->identity->username,
            "item" => '[]',
            "embed_data" => '{}',
            "callback_url" => env("URL_ORDER_CALL_BACK"),
            "amount" => $params['amount'],
            "description" => $params['description'],
            "bank_code" => ""
        ];

        $data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"] . "|" . $order["app_time"] . "|" . $order["embed_data"] . "|" . $order["item"];

        $order["mac"] = hash_hmac("sha256", $data, $this->key1);
        $response = $client->post("{$this->endpoint}/create", $order)->send();
        if ($response->isOk) {
            $order = Order::find()->where(['id' => $params['orderId']])->one();
            $order->app_trans_id = $app_trans_id;
            $order->save(false);
            return $response->data;
        }
        return null;
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