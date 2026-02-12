<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'RAM Upgrade',
                'description' => 'Professional RAM installation and upgrade service. We\'ll help you choose the right memory and install it safely. Includes compatibility check and performance testing.',
                'price' => 29.99,
            ],
            [
                'name' => 'SSD Upgrade',
                'description' => 'Complete SSD upgrade service including data migration, installation, and optimization. Breathe new life into your old computer with blazing-fast storage.',
                'price' => 49.99,
            ],
            [
                'name' => 'Full System Refresh',
                'description' => 'Comprehensive system upgrade including RAM, SSD, and full optimization. Perfect for extending the life of your computer by 3-5 years. Includes data migration and software setup.',
                'price' => 99.99,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
