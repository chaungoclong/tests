<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->numerify('permission###'),
            'slug' => $this->faker->unique()->numerify('permission_###'),
            'module' => $this->faker->numerify('module###'),
            'action' => $this->faker->numerify('Controller###@action###'),
            'action_name' => $this->faker->numerify('action###'),
            'description' => $this->faker->text()
        ];
    }
}
