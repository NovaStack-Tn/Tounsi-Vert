<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'TounsiVert',
            'email' => 'admin@tounsivert.tn',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'region' => 'Grand Tunis',
            'city' => 'Tunis',
            'phone_number' => '+216 70 123 456',
            'score' => 0,
            'email_verified_at' => now(),
        ]);

        // Sample organizer
        User::create([
            'first_name' => 'Ahmed',
            'last_name' => 'Ben Ali',
            'email' => 'organizer@tounsivert.tn',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'region' => 'Grand Tunis',
            'city' => 'Ariana',
            'phone_number' => '+216 71 234 567',
            'score' => 50,
            'email_verified_at' => now(),
        ]);

        // Sample member
        User::create([
            'first_name' => 'Fatma',
            'last_name' => 'Hamdi',
            'email' => 'member@tounsivert.tn',
            'password' => Hash::make('password'),
            'role' => 'member',
            'region' => 'Sousse',
            'city' => 'Sousse',
            'phone_number' => '+216 73 345 678',
            'score' => 25,
            'email_verified_at' => now(),
        ]);
    }
}
