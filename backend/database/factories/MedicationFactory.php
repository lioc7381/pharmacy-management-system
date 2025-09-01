<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medication>
 */
class MedicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'strength_form' => fake()->randomElement(['100mg Tablet', '250mg Capsule', '500mg Syrup', '10mg Ointment']),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 100),
            'current_quantity' => fake()->numberBetween(0, 200),
            'minimum_threshold' => fake()->numberBetween(10, 25),
            'category' => fake()->randomElement(['Pain Relief', 'Antibiotics', 'Vitamins', 'Cold & Flu', 'Skincare']),
            'status' => 'active',
        ];
    }
}
