<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'ingredients' => implode(',', $this->faker->words(5)),
            'prep_time' => $this->faker->numberBetween(5, 60),
            'cook_time' => $this->faker->numberBetween(10, 120),
            'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'description' => $this->faker->sentence(),
        ];
    }
}
