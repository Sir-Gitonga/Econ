<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    private $base_url;
    private $consumer_key;
    private $consumer_secret;
    private $shortcode;
    private $passkey;
    private $callback_url;

    public function __construct()
    {
        $this->base_url = config('mpesa.env') === 'live' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';
        $this->consumer_key = config('mpesa.consumer_key');
        $this->consumer_secret = config('mpesa.consumer_secret');
        $this->shortcode = config('mpesa.shortcode');
        $this->passkey = config('mpesa.passkey');
        $this->callback_url = config('mpesa.callback_url');
    }

    private function generateAccessToken()
    {
        $response = Http::withBasicAuth($this->consumer_key, $this->consumer_secret)
            ->get($this->base_url . '/oauth/v1/generate?grant_type=client_credentials');

        return $response['access_token'] ?? null;
    }

    public function stkPush($phone, $amount, $reference, $description)
    {
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $access_token = $this->generateAccessToken();

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->callback_url,
            'AccountReference' => $reference,
            'TransactionDesc' => $description,
        ];

        $response = Http::withToken($access_token)
            ->post($this->base_url . '/mpesa/stkpush/v1/processrequest', $payload);

        return $response->json();
    }
}
