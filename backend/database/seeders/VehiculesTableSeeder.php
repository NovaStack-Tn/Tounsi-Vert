<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicule;
use Illuminate\Support\Facades\Auth;

class VehiculesTableSeeder extends Seeder
{
    public function run()
    {
        $vehicules = [
            [
                'type' => 'Compact Car',
                'description' => 'Small city car, fuel efficient, for elderly transportation',
                'capacity' => 4,
                'availability_start' => '2025-10-25',
                'availability_end' => '2025-12-31',
                'location' => 'Sousse',
                'status' => 'active',
                'image_path' => 'vehicules/car1.jpg',
                'owner_id' => 1
            ],
            [
                'type' => 'SUV',
                'description' => 'Spacious SUV for family trips',
                'capacity' => 7,
                'availability_start' => '2025-10-26',
                'availability_end' => '2025-12-31',
                'location' => 'Tunis',
                'status' => 'active',
                'image_path' => 'vehicules/car2.jpg',
                'owner_id' => 1
            ],
            [
                'type' => 'Sport Car',
                'description' => 'Fast and stylish',
                'capacity' => 2,
                'availability_start' => '2025-10-27',
                'availability_end' => '2025-12-31',
                'location' => 'Sfax',
                'status' => 'active',
                'image_path' => 'vehicules/car3.jpg',
                'owner_id' => 1
            ],
            [
                'type' => 'Van',
                'description' => 'Perfect for group travels',
                'capacity' => 12,
                'availability_start' => '2025-10-28',
                'availability_end' => '2025-12-31',
                'location' => 'Monastir',
                'status' => 'active',
                'image_path' => 'vehicules/car4.jpg',
                'owner_id' => 1
            ],
            // add 6 more without images or use car1–4 again for testing
            [
                'type' => 'Pickup Truck',
                'description' => 'For heavy loads',
                'capacity' => 3,
                'availability_start' => '2025-10-29',
                'availability_end' => '2025-12-31',
                'location' => 'Sousse',
                'status' => 'active',
                'image_path' => 'vehicules/car1.jpg',
                'owner_id' => 2
            ],
            [
                'type' => 'Electric Car',
                'description' => 'Eco-friendly electric vehicle',
                'capacity' => 4,
                'availability_start' => '2025-10-30',
                'availability_end' => '2025-12-31',
                'location' => 'Tunis',
                'status' => 'active',
                'image_path' => 'vehicules/car2.jpg',
                'owner_id' => 2
            ],
            [
                'type' => 'Luxury Sedan',
                'description' => 'Comfortable and classy',
                'capacity' => 5,
                'availability_start' => '2025-11-01',
                'availability_end' => '2025-12-31',
                'location' => 'Sfax',
                'status' => 'active',
                'image_path' => 'vehicules/car3.jpg',
                'owner_id' => 2
            ],
            [
                'type' => 'Minibus',
                'description' => 'Ideal for small groups',
                'capacity' => 15,
                'availability_start' => '2025-11-02',
                'availability_end' => '2025-12-31',
                'location' => 'Monastir',
                'status' => 'active',
                'image_path' => 'vehicules/car4.jpg',
                'owner_id' => 2
            ],
            [
                'type' => 'Convertible',
                'description' => 'Fun for weekend rides',
                'capacity' => 2,
                'availability_start' => '2025-11-03',
                'availability_end' => '2025-12-31',
                'location' => 'Sousse',
                'status' => 'active',
                'image_path' => 'vehicules/car1.jpg',
                'owner_id' => 1
            ],
            [
                'type' => 'Hatchback',
                'description' => 'Compact and practical',
                'capacity' => 4,
                'availability_start' => '2025-11-04',
                'availability_end' => '2025-12-31',
                'location' => 'Tunis',
                'status' => 'active',
                'image_path' => 'vehicules/car2.jpg',
                'owner_id' => 1
            ],
        ];

        foreach ($vehicules as $v) {
            Vehicule::create($v);
        }
    }
}
