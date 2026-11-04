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

    public function test_guest_gets_401_from_protected_api(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
    }

    public function test_active_user_can_log_in(): void
    {
        $user = $this->userWithRole('cashier');

        $this->postJson('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->userWithRole('cashier');

        $this->postJson('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = $this->userWithRole('cashier', ['is_active' => false]);

        $this->postJson('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertUnprocessable();

        $this->assertGuest();
    }

    public function test_api_user_returns_role_and_permissions(): void
    {
        $this->actingAs($this->userWithRole('waiter'))
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('role.name', 'waiter')
            ->assertJsonFragment(['orders.create'])
            ->assertJsonMissing(['refunds.process']);
    }

    public function test_inactive_user_with_session_is_blocked_from_api(): void
    {
        $this->actingAs($this->userWithRole('cashier', ['is_active' => false]))
            ->getJson('/api/user')
            ->assertForbidden();
    }

    public function test_admin_can_open_user_management_endpoint(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->getJson('/api/admin/users')->assertOk();
    }

    public function test_chef_and_manager_cannot_open_user_management_endpoint(): void
    {
        $this->actingAs($this->userWithRole('chef'))
            ->getJson('/api/admin/users')->assertForbidden();

        $this->actingAs($this->userWithRole('manager'))
            ->getJson('/api/admin/users')->assertForbidden();
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
