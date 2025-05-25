<?php

namespace Database\Seeders;

use App\Models\PayPeriod;
use App\Models\Person;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Person::query()->create([
            'name' => 'أدمن',
            'tr_name' => 'Admin',
            'phone' => '1234567890',
        ])->user()->create([
            'username' => 'admin',
            'password' => bcrypt('123'),
            'role' => 'admin',
        ]);

        Person::query()->insert([
            [
                'name' => 'أحمد',
                'tr_name' => 'Ahmed',
                'phone' => '0555555555',
            ],
            [
                'name' => 'محمد',
                'tr_name' => 'Mehmed',
                'phone' => '0555555555',
            ],
            [
                'name' => 'علي',
                'tr_name' => 'Ali',
                'phone' => '0555555555',
            ],
            [
                'name' => 'يوسف',
                'tr_name' => 'Yusuf',
                'phone' => '0555555555',
            ],
            [
                'name' => 'عمر',
                'tr_name' => 'Omer',
                'phone' => '0555555555',
            ],
            [
                'name' => 'سليم',
                'tr_name' => 'Salim',
                'phone' => '0555555555',
            ],
        ]);

        PayPeriod::query()->create([
            'start_date' => '2025-05-15',
            'end_date' => '2025-05-31',
            'is_active' => true,
            'white_sorting_price' => 5,
            'yellow_sorting_price' => 5,
            'sorting_and_trimming_price' => 10,
            'sawing_price' => 7,
            'sorting_and_sawing_price' => 10,
        ])->workSessions()->create([
            'date' => '2025-05-15',
            'start_time' => '2025-05-15 08:00:00',
            'end_time' => '2025-05-15 17:00:00',
            'is_active' => true,
        ])->sawingMissions()->create([
            'is_started' => false,
            'is_finished' => false,
        ]);
    }
}
