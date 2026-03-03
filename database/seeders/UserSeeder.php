<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       User::create([
            'name' => 'Dummy User',
            'email' => 'user@touchme.com',
            'role' => 'user',
            'password' => Hash::make('user123'),
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@touchme.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);
    }
}
