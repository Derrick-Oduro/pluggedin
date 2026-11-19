<?php

/*
|--------------------------------------------------------------------------
| Product specification templates
|--------------------------------------------------------------------------
|
| Each category is linked to one template (categories.spec_template). When a
| category has no template set, App\Support\ProductSpecs guesses one from the
| category slug using the "keywords" list below.
|
| Field keys:
|   label        Shown on the form and the product page
|   type         text | number | select | textarea
|   options      For select fields
|   required     Must be filled before the listing can be submitted
|   suffix       Unit appended when displayed (e.g. "GB", "inches")
|   placeholder  Hint shown in the empty field
|
*/

$yesNo = ['Yes', 'No'];

return [

    'conditions' => [
        'new' => 'Brand new (sealed)',
        'open_box' => 'Open box',
        'like_new' => 'Used - like new',
        'good' => 'Used - good',
        'fair' => 'Used - fair',
        'refurbished' => 'Refurbished',
        'for_parts' => 'For parts / not working',
    ],

    'templates' => [

        'laptop' => [
            'label' => 'Laptop',
            'keywords' => ['laptop', 'notebook', 'macbook', 'chromebook', 'ultrabook'],
            'groups' => [
                'Basics' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. HP, Dell, Lenovo, Apple'],
                    'model' => ['label' => 'Model', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. EliteBook 840 G8'],
                    'release_year' => ['label' => 'Release year', 'type' => 'number', 'placeholder' => 'e.g. 2022'],
                    'colour' => ['label' => 'Colour', 'type' => 'text'],
                ],
                'Performance' => [
                    'processor' => ['label' => 'Processor (CPU)', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Intel Core i5-1135G7, Apple M2'],
                    'processor_generation' => ['label' => 'Processor generation', 'type' => 'text', 'placeholder' => 'e.g. 11th Gen'],
                    'ram_size' => ['label' => 'RAM', 'type' => 'select', 'required' => true, 'suffix' => 'GB', 'options' => ['2', '4', '8', '12', '16', '24', '32', '64']],
                    'ram_type' => ['label' => 'RAM type', 'type' => 'select', 'options' => ['DDR3', 'DDR4', 'DDR5', 'LPDDR4', 'LPDDR4X', 'LPDDR5', 'Unified memory']],
                    'ram_upgradeable' => ['label' => 'RAM upgradeable', 'type' => 'select', 'options' => $yesNo],
                    'storage_size' => ['label' => 'Storage capacity', 'type' => 'select', 'required' => true, 'options' => ['64 GB', '128 GB', '256 GB', '512 GB', '1 TB', '2 TB', '4 TB']],
                    'storage_type' => ['label' => 'Storage type', 'type' => 'select', 'required' => true, 'options' => ['NVMe SSD', 'SATA SSD', 'HDD', 'SSD + HDD', 'eMMC']],
                    'graphics' => ['label' => 'Graphics (GPU)', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Intel Iris Xe, NVIDIA RTX 3050'],
                    'graphics_memory' => ['label' => 'Dedicated GPU memory', 'type' => 'select', 'options' => ['Shared / integrated', '2 GB', '4 GB', '6 GB', '8 GB', '12 GB', '16 GB']],
                ],
                'Display' => [
                    'screen_size' => ['label' => 'Screen size', 'type' => 'number', 'required' => true, 'suffix' => 'inches', 'placeholder' => 'e.g. 14'],
                    'resolution' => ['label' => 'Resolution', 'type' => 'select', 'options' => ['HD (1366 x 768)', 'Full HD (1920 x 1080)', 'WUXGA (1920 x 1200)', '2K / QHD (2560 x 1440)', '2.5K / Retina', '4K / UHD (3840 x 2160)']],
                    'panel_type' => ['label' => 'Panel type', 'type' => 'select', 'options' => ['IPS', 'TN', 'VA', 'OLED', 'Mini-LED', 'Retina']],
                    'refresh_rate' => ['label' => 'Refresh rate', 'type' => 'select', 'options' => ['60 Hz', '90 Hz', '120 Hz', '144 Hz', '165 Hz', '240 Hz']],
                    'touchscreen' => ['label' => 'Touchscreen', 'type' => 'select', 'options' => $yesNo],
                ],
                'Battery & power' => [
                    'battery_health' => ['label' => 'Battery health', 'type' => 'number', 'suffix' => '%', 'placeholder' => 'e.g. 88'],
                    'battery_life' => ['label' => 'Battery life (real use)', 'type' => 'text', 'placeholder' => 'e.g. 5-6 hours'],
                    'charger_included' => ['label' => 'Charger included', 'type' => 'select', 'required' => true, 'options' => ['Yes, original', 'Yes, compatible', 'No']],
                ],
                'Software' => [
                    'operating_system' => ['label' => 'Operating system', 'type' => 'select', 'required' => true, 'options' => ['Windows 11', 'Windows 10', 'macOS', 'ChromeOS', 'Linux', 'No OS']],
                    'os_activated' => ['label' => 'OS licence activated', 'type' => 'select', 'options' => $yesNo],
                ],
                'Features & connectivity' => [
                    'keyboard_backlit' => ['label' => 'Backlit keyboard', 'type' => 'select', 'options' => $yesNo],
                    'fingerprint_reader' => ['label' => 'Fingerprint / face unlock', 'type' => 'select', 'options' => $yesNo],
                    'webcam' => ['label' => 'Webcam', 'type' => 'text', 'placeholder' => 'e.g. 720p, 1080p'],
                    'wifi' => ['label' => 'Wi-Fi', 'type' => 'select', 'options' => ['Wi-Fi 5', 'Wi-Fi 6', 'Wi-Fi 6E', 'Wi-Fi 7']],
                    'bluetooth' => ['label' => 'Bluetooth', 'type' => 'text', 'placeholder' => 'e.g. 5.1'],
                    'ports' => ['label' => 'Ports', 'type' => 'text', 'placeholder' => 'e.g. 2x USB-A, 1x USB-C, HDMI, SD card'],
                    'weight' => ['label' => 'Weight', 'type' => 'number', 'suffix' => 'kg', 'placeholder' => 'e.g. 1.4'],
                ],
                'Condition details' => [
                    'known_issues' => ['label' => 'Known issues / defects', 'type' => 'textarea', 'placeholder' => 'Scratches, dead pixels, worn keys... write "None" if none'],
                    'warranty' => ['label' => 'Warranty', 'type' => 'text', 'placeholder' => 'e.g. 3 months seller warranty'],
                ],
            ],
        ],

        'desktop' => [
            'label' => 'Desktop / PC',
            'keywords' => ['desktop', 'pc', 'computer', 'imac', 'all-in-one', 'workstation'],
            'groups' => [
                'Basics' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Dell, HP, Custom build'],
                    'model' => ['label' => 'Model', 'type' => 'text', 'required' => true],
                    'form_factor' => ['label' => 'Form factor', 'type' => 'select', 'required' => true, 'options' => ['Tower', 'Mini tower', 'Small form factor', 'Mini PC', 'All-in-one']],
                ],
                'Performance' => [
                    'processor' => ['label' => 'Processor (CPU)', 'type' => 'text', 'required' => true],
                    'ram_size' => ['label' => 'RAM', 'type' => 'select', 'required' => true, 'suffix' => 'GB', 'options' => ['4', '8', '16', '32', '64', '128']],
                    'ram_type' => ['label' => 'RAM type', 'type' => 'select', 'options' => ['DDR3', 'DDR4', 'DDR5', 'Unified memory']],
                    'storage_size' => ['label' => 'Storage capacity', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. 512 GB SSD + 1 TB HDD'],
                    'storage_type' => ['label' => 'Storage type', 'type' => 'select', 'required' => true, 'options' => ['NVMe SSD', 'SATA SSD', 'HDD', 'SSD + HDD']],
                    'graphics' => ['label' => 'Graphics (GPU)', 'type' => 'text', 'required' => true],
                    'power_supply' => ['label' => 'Power supply', 'type' => 'text', 'placeholder' => 'e.g. 650W 80+ Bronze'],
                ],
                'Software & extras' => [
                    'operating_system' => ['label' => 'Operating system', 'type' => 'select', 'required' => true, 'options' => ['Windows 11', 'Windows 10', 'macOS', 'Linux', 'No OS']],
                    'wifi' => ['label' => 'Built-in Wi-Fi', 'type' => 'select', 'options' => $yesNo],
                    'ports' => ['label' => 'Ports', 'type' => 'text'],
                    'included_peripherals' => ['label' => 'Included peripherals', 'type' => 'text', 'placeholder' => 'e.g. Monitor, keyboard, mouse'],
                    'known_issues' => ['label' => 'Known issues / defects', 'type' => 'textarea'],
                    'warranty' => ['label' => 'Warranty', 'type' => 'text'],
                ],
            ],
        ],

        'phone' => [
            'label' => 'Phone',
            'keywords' => ['phone', 'smartphone', 'iphone', 'mobile', 'android'],
            'groups' => [
                'Basics' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Apple, Samsung, Tecno'],
                    'model' => ['label' => 'Model', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. iPhone 13 Pro'],
                    'colour' => ['label' => 'Colour', 'type' => 'text'],
                ],
                'Performance' => [
                    'storage_size' => ['label' => 'Storage', 'type' => 'select', 'required' => true, 'options' => ['16 GB', '32 GB', '64 GB', '128 GB', '256 GB', '512 GB', '1 TB']],
                    'ram_size' => ['label' => 'RAM', 'type' => 'select', 'suffix' => 'GB', 'options' => ['2', '3', '4', '6', '8', '12', '16']],
                    'processor' => ['label' => 'Chipset', 'type' => 'text', 'placeholder' => 'e.g. A15 Bionic, Snapdragon 8 Gen 2'],
                ],
                'Display & camera' => [
                    'screen_size' => ['label' => 'Screen size', 'type' => 'number', 'suffix' => 'inches'],
                    'main_camera' => ['label' => 'Main camera', 'type' => 'text', 'placeholder' => 'e.g. 12MP triple'],
                ],
                'Battery & network' => [
                    'battery_health' => ['label' => 'Battery health', 'type' => 'number', 'suffix' => '%'],
                    'network' => ['label' => 'Network', 'type' => 'select', 'required' => true, 'options' => ['4G', '5G']],
                    'sim' => ['label' => 'SIM', 'type' => 'select', 'options' => ['Single SIM', 'Dual SIM', 'SIM + eSIM', 'eSIM only']],
                    'network_lock' => ['label' => 'Network lock', 'type' => 'select', 'required' => true, 'options' => ['Factory unlocked', 'Carrier locked']],
                    'account_lock' => ['label' => 'iCloud / Google account removed', 'type' => 'select', 'required' => true, 'options' => $yesNo],
                    'operating_system' => ['label' => 'OS version', 'type' => 'text', 'placeholder' => 'e.g. iOS 17, Android 14'],
                ],
                'Condition details' => [
                    'charger_included' => ['label' => 'Charger included', 'type' => 'select', 'options' => ['Yes, original', 'Yes, compatible', 'No']],
                    'known_issues' => ['label' => 'Known issues / defects', 'type' => 'textarea', 'placeholder' => 'Face ID, screen replaced, cracks... write "None" if none'],
                    'warranty' => ['label' => 'Warranty', 'type' => 'text'],
                ],
            ],
        ],

        'tablet' => [
            'label' => 'Tablet',
            'keywords' => ['tablet', 'ipad', 'tab'],
            'groups' => [
                'Basics' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true],
                    'model' => ['label' => 'Model', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. iPad Air 5th gen'],
                    'colour' => ['label' => 'Colour', 'type' => 'text'],
                ],
                'Specs' => [
                    'storage_size' => ['label' => 'Storage', 'type' => 'select', 'required' => true, 'options' => ['16 GB', '32 GB', '64 GB', '128 GB', '256 GB', '512 GB', '1 TB']],
                    'ram_size' => ['label' => 'RAM', 'type' => 'select', 'suffix' => 'GB', 'options' => ['2', '3', '4', '6', '8', '12', '16']],
                    'screen_size' => ['label' => 'Screen size', 'type' => 'number', 'required' => true, 'suffix' => 'inches'],
                    'connectivity' => ['label' => 'Connectivity', 'type' => 'select', 'required' => true, 'options' => ['Wi-Fi only', 'Wi-Fi + Cellular']],
                    'battery_health' => ['label' => 'Battery health', 'type' => 'number', 'suffix' => '%'],
                    'operating_system' => ['label' => 'OS version', 'type' => 'text'],
                    'account_lock' => ['label' => 'iCloud / Google account removed', 'type' => 'select', 'required' => true, 'options' => $yesNo],
                    'stylus_support' => ['label' => 'Stylus / keyboard included', 'type' => 'text'],
                    'charger_included' => ['label' => 'Charger included', 'type' => 'select', 'options' => ['Yes, original', 'Yes, compatible', 'No']],
                    'known_issues' => ['label' => 'Known issues / defects', 'type' => 'textarea'],
                ],
            ],
        ],

        'monitor' => [
            'label' => 'Monitor / display',
            'keywords' => ['monitor', 'display', 'screen'],
            'groups' => [
                'Specs' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true],
                    'model' => ['label' => 'Model', 'type' => 'text', 'required' => true],
                    'screen_size' => ['label' => 'Screen size', 'type' => 'number', 'required' => true, 'suffix' => 'inches'],
                    'resolution' => ['label' => 'Resolution', 'type' => 'select', 'required' => true, 'options' => ['HD (1366 x 768)', 'Full HD (1920 x 1080)', '2K / QHD (2560 x 1440)', 'Ultrawide (3440 x 1440)', '4K / UHD (3840 x 2160)']],
                    'panel_type' => ['label' => 'Panel type', 'type' => 'select', 'options' => ['IPS', 'TN', 'VA', 'OLED']],
                    'refresh_rate' => ['label' => 'Refresh rate', 'type' => 'select', 'options' => ['60 Hz', '75 Hz', '100 Hz', '144 Hz', '165 Hz', '240 Hz']],
                    'response_time' => ['label' => 'Response time', 'type' => 'text', 'placeholder' => 'e.g. 1ms'],
                    'ports' => ['label' => 'Inputs', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. HDMI, DisplayPort, VGA'],
                    'cables_included' => ['label' => 'Cables included', 'type' => 'text'],
                    'known_issues' => ['label' => 'Known issues / defects', 'type' => 'textarea'],
                ],
            ],
        ],

        'storage' => [
            'label' => 'Storage drive',
            'keywords' => ['storage', 'ssd', 'hdd', 'hard-drive', 'drive', 'flash'],
            'groups' => [
                'Specs' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Samsung, WD, Crucial'],
                    'model' => ['label' => 'Model', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. 980 PRO'],
                    'capacity' => ['label' => 'Capacity', 'type' => 'select', 'required' => true, 'options' => ['32 GB', '64 GB', '128 GB', '256 GB', '500 GB', '512 GB', '1 TB', '2 TB', '4 TB', '8 TB']],
                    'drive_type' => ['label' => 'Drive type', 'type' => 'select', 'required' => true, 'options' => ['NVMe SSD', 'SATA SSD', 'HDD', 'External SSD', 'External HDD', 'USB flash drive', 'Memory card']],
                    'form_factor' => ['label' => 'Form factor', 'type' => 'select', 'required' => true, 'options' => ['M.2 2280', 'M.2 2242', 'M.2 2230', '2.5"', '3.5"', 'Portable', 'USB stick', 'SD / microSD']],
                    'interface' => ['label' => 'Interface', 'type' => 'select', 'required' => true, 'options' => ['PCIe Gen 3', 'PCIe Gen 4', 'PCIe Gen 5', 'SATA III', 'USB 3.2', 'USB-C', 'Thunderbolt']],
                    'read_speed' => ['label' => 'Read speed', 'type' => 'number', 'suffix' => 'MB/s'],
                    'write_speed' => ['label' => 'Write speed', 'type' => 'number', 'suffix' => 'MB/s'],
                    'health' => ['label' => 'Drive health', 'type' => 'number', 'suffix' => '%', 'placeholder' => 'From CrystalDiskInfo, if used'],
                    'warranty' => ['label' => 'Warranty', 'type' => 'text'],
                ],
            ],
        ],

        'memory' => [
            'label' => 'Memory (RAM)',
            'keywords' => ['memory', 'ram', 'ddr'],
            'groups' => [
                'Specs' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Corsair, Kingston'],
                    'model' => ['label' => 'Model', 'type' => 'text', 'placeholder' => 'e.g. Vengeance LPX'],
                    'capacity' => ['label' => 'Total capacity', 'type' => 'select', 'required' => true, 'options' => ['2 GB', '4 GB', '8 GB', '16 GB', '32 GB', '64 GB', '128 GB']],
                    'kit_configuration' => ['label' => 'Kit', 'type' => 'select', 'options' => ['1 x stick', '2 x sticks', '4 x sticks']],
                    'memory_type' => ['label' => 'Memory type', 'type' => 'select', 'required' => true, 'options' => ['DDR3', 'DDR3L', 'DDR4', 'DDR5']],
                    'form_factor' => ['label' => 'Form factor', 'type' => 'select', 'required' => true, 'options' => ['DIMM (desktop)', 'SO-DIMM (laptop)']],
                    'speed' => ['label' => 'Speed', 'type' => 'number', 'required' => true, 'suffix' => 'MHz', 'placeholder' => 'e.g. 3200'],
                    'cas_latency' => ['label' => 'CAS latency', 'type' => 'text', 'placeholder' => 'e.g. CL16'],
                    'voltage' => ['label' => 'Voltage', 'type' => 'text', 'placeholder' => 'e.g. 1.35V'],
                    'warranty' => ['label' => 'Warranty', 'type' => 'text'],
                ],
            ],
        ],

        'networking' => [
            'label' => 'Networking / Wi-Fi',
            'keywords' => ['wifi', 'wi-fi', 'network', 'router', 'modem', 'mifi'],
            'groups' => [
                'Specs' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. TP-Link, Huawei'],
                    'model' => ['label' => 'Model', 'type' => 'text', 'required' => true],
                    'device_type' => ['label' => 'Device type', 'type' => 'select', 'required' => true, 'options' => ['Router', 'Mesh system', 'Range extender', 'USB Wi-Fi adapter', 'PCIe Wi-Fi card', 'MiFi / 4G router', 'Switch', 'Access point']],
                    'wifi_standard' => ['label' => 'Wi-Fi standard', 'type' => 'select', 'options' => ['Wi-Fi 4 (802.11n)', 'Wi-Fi 5 (802.11ac)', 'Wi-Fi 6 (802.11ax)', 'Wi-Fi 6E', 'Wi-Fi 7']],
                    'bands' => ['label' => 'Bands', 'type' => 'select', 'options' => ['Single band (2.4 GHz)', 'Dual band', 'Tri band']],
                    'max_speed' => ['label' => 'Max speed', 'type' => 'text', 'placeholder' => 'e.g. AX1800'],
                    'sim_support' => ['label' => 'SIM / network support', 'type' => 'text', 'placeholder' => 'e.g. 4G LTE, unlocked'],
                    'ports' => ['label' => 'Ports', 'type' => 'text'],
                    'warranty' => ['label' => 'Warranty', 'type' => 'text'],
                ],
            ],
        ],

        'accessory' => [
            'label' => 'Accessory',
            'keywords' => ['accessor', 'cable', 'charger', 'mouse', 'keyboard', 'headphone', 'bag', 'case'],
            'groups' => [
                'Specs' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true],
                    'model' => ['label' => 'Model', 'type' => 'text'],
                    'accessory_type' => ['label' => 'Accessory type', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Charger, mouse, laptop bag'],
                    'compatibility' => ['label' => 'Compatible with', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. HP laptops with 65W USB-C'],
                    'connectivity' => ['label' => 'Connectivity', 'type' => 'select', 'options' => ['Wired', 'Wireless', 'Bluetooth', 'Not applicable']],
                    'colour' => ['label' => 'Colour', 'type' => 'text'],
                    'warranty' => ['label' => 'Warranty', 'type' => 'text'],
                ],
            ],
        ],

        'general' => [
            'label' => 'General',
            'keywords' => [],
            'groups' => [
                'Specs' => [
                    'brand' => ['label' => 'Brand', 'type' => 'text', 'required' => true],
                    'model' => ['label' => 'Model', 'type' => 'text'],
                    'warranty' => ['label' => 'Warranty', 'type' => 'text'],
                ],
            ],
        ],
    ],
];
