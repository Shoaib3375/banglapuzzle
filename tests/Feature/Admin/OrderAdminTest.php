<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_view_orders_list(): void
    {
        Order::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.index');
    }

    public function test_admin_can_view_order_detail(): void
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.show');
        $response->assertSee($order->order_number);
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.orders.updateStatus', $order), [
                'status' => 'confirmed',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_guest_cannot_access_admin_orders(): void
    {
        $response = $this->get(route('admin.orders.index'));

        $response->assertRedirect(route('admin.login'));
    }
}
