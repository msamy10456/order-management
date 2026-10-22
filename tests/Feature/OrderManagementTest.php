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

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'type' => 'user',
        ]);
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_order_with_items_and_calculate_total(): void
    {
        $payload = [
            'customer_name' => 'Alice Smith',
            'customer_email' => 'alice@example.com',
            'customer_phone' => '01012345678',
            'address' => '123 Main St, Cairo',
            'items' => [
                [
                    'product_name' => 'Mechanical Keyboard',
                    'quantity' => 2,
                    'price' => 50.0,
                ],
                [
                    'product_name' => 'Wireless Mouse',
                    'quantity' => 1,
                    'price' => 25.0,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Alice Smith',
            'customer_email' => 'alice@example.com',
            'status' => OrderStatusEnum::PENDING,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_name' => 'Mechanical Keyboard',
            'quantity' => 2,
            'price' => 50.0,
            'total' => 100.0,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_name' => 'Wireless Mouse',
            'quantity' => 1,
            'price' => 25.0,
            'total' => 25.0,
        ]);

        $data = $response->json('data');
        $this->assertEquals(125.0, $data['total_price'] ?? $data['total_items']);
    }

    public function test_can_view_orders_and_filter_by_status(): void
    {
        Order::create([
            'customer_name' => 'Pending Customer',
            'status' => OrderStatusEnum::PENDING,
        ]);

        Order::create([
            'customer_name' => 'Confirmed Customer',
            'status' => OrderStatusEnum::CONFIRMED,
        ]);

        Order::create([
            'customer_name' => 'Cancelled Customer',
            'status' => OrderStatusEnum::CANCELLED,
        ]);

        $allResponse = $this->getJson('/api/orders');
        $allResponse->assertStatus(200);
        $this->assertCount(3, $allResponse->json('data'));

        $confirmedResponse = $this->getJson('/api/orders?status=confirmed');
        $confirmedResponse->assertStatus(200);
        $this->assertCount(1, $confirmedResponse->json('data'));
        $this->assertEquals('Confirmed Customer', $confirmedResponse->json('data.0.customer_name'));

        $pendingResponse = $this->getJson('/api/orders?status=pending');
        $pendingResponse->assertStatus(200);
        $this->assertCount(1, $pendingResponse->json('data'));
        $this->assertEquals('Pending Customer', $pendingResponse->json('data.0.customer_name'));
    }

    public function test_can_show_single_order_with_items(): void
    {
        $order = Order::create([
            'customer_name' => 'Jane Doe',
            'status' => OrderStatusEnum::PENDING,
        ]);
        $order->items()->create([
            'product_name' => 'Laptop Bag',
            'quantity' => 1,
            'price' => 40.0,
            'total' => 40.0,
        ]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.customer_name', 'Jane Doe');
        $response->assertJsonPath('data.items.0.product_name', 'Laptop Bag');
    }

    public function test_can_update_order_and_change_status(): void
    {
        $order = Order::create([
            'customer_name' => 'Old Name',
            'status' => OrderStatusEnum::PENDING,
        ]);

        $response = $this->putJson("/api/orders/{$order->id}", [
            'customer_name' => 'New Name',
            'status' => 'confirmed',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'customer_name' => 'New Name',
            'status' => OrderStatusEnum::CONFIRMED,
        ]);
    }

    public function test_can_delete_order_without_associated_payments(): void
    {
        $order = Order::create([
            'customer_name' => 'Deletable Order',
            'status' => OrderStatusEnum::PENDING,
        ]);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_cannot_delete_order_with_associated_payments(): void
    {
        $order = Order::create([
            'customer_name' => 'Paid Order',
            'status' => OrderStatusEnum::CONFIRMED,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'method' => 'paypal',
            'amount' => 100.0,
            'transaction_id' => 'PAYID-12345',
        ]);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        // Business rule: HTTP 422 preventing deletion
        $response->assertStatus(422);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_order_creation_validation_fails_for_invalid_data(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_name' => '',
            'items' => [],
        ]);

        $response->assertStatus(422);
    }
}
