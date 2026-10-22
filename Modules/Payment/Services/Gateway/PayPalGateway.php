<?php

namespace Modules\Payment\Services\Gateways;

use Modules\Payment\Services\Contract\PaymentGatewayInterface;
use Modules\Payment\Services\Gateway\GatewayResponse;

/**
 * محاكاة بوابة PayPal.
 * - بدون email => فشل.
 * - email يبدأ بـ "fail" => رفض (لتجربة حالة الفشل).
 */
class PayPalGateway implements PaymentGatewayInterface
{
    public function checkStatus($transactionId): GatewayResponse
    {
        $merchantRefNumber = $transactionId;
        $merchantCode = config('fawry.gateway.merchant_code');
        $merchant_sec_key = config('fawry.gateway.security_key');
        $signature = hash('sha256', $merchantCode.$merchantRefNumber.$merchant_sec_key);
        $httpClient = new \GuzzleHttp\Client;
        try {
            $response = $httpClient->request('GET', 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/payments/status/v2', [
                'query' => [
                    'merchantCode' => $merchantCode,
                    'merchantRefNumber' => $merchantRefNumber,
                    'signature' => $signature,
                ],
            ]);

            $response = json_decode($response->getBody()->getContents(), true);

            if ($response['orderStatus'] != 'PAID') {
                return GatewayResponse::failure(
                    'Payment not completed. Status: '.$response['orderStatus'],
                    $response,
                );
            }

            return GatewayResponse::success(
                $merchantRefNumber,
                'PayPal payment completed.',
                $response,
            );

        } catch (\Exception $e) {
            return GatewayResponse::failure(
                'Error occurred while checking payment status: '.$e->getMessage(),
            );
        }
    }
}
