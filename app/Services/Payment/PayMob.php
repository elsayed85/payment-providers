<?php

namespace App\Services\Payment;

use App\Exceptions\Payments\PayMob\AuthFailed;
use App\Exceptions\Payments\PayMob\InvalidCredentials;
use App\Exceptions\Payments\PayMob\OrderFailed;
use App\Exceptions\Payments\PayMob\PasswordNotExist;
use App\Exceptions\Payments\PayMob\PaymentFailed;
use App\Exceptions\Payments\PayMob\UserNameNotExist;

class PayMob
{
    const API_AUTH_TOKEN =  'https://accept.paymobsolutions.com/api/auth/tokens';
    const API_ORDERS = 'https://accept.paymobsolutions.com/api/ecommerce/orders';
    const API_PAYMENT_KEYS = 'https://accept.paymobsolutions.com/api/acceptance/payment_keys';

    public static function auth()
    {
        $user = [
            'username' => config('payment.paymob.username'),
            'password' => config('payment.paymob.password'),
        ];

        $data = json_encode($user);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_AUTH_TOKEN);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch);
        if ($response === false) {
            echo curl_error($ch);
        }
        $reponse =  json_decode($response);

        if (!isset($reponse->token)) {
            if (isset($reponse->username)) {
                throw new InvalidCredentials("username : " . $reponse->username[0]);
            } elseif (isset($reponse->password)) {
                throw new InvalidCredentials("password : " . $reponse->password[0]);
            } elseif (isset($reponse->detail)) {
                throw new AuthFailed($reponse->detail);
            } else {
                throw new AuthFailed("Auth Failed");
            }
        }

        return $reponse;
    }

    public static function addOrder(array $requestData)
    {
        $postData = json_encode($requestData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_ORDERS);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $response = curl_exec($ch);
        if ($response === false) {
            echo curl_error($ch);
        }
        curl_close($ch);
        $reponse =  json_decode($response);

        if (!isset($reponse->id)) {
            if (isset($reponse->message)) {
                throw new OrderFailed($reponse->message);
            } else {
                throw new OrderFailed("Order Failed");
            }
        }

        return $reponse;
    }

    public static function paymentKey($requestData)
    {
        $requestData['expiration'] = 3600;
        $requestData['integration_id'] = config('payment.paymob.integration_id');
        $postData = json_encode($requestData);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::API_PAYMENT_KEYS);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $response = curl_exec($ch);
        if ($response === false) {
            echo curl_error($ch);
        }
        curl_close($ch);
        $reponse =  json_decode($response);

        if (!isset($reponse->token)) {
            if (isset($reponse->message)) {
                throw new PaymentFailed($reponse->message);
            } else {
                throw new PaymentFailed("Payment Failed");
            }
        }

        return $reponse;
    }
}
