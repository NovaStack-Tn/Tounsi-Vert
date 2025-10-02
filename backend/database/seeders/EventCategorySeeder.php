<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Food Aid',
            'War Relief',
            'Mosque Building',
            'Health',
            'Education',
            'Tree Planting',
            'Clean Up',
            'Recycling',
            'Community Development',
            'Youth Programs',
        ];

        foreach ($categories as $category) {
            EventCategory::create(['name' => $category]);
        }
    }
}
