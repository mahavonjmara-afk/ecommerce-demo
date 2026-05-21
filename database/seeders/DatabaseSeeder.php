<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'name' => 'Admin Demo',
            'password' => bcrypt('admin123!'),
            'is_admin' => true,
        ]);

        // Client
        User::firstOrCreate(['email' => 'client@gmail.com'], [
            'name' => 'Client Test',
            'password' => bcrypt('client123!'),
            'is_admin' => false,
        ]);
    }
}