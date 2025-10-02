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
            'Collective',
        ];

        foreach ($categories as $category) {
            OrgCategory::create(['name' => $category]);
        }
    }
}
