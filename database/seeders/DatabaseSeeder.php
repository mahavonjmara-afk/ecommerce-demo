<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */

public function run(): void
{
    // Admin de test
    User::factory()->create([
        'name' => 'Admin E-Shop',
        'email' => 'omar@gmail.com',
        'password' => Hash::make('admin12'),
        'is_admin' => true,
        'email_verified_at' => now(),
    ]);

    // Utilisateur normal de test
    User::factory()->create([
        'name' => 'Client Test',
        'email' => 'client@gmail.com',
        'password' => Hash::make('client123'),
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);
}
}
