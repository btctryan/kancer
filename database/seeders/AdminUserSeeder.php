<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::firstOrCreate(
            ['email' => 'admin@kancer.com'],
            [
                'name' => 'Admin KANCER',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}