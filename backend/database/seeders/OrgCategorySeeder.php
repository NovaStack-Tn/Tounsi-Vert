<?php

namespace Database\Seeders;

use App\Models\OrgCategory;
use Illuminate\Database\Seeder;

class OrgCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Charity',
            'Mosque',
            'NGO',
            'Municipality',
            'Student Club',
            'Association',
            'Foundation',
        ];

        foreach ($categories as $category) {
            OrgCategory::firstOrCreate(['name' => $category]);
        }
    }
}
