<?php

namespace Modules\Otp\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Otp\Contracts\OtpContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class OrangeService implements OtpContract
{
    public $url;
    public $dev_phone;
    public $token;
    public $client_secret;
    public $client_id;
    public $application_id;
    public function __construct()
    {
        $this->url = 'https://api.orange.com';
        $this->dev_phone = config('app.dev_phone');
        $this->token = config('app.sms_token');
        $this->client_secret = config('app.client_secret');
        $this->client_id = config('app.client_id');
        $this->application_id = config('app.application_id');
    }
    private function authentication()
    {
        $sms =  Http::asForm()->withHeaders([
            'Authorization' => $this->token,
        ])->post($this->url . '/oauth/v3/token', [
            'grant_type' => 'client_credentials'
        ]);
        return json_decode($sms)?->access_token ?? null;
    }

    public function send($phone, $message): bool
    {
        $auth_key = $this->authentication();
        $data = [
            "outboundSMSMessageRequest" => [
                "address" => "tel:+" . $phone,
                "senderAddress" => "tel:+" . $this->dev_phone,
                "outboundSMSTextMessage" => [
                    "message" => $message
                ]
            ]
        ];
        $sms =  Http::withHeaders([
            'Authorization' => 'Bearer ' . $auth_key,
        ])->post($this->url . '/smsmessaging/v1/outbound/tel:+' . $this->dev_phone . '/requests', $data);
        return true;
    }
}
