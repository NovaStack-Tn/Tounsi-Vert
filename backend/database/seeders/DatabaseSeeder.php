<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            OrgCategorySeeder::class,
            EventCategorySeeder::class,
        ]);

        // Create demo users
        \App\Models\User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@tounsivert.tn',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'region' => 'Tunis',
            'city' => 'Tunis',
            'score' => 100,
        ]);

        \App\Models\User::create([
            'first_name' => 'Organizer',
            'last_name' => 'Demo',
            'email' => 'organizer@tounsivert.tn',
            'password' => bcrypt('password'),
            'role' => 'organizer',
            'region' => 'Tunis',
            'city' => 'Ariana',
            'phone_number' => '+216 20 123 456',
            'score' => 50,
        ]);

        \App\Models\User::create([
            'first_name' => 'Member',
            'last_name' => 'Demo',
            'email' => 'member@tounsivert.tn',
            'password' => bcrypt('password'),
            'role' => 'member',
            'region' => 'Tunis',
            'city' => 'Ben Arous',
            'score' => 25,
        ]);

        // Create additional member users for testing
        for ($i = 1; $i <= 5; $i++) {
            \App\Models\User::create([
                'first_name' => 'Member',
                'last_name' => "Test{$i}",
                'email' => "member{$i}@tounsivert.tn",
                'password' => bcrypt('password'),
                'role' => 'member',
                'region' => 'Tunis',
                'city' => 'Tunis',
                'score' => rand(10, 100),
            ]);
        }

        // Seed reports after users and organizations are created
        $this->call([
            ReportSeeder::class,
        ]);
    }
}
