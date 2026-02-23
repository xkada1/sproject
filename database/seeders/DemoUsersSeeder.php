<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Admin User', 'email' => 'admin@saucywing.test', 'role' => 'admin'],
            ['name' => 'Manager User', 'email' => 'manager@saucywing.test', 'role' => 'manager'],
            ['name' => 'Cashier User', 'email' => 'cashier@saucywing.test', 'role' => 'cashier'],
        ];

        foreach ($defaults as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'role' => $u['role'],
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
