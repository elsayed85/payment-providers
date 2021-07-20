<?php

namespace App\Services\Payment;

class Vapulus
{
    private $baseUrl;
    private $successUrl;
    private $failUrl;

    public function __construct()
    {
        $this->baseUrl = config('payment.vapulus.base_url');
        $this->successUrl = config('payment.vapulus.success_url');
        $this->failUrl = config('payment.vapulus.fail_url');
    }

    public function generateHash($postData)
    {
        ksort($postData);
        $message = "";
        $appendAmp = 0;
        foreach ($postData as $key => $value) {
            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $message .= $key . '=' . $value;
                    $appendAmp = 1;
                } else {
                    $message .= '&' . $key . "=" . $value;
                }
            }
        }

        $secret = pack('H*', config('payment.vapulus.hash'));
        return hash_hmac('sha256', $message, $secret);
    }

    function HTTPPost($url, array $params)
    {
        $url = $this->baseUrl . $url;

        $params['hashSecret'] = $this->generateHash($params);
        $params['appId'] = config('payment.vapulus.app_id');
        $params['password'] = config('payment.vapulus.password');

        $query = http_build_query($params);
        $ch    = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $response = curl_exec($ch);
        curl_close($ch);

        $resp = json_decode($response);
        return $resp;
    }

    public function userInfo($userId)
    {
        return $this->HTTPPost('userInfo', ['userId' => $userId]);
    }

    // CARDS
    public function addCard(array $data)
    {
        return $this->HTTPPost('makeTransaction', $data);
    }

    public function cardInfo($userId, $cardId)
    {
        return $this->HTTPPost('cardInfo', ['userId' => $userId, 'cardId' => $cardId]);
    }

    // OTB
    public function validateOTP(array $data)
    {
        return $this->HTTPPost('validateOTP', $data);
    }

    public function resendCode(array $data)
    {
        return $this->HTTPPost('resendCode', $data);
    }

    // PAYMENT
    public function makePayment(array $data)
    {
        return $this->HTTPPost('makePayment', $data);
    }

    // TRANSACTIONS
    public function makeTransaction(array $data, $amount)
    {
        $data['onAccept'] = $this->successUrl;
        $data['onFail'] = $this->failUrl;
        $data['amount'] = $amount;

        return $this->HTTPPost('makeTransaction', $data);
    }

    public function transactionsList($userId, array $data)
    {
        return $this->HTTPPost('transactions/list', array_merge(['userId' => $userId], $data));
    }

    public function transactionInfo($transactionId)
    {
        return $this->HTTPPost('transactionInfo', ['transactionId' => $transactionId]);
    }

    public function transactionStatus($transactionId, $merchantId)
    {
        return $this->HTTPPost('transaction/status', ['transactionId' => $transactionId, 'merchantId' => $merchantId]);
    }
}
