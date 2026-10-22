<?php

namespace Tests\Unit;

use InvalidArgumentException;
use Modules\Order\Models\Order;
use Modules\Payment\Enum\PaymentStatusEnum;
use Modules\Payment\Gateways\Contracts\PaymentGatewayInterface;
use Modules\Payment\Gateways\CreditCardGateway;
use Modules\Payment\Gateways\PaymentGatewayManager;
use Modules\Payment\Gateways\PaymobGateway;
use Modules\Payment\Gateways\PaypalGateway;
use Tests\TestCase;

class PaymentGatewayStrategyTest extends TestCase
{
    private PaymentGatewayManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new PaymentGatewayManager;
    }

    public function test_manager_resolves_supported_gateways(): void
    {
        $paypal = $this->manager->getGateway('paypal');
        $this->assertInstanceOf(PaymentGatewayInterface::class, $paypal);
        $this->assertInstanceOf(PaypalGateway::class, $paypal);
        $this->assertEquals('paypal', $paypal->getIdentifier());

        $cc = $this->manager->getGateway('credit_card');
        $this->assertInstanceOf(CreditCardGateway::class, $cc);
        $this->assertEquals('credit_card', $cc->getIdentifier());

        $paymob = $this->manager->getGateway('paymob');
        $this->assertInstanceOf(PaymobGateway::class, $paymob);
        $this->assertEquals('paymob', $paymob->getIdentifier());
    }

    public function test_manager_throws_exception_for_unsupported_gateway(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->manager->getGateway('unsupported_gateway_xyz');
    }

    public function test_credit_card_gateway_successful_simulation(): void
    {
        $gateway = new CreditCardGateway;
        $order = new Order(['customer_email' => 'test@example.com']);

        $result = $gateway->process($order, ['card_number' => '4242424242424242']);

        $this->assertTrue($result['success']);
        $this->assertEquals(PaymentStatusEnum::SUCCESSFUL, $result['status']);
        $this->assertStringStartsWith('CC-TXN-', $result['transaction_id']);
    }

    public function test_credit_card_gateway_failure_simulation(): void
    {
        $gateway = new CreditCardGateway;
        $order = new Order;

        $result = $gateway->process($order, ['card_number' => '0000000000000000']);

        $this->assertFalse($result['success']);
        $this->assertEquals(PaymentStatusEnum::FAILED, $result['status']);
        $this->assertStringStartsWith('CC-FAIL-', $result['transaction_id']);
    }

    public function test_paypal_gateway_successful_simulation(): void
    {
        $gateway = new PaypalGateway;
        $order = new Order(['customer_email' => 'buyer@example.com']);

        $result = $gateway->process($order, []);

        $this->assertTrue($result['success']);
        $this->assertEquals(PaymentStatusEnum::SUCCESSFUL, $result['status']);
        $this->assertStringStartsWith('PAYID-', $result['transaction_id']);
    }

    public function test_paymob_gateway_successful_simulation(): void
    {
        $gateway = new PaymobGateway;
        $order = new Order;

        $result = $gateway->process($order, []);

        $this->assertTrue($result['success']);
        $this->assertEquals(PaymentStatusEnum::SUCCESSFUL, $result['status']);
        $this->assertStringStartsWith('PAYMOB-', $result['transaction_id']);
    }
}
