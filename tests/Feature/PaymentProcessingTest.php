<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Order\Enum\OrderStatusEnum;
use Modules\Order\Models\Order;
use Modules\Payment\Enum\PaymentStatusEnum;
use Modules\Payment\Models\Payment;
use Tests\TestCase;

class PaymentProcessingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Order $confirmedOrder;
    private Order $pendingOrder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'type' => 'user',
        ]);
        Sanctum::actingAs($this->user);

        $this->confirmedOrder = Order::create([
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'status' => OrderStatusEnum::CONFIRMED,
        ]);
        $this->confirmedOrder->items()->create([
            'product_name' => 'Headphones',
            'quantity' => 2,
            'price' => 75.0,
            'total' => 150.0,
        ]);

        $this->pendingOrder = Order::create([
            'customer_name' => 'Pending Bob',
            'status' => OrderStatusEnum::PENDING,
        ]);
        $this->pendingOrder->items()->create([
            'product_name' => 'Keyboard',
            'quantity' => 1,
            'price' => 50.0,
            'total' => 50.0,
        ]);
    }

    public function test_can_process_payment_via_paypal_for_confirmed_order(): void
    {
        $response = $this->postJson('/api/payments', [
            'order_id' => $this->confirmedOrder->id,
            'method' => 'paypal',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.order_id', $this->confirmedOrder->id);
        $response->assertJsonPath('data.method', 'paypal');
        $response->assertJsonPath('data.status', PaymentStatusEnum::SUCCESSFUL);
        $this->assertEquals(150, $response->json('data.amount'));

        $this->assertDatabaseHas('payments', [
            'order_id' => $this->confirmedOrder->id,
            'method' => 'paypal',
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'amount' => 150.0,
        ]);
    }

    public function test_can_process_payment_via_credit_card_for_confirmed_order(): void
    {
        $response = $this->postJson('/api/payments', [
            'order_id' => $this->confirmedOrder->id,
            'method' => 'credit_card',
            'card_number' => '4242424242424242',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.method', 'credit_card');
        $response->assertJsonPath('data.status', PaymentStatusEnum::SUCCESSFUL);

        $this->assertDatabaseHas('payments', [
            'order_id' => $this->confirmedOrder->id,
            'method' => 'credit_card',
            'status' => PaymentStatusEnum::SUCCESSFUL,
        ]);
    }

    public function test_can_process_payment_via_paymob_for_confirmed_order(): void
    {
        $response = $this->postJson('/api/payments', [
            'order_id' => $this->confirmedOrder->id,
            'method' => 'paymob',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.method', 'paymob');
        $response->assertJsonPath('data.status', PaymentStatusEnum::SUCCESSFUL);
    }

    public function test_payment_failure_simulation(): void
    {
        $response = $this->postJson('/api/payments', [
            'order_id' => $this->confirmedOrder->id,
            'method' => 'credit_card',
            'simulate_failure' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', PaymentStatusEnum::FAILED);

        $this->assertDatabaseHas('payments', [
            'order_id' => $this->confirmedOrder->id,
            'method' => 'credit_card',
            'status' => PaymentStatusEnum::FAILED,
        ]);
    }

    public function test_cannot_process_payment_for_unconfirmed_order(): void
    {
        // Business rule: Payments can only be processed for orders in confirmed status.
        $response = $this->postJson('/api/payments', [
            'order_id' => $this->pendingOrder->id,
            'method' => 'paypal',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('payments', [
            'order_id' => $this->pendingOrder->id,
        ]);
    }

    public function test_process_payment_validation_fails_for_invalid_gateway(): void
    {
        $response = $this->postJson('/api/payments', [
            'order_id' => $this->confirmedOrder->id,
            'method' => 'unknown_cryptocurrency',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_view_all_payments_and_filter_by_order_id(): void
    {
        Payment::create([
            'order_id' => $this->confirmedOrder->id,
            'method' => 'paypal',
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'amount' => 150.0,
            'transaction_id' => 'TXN-1',
        ]);

        Payment::create([
            'order_id' => $this->confirmedOrder->id,
            'method' => 'credit_card',
            'status' => PaymentStatusEnum::FAILED,
            'amount' => 150.0,
            'transaction_id' => 'TXN-2',
        ]);

        $allResponse = $this->getJson('/api/payments');
        $allResponse->assertStatus(200);
        $this->assertCount(2, $allResponse->json('data'));

        $filteredResponse = $this->getJson("/api/payments?order_id={$this->confirmedOrder->id}");
        $filteredResponse->assertStatus(200);
        $this->assertCount(2, $filteredResponse->json('data'));
    }

    public function test_can_view_payments_for_specific_order(): void
    {
        Payment::create([
            'order_id' => $this->confirmedOrder->id,
            'method' => 'paymob',
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'amount' => 150.0,
            'transaction_id' => 'PAYMOB-101',
        ]);

        $response = $this->getJson("/api/orders/{$this->confirmedOrder->id}/payments");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('paymob', $response->json('data.0.method'));
    }

    public function test_can_view_single_payment_details(): void
    {
        $payment = Payment::create([
            'order_id' => $this->confirmedOrder->id,
            'method' => 'paypal',
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'amount' => 150.0,
            'transaction_id' => 'PAYID-DETAIL-TEST',
        ]);

        $response = $this->getJson("/api/payments/{$payment->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $payment->id);
        $response->assertJsonPath('data.transaction_id', 'PAYID-DETAIL-TEST');
        $response->assertJsonPath('data.order.id', $this->confirmedOrder->id);
    }
}
