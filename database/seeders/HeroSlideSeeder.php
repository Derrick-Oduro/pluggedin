<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $slides = [
            [
                'title' => 'Upgrade. Don\'t Replace.',
                'caption' => 'Welcome to PluggedIn — Upgrade. Don\'t Replace.',
                'image_url' => '/images/anas-009s8_96qpQ-unsplash.jpg',
                'alt_text' => 'Professional computer upgrade service',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Quality Components',
                'caption' => 'Quality components and careful installs.',
                'image_url' => '/images/nikita-kachanovsky-OVbeSXRk_9E-unsplash.jpg',
                'alt_text' => 'High-quality electronics components',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Book a Service',
                'caption' => 'Book a service or browse upgrades today.',
                'image_url' => '/images/pakata-goh-RDolnHtjVCY-unsplash.jpg',
                'alt_text' => 'Professional technician at work',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::updateOrCreate(
                ['title' => $slide['title']],
                $slide
            );
        }
    }
}
