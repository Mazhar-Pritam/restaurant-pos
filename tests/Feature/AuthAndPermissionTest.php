<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role_id'   => Role::where('name', $role)->value('id'),
            'is_active' => true,
        ], $attributes));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_active_user_can_log_in(): void
    {
        $user = $this->userWithRole('cashier');

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->userWithRole('cashier');

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = $this->userWithRole('cashier', ['is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_open_user_management(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get('/admin/users')->assertOk();
    }

    public function test_chef_and_manager_cannot_open_user_management(): void
    {
        $this->actingAs($this->userWithRole('chef'))
            ->get('/admin/users')->assertForbidden();

        $this->actingAs($this->userWithRole('manager'))
            ->get('/admin/users')->assertForbidden();
    }

    public function test_role_permissions_match_the_design(): void
    {
        $waiter  = $this->userWithRole('waiter');
        $chef    = $this->userWithRole('chef');
        $cashier = $this->userWithRole('cashier');

        $this->assertTrue($waiter->hasPermission('orders.create'));
        $this->assertFalse($waiter->hasPermission('refunds.process'));
        $this->assertTrue($chef->hasPermission('kitchen.update'));
        $this->assertFalse($chef->hasPermission('pos.use'));
        $this->assertTrue($cashier->hasPermission('refunds.process'));
        $this->assertFalse($cashier->hasPermission('refunds.approve'));
    }

    public function test_user_without_a_role_has_no_permissions(): void
    {
        $user = User::factory()->create(['role_id' => null]);

        $this->assertFalse($user->hasPermission('orders.create'));
    }
}
