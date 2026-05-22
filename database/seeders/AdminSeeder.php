<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'admin@nazfram.local',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'alamat' => 'Admin',
                'nohp' => '0812345678',
            ]
        );
    }
}
