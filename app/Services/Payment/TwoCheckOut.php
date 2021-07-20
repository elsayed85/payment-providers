<?php

namespace App\Services\Payment;

class TwoCheckOut
{
    public static function createSale(array $requestData)
    {
        $host = "https://www.2checkout.com/checkout/api/1/" . config('payment.2checkout.merchantCode') . "/rs/authService";
        $requestData['sellerId'] = config('payment.2checkout.merchantCode');
        $requestData['privateKey'] = config('payment.2checkout.privateKey');
        $requestData['demo'] = config('payment.2checkout.demo');
        $payload = json_encode($requestData);
        $ch = curl_init();
        $headerArray = array(
            "Content-Type: application/json",
            "Accept: application/json",
        );
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSLVERSION, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        $response = curl_exec($ch);
        if ($response === false) {
            echo curl_error($ch);
        }
        curl_close($ch);
        return json_decode($response);
    }
}
