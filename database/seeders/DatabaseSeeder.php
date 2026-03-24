<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@lab.local'],
            [
                'name' => 'Admin Lab',
                'password' => 'A_super_secret_password',
                'email_verified_at' => now(),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'student@lab.local'],
            [
                'name' => 'Student Lab',
                'password' => 'student123',
                'email_verified_at' => now(),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('products')->whereIn('name', ['Keyboard', 'Mouse', 'Monitor', 'Webcam'])->delete();

        DB::table('products')->insert([
            ['name' => 'Keyboard', 'description' => 'Mechanical keyboard for office and gaming.', 'price' => 45.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mouse', 'description' => 'Compact optical mouse with USB receiver.', 'price' => 19.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monitor', 'description' => '24-inch IPS monitor with Full HD resolution.', 'price' => 210.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Webcam', 'description' => 'HD webcam with built-in microphone.', 'price' => 68.50, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
