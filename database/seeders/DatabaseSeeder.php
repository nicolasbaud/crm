<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('fr_FR');
        \App\Models\Customer::factory(10)->create();

        /*\App\Models\Customer::factory(10)->create([
             'name' => 'Test User',
             'email' => 'test@example.com',
            'name' => $faker->name(),
            'email' => $faker->email(),
            'birthday' => $faker->dateTimeThisCentury(),
            'gender' => 'male',
            'phone' => $faker->mobileNumber(),
            'street' => $faker->secondaryAddress(),
            'city' => $faker->departmentName(),
            'state' => $faker->departmentName(),
            'zip' => $faker->departmentNumber(),
            'country' => 'fr',
        ]);*/
    }
}
