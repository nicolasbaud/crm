<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('fr_FR');

        return [
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
            'created_at' => $faker->dateTimeBetween('-1 year', '+0 week'),
        ];
    }
}
