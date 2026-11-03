<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'users.manage'          => 'Manage users and permissions',
            'settings.manage'       => 'Manage restaurant settings',
            'audit.view'            => 'View audit logs',
            'reports.view'          => 'View reports',
            'menu.manage'           => 'Manage categories and menu items',
            'tables.manage'         => 'Manage tables and reservations',
            'tables.use'            => 'View and use tables',
            'orders.create'         => 'Create orders',
            'orders.update'         => 'Update orders before preparation',
            'orders.approve_cancel' => 'Approve cancellation after preparation',
            'kitchen.view'          => 'View kitchen screen',
            'kitchen.update'        => 'Update kitchen item status',
            'pos.use'               => 'Use POS and billing',
            'payments.take'         => 'Take payments',
            'discounts.apply'       => 'Apply discounts',
            'refunds.process'       => 'Process refunds',
            'refunds.approve'       => 'Approve refunds above cashier limit',
            'inventory.manage'      => 'Manage ingredients and recipes',
            'stock.adjust'          => 'Adjust stock and record damage',
            'purchases.manage'      => 'Manage suppliers and purchasing',
            'expenses.manage'       => 'Manage expenses',
        ];

        foreach ($permissions as $name => $label) {
            Permission::updateOrCreate(['name' => $name], ['label' => $label]);
        }

        $roles = [
            'admin'   => ['Admin', array_keys($permissions)],
            'manager' => ['Manager', [
                'reports.view', 'menu.manage', 'tables.manage', 'tables.use',
                'orders.create', 'orders.update', 'orders.approve_cancel',
                'kitchen.view', 'pos.use', 'payments.take', 'discounts.apply',
                'refunds.process', 'refunds.approve', 'inventory.manage',
                'stock.adjust', 'purchases.manage', 'expenses.manage',
            ]],
            'cashier' => ['Cashier', [
                'tables.use', 'pos.use', 'payments.take',
                'discounts.apply', 'refunds.process',
            ]],
            'waiter'  => ['Waiter', [
                'tables.use', 'orders.create', 'orders.update',
            ]],
            'chef'    => ['Chef', ['kitchen.view', 'kitchen.update']],
        ];

        foreach ($roles as $name => [$label, $permissionNames]) {
            $role = Role::updateOrCreate(['name' => $name], ['label' => $label]);
            $role->permissions()->sync(
                Permission::whereIn('name', $permissionNames)->pluck('id')
            );
        }

        $this->createLocalDemoUsers();
    }

    // Demo users are created only on a local machine (APP_ENV=local)
    // and only when SEED_PASSWORD is set in .env.
    private function createLocalDemoUsers(): void
    {
        $password = env('SEED_PASSWORD');

        if (! app()->environment('local') || ! $password) {
            return;
        }

        foreach (['admin', 'manager', 'cashier', 'waiter', 'chef'] as $roleName) {
            User::updateOrCreate(
                ['email' => "{$roleName}@example.test"],
                [
                    'name'      => ucfirst($roleName) . ' Demo',
                    'password'  => Hash::make($password),
                    'role_id'   => Role::where('name', $roleName)->value('id'),
                    'is_active' => true,
                ]
            );
        }
    }
}
