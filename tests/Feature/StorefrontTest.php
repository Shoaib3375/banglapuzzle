<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private Subcategory $subcategory;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create(['is_active' => true]);
        $this->subcategory = Subcategory::factory()
            ->forCategory($this->category->id)
            ->create(['is_active' => true]);
        $this->product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['is_active' => true]);
    }

    public function test_home_page_returns_200(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    public function test_home_page_does_not_show_inactive_product(): void
    {
        $inactive = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['is_active' => false, 'name' => 'Hidden Inactive Product']);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee($inactive->name);
    }

    public function test_category_page_returns_200_for_active_category(): void
    {
        $response = $this->get(route('storefront.category', $this->category->slug));

        $response->assertStatus(200);
    }

    public function test_category_page_returns_404_for_inactive_category(): void
    {
        $inactiveCategory = Category::factory()->create(['is_active' => false]);

        $response = $this->get(route('storefront.category', $inactiveCategory->slug));

        $response->assertStatus(404);
    }

    public function test_category_page_returns_404_for_nonexistent_category(): void
    {
        $response = $this->get(route('storefront.category', 'no-such-slug'));

        $response->assertStatus(404);
    }

    public function test_subcategory_page_returns_200_for_active_subcategory(): void
    {
        $response = $this->get(route('storefront.subcategory', [
            $this->category->slug,
            $this->subcategory->slug,
        ]));

        $response->assertStatus(200);
    }

    public function test_subcategory_page_returns_404_for_inactive_subcategory(): void
    {
        $inactiveSub = Subcategory::factory()
            ->forCategory($this->category->id)
            ->create(['is_active' => false]);

        $response = $this->get(route('storefront.subcategory', [
            $this->category->slug,
            $inactiveSub->slug,
        ]));

        $response->assertStatus(404);
    }

    public function test_subcategory_page_returns_404_when_subcategory_belongs_to_different_category(): void
    {
        $otherCategory = Category::factory()->create(['is_active' => true]);

        $response = $this->get(route('storefront.subcategory', [
            $otherCategory->slug,
            $this->subcategory->slug,
        ]));

        $response->assertStatus(404);
    }

    public function test_product_page_returns_200_for_active_product(): void
    {
        $response = $this->get(route('storefront.product', $this->product->slug));

        $response->assertStatus(200);
    }

    public function test_product_page_returns_404_for_inactive_product(): void
    {
        $inactive = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['is_active' => false]);

        $response = $this->get(route('storefront.product', $inactive->slug));

        $response->assertStatus(404);
    }

    public function test_product_page_returns_404_when_category_is_inactive(): void
    {
        $inactiveCategory = Category::factory()->create(['is_active' => false]);
        $sub = Subcategory::factory()
            ->forCategory($inactiveCategory->id)
            ->create(['is_active' => true]);

        $product = Product::factory()
            ->forSubcategory($inactiveCategory->id, $sub->id)
            ->create(['is_active' => true]);

        $response = $this->get(route('storefront.product', $product->slug));

        $response->assertStatus(404);
    }

    public function test_product_page_returns_404_when_subcategory_is_inactive(): void
    {
        $activeCategory = Category::factory()->create(['is_active' => true]);
        $inactiveSub = Subcategory::factory()
            ->forCategory($activeCategory->id)
            ->create(['is_active' => false]);

        $product = Product::factory()
            ->forSubcategory($activeCategory->id, $inactiveSub->id)
            ->create(['is_active' => true]);

        $response = $this->get(route('storefront.product', $product->slug));

        $response->assertStatus(404);
    }

    public function test_product_page_shows_discount_percentage_when_applicable(): void
    {
        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create([
                'is_active' => true,
                'old_price' => 1000,
                'new_price' => 800,
            ]);

        $response = $this->get(route('storefront.product', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('20%');
        $response->assertSee('OFF');
    }

    public function test_user_can_register(): void
    {
        $response = $this->post(route('user.register.post'), [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('storefront.profile'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'is_admin' => false,
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->post(route('user.login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get(route('storefront.profile'));

        $response->assertRedirect(route('user.login'));
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Customer',
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get(route('storefront.profile'));

        $response->assertStatus(200);
        $response->assertSee('Personal Information');
        $response->assertSee('Jane Customer');
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->post(route('storefront.profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'phone' => '01700000000',
            'address' => 'House 1',
            'city' => 'Dhaka',
            'district' => 'Dhaka',
            'postcode' => '1207',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Profile updated successfully.');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '01700000000',
            'district' => 'Dhaka',
        ]);
    }

    /** Admin account cannot log in through the customer login route. */
    public function test_admin_cannot_login_via_customer_route(): void
    {
        $admin = \App\Models\User::factory()->create([
            'email'    => 'admin-security@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $response = $this->post(route('user.login.post'), [
            'email'    => 'admin-security@test.com',
            'password' => 'password',
        ]);

        // Should not be authenticated (or redirected back with error) —
        // the customer login guard requires is_admin = false.
        $this->assertFalse($this->isAuthenticated() && auth()->user()?->id === $admin->id && !auth()->user()?->is_admin);
        // Ensure the admin is NOT placed in the session as a logged-in customer.
        if (auth()->check()) {
            $this->assertTrue((bool) auth()->user()->is_admin === false || auth()->user()->id !== $admin->id);
        }
    }

    /** Regular customer cannot access the admin dashboard. */
    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = \App\Models\User::factory()->create([
            'email'    => 'customer-sec@test.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        $response = $this->actingAs($customer)
            ->get(route('admin.dashboard'));

        // Should be redirected away — customer is not an admin.
        $response->assertRedirect();
        $this->assertNotEquals(route('admin.dashboard'), $response->headers->get('Location'));
    }
}
