<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Person::query()->create([
            'id' => 1,
            'name' => 'أدمن',
            'tr_name' => 'Admin',
            'phone' => '1234567890',
        ]);
        User::query()->create([
            'person_id' => 1,
            'username' => 'admin',
            'password' => bcrypt('123'),
            'role' => 'admin',
        ]);
    }
}
