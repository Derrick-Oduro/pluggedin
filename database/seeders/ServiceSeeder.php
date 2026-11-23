<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * The starting workshop menu. Prices are typical Accra campus rates in cedis and can be
 * changed any time in Super admin → Repairs & services. Existing services are left alone,
 * except the original dollar-style placeholders, which are replaced.
 */
class ServiceSeeder extends Seeder
{
    private const OLD_PLACEHOLDER_PRICES = ['RAM Upgrade' => 29.99, 'SSD Upgrade' => 49.99, 'Full System Refresh' => 99.99];

    public function run(): void
    {
        $memory = Category::where('slug', 'memory')->value('id');
        $storage = Category::where('slug', 'storage')->value('id');
        $computers = ['laptop', 'desktop'];

        $services = [
            ['diagnosis', 'Device check-up', 50, false, 'Same day', null, null,
                'We find out exactly what’s wrong and tell you the price to fix it. The GH₵50 is taken off the repair if you go ahead.'],

            ['repair', 'Laptop screen replacement', 650, true, '1–3 days', ['laptop'], null,
                'Cracked, flickering or dead display replaced with a tested panel for your exact model.'],
            ['repair', 'Phone screen replacement', 350, true, 'Same day – 2 days', ['phone'], null,
                'New screen and touch glass fitted and checked for touch, brightness and dead pixels.'],
            ['repair', 'Battery replacement', 250, true, '1–2 days', ['laptop', 'phone', 'tablet'], null,
                'For batteries that drain fast, swell or shut down early. New battery fitted and charge-tested.'],
            ['repair', 'Charging port repair', 150, true, 'Same day', ['laptop', 'phone', 'tablet'], null,
                'Loose or dead charging port cleaned, repaired or replaced.'],
            ['repair', 'Keyboard replacement', 300, true, '1–3 days', ['laptop'], null,
                'Missing, sticky or dead keys fixed with a full replacement keyboard.'],
            ['repair', 'Water damage cleaning', 200, true, '2–4 days', null, null,
                'Device opened, cleaned and dried, and damaged parts checked. Bring it in quickly and don’t charge it.'],
            ['repair', 'Windows reinstall & virus removal', 150, false, 'Same day', $computers, null,
                'Fresh Windows install with drivers and updates, or a clean-up of viruses and pop-ups. Your files are backed up first.'],
            ['repair', 'Overheating fix', 120, false, 'Same day', $computers, null,
                'Fans cleaned and fresh thermal paste applied so it runs cooler, quieter and faster.'],

            ['upgrade', 'RAM Upgrade', 60, false, 'Same day', $computers, $memory,
                'We check what your device supports and fit the RAM. Buy it from PluggedIn or bring your own.'],
            ['upgrade', 'SSD Upgrade', 150, false, 'Same day', $computers, $storage,
                'Swap a slow hard drive for an SSD. Includes moving Windows, apps and files across.'],
            ['upgrade', 'Full System Refresh', 300, false, '1–2 days', $computers, null,
                'Deep clean, fresh thermal paste, Windows reinstall and tune-up. Add RAM or an SSD at the same time.'],
        ];

        foreach ($services as $order => [$type, $name, $price, $from, $turnaround, $devices, $parts, $description]) {
            $attributes = [
                'type' => $type,
                'description' => $description,
                'price' => $price,
                'price_is_from' => $from,
                'turnaround' => $turnaround,
                'device_types' => $devices,
                'parts_category_id' => $parts,
                'is_active' => true,
                'sort_order' => $order,
            ];

            $existing = Service::where('name', $name)->first();
            if (! $existing) {
                Service::create(['name' => $name] + $attributes);
            } elseif (isset(self::OLD_PLACEHOLDER_PRICES[$name]) && (float) $existing->price === self::OLD_PLACEHOLDER_PRICES[$name]) {
                $existing->update($attributes);
            }
        }
    }
}
