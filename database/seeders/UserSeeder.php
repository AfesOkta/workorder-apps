<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'manager@apps.com',
            'name' => 'manager',
            'password' => bcrypt('password'),
            'role' => 'production_manager',
        ]);

        User::create([
            'email' => 'operator1@apps.com',
            'name' => 'operator1',
            'password' => bcrypt('password'),
            'role' => 'operator',
        ]);
    }
}
