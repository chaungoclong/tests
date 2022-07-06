<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->numerify('role###'),
            'slug' => $this->faker->unique()->numerify('role_###'),
            'description' => $this->faker->text()
        ];
    }
}
