<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use App\Models\OrgCategory;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::where('role', 'organizer')->first();
        $orgCategory = OrgCategory::first();
        $eventCategory = EventCategory::first();

        if (!$organizer || !$orgCategory) {
            $this->command->warn('Skipping TestDataSeeder: No organizer or categories found');
            return;
        }

        // Create test organizations
        $organizations = [];
        for ($i = 1; $i <= 3; $i++) {
            $organizations[] = Organization::create([
                'owner_id' => $organizer->id,
                'org_category_id' => $orgCategory->id,
                'name' => "Green Tunisia Organization {$i}",
                'description' => "A dedicated environmental organization working towards a greener Tunisia. We organize various eco-friendly events and initiatives.",
                'address' => "{$i}23 Avenue Habib Bourguiba",
                'region' => 'Tunis',
                'city' => 'Tunis',
                'zipcode' => '1000',
                'phone_number' => '+216 71 ' . (100000 + $i),
                'is_verified' => true,
                'is_blocked' => false,
            ]);
        }

        // Create test events for each organization
        if ($eventCategory) {
            foreach ($organizations as $index => $org) {
                for ($j = 1; $j <= 2; $j++) {
                    Event::create([
                        'organization_id' => $org->id,
                        'event_category_id' => $eventCategory->id,
                        'title' => "Beach Cleanup Event {$index}-{$j}",
                        'description' => "Join us for a beach cleanup event to help keep our coasts clean and beautiful.",
                        'address' => "La Marsa Beach",
                        'region' => 'Tunis',
                        'city' => 'La Marsa',
                        'zipcode' => '2078',
                        'start_at' => now()->addDays(rand(7, 30)),
                        'end_at' => now()->addDays(rand(7, 30))->addHours(4),
                        'max_participants' => rand(20, 100),
                        'is_published' => true,
                    ]);
                }
            }
        }

        $this->command->info('Test organizations and events created successfully!');
    }
}
