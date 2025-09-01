<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'role' => 'client',
        ]);

        User::factory()->create([
            'name' => 'Pharmacist User',
            'email' => 'pharmacist@example.com',
            'role' => 'pharmacist',
        ]);

        User::factory()->create([
            'name' => 'Salesperson User',
            'email' => 'salesperson@example.com',
            'role' => 'salesperson',
        ]);

        User::factory()->create([
            'name' => 'Delivery User',
            'email' => 'delivery@example.com',
            'role' => 'delivery',
        ]);

        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'role' => 'manager',
        ]);
    }
}
