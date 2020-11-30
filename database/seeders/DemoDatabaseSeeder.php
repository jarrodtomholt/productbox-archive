<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use App\Models\Option;
use App\Models\Tenant;
use App\Models\Variant;
use App\Models\Category;
use App\Facades\Settings;
use Illuminate\Database\Seeder;

class DemoDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'email' => 'user@productbox.test',
            'password' => 'password',
        ]);

        $tenant = Tenant::create([
            'name' => 'Demo',
            'email' => 'demo@productbox.test',
            'phone' => '1234567890',
            'active' => true,
        ]);
        $tenant->domains()->create(['domain' => 'demo.productbox.test']);

        $tenant->run(function ($tenant) {
            Category::factory()->count(rand(5, 8))->create()->each(function ($category) {
                Item::factory()->available()->count(rand(5, 10))->create([
                    'category_id' => $category,
                ])->each(function ($item) {
                    Variant::factory()->create([
                        'item_id' => $item
                    ]);
                    Option::factory()->create([
                        'item_id' => $item
                    ]);
                });
            });

            Settings::set([
                'name' => 'Demo',
                'email' => 'demo@productbox.test',
                'phone' => '1234567890',
                'messageOfTheDay' => 'Welcome to our ProductBox demo',
                'deliveryEnabled' => false,
                'primaryColor' => '#da1074',
                'secondaryColor' => '#143642',
                'openingHours' => [
                    'monday' => ['10:00-14:00'],
                    'tuesday' => ['10:00-14:00'],
                    'wednesday' => ['10:00-14:00'],
                    'thursday' => ['10:00-14:00', '17:00-23:00'],
                    'friday' => ['10:00-14:00', '17:00-23:00'],
                    'saturday' => ['08:00-14:00', '17:00-23:00'],
                    'sunday' => ['08:00-14:00', '17:00-20:00'],
                    'exceptions' => [
                        '01-01' => [],
                        '12-25' => [],
                    ],
                ],
            ]);
        });
    }
}
