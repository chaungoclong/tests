<?php

namespace Database\Factories;

use App\Models\Role;
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
            'name' => $this->faker->numerify('role_###'),
            'slug' => $this->faker->unique()->text(100),
            'parent_id' => optional(Role::inRandomOrder()->first())->id,
            'description' => $this->faker->text()
        ];
    }
}
