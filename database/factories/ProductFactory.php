<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Beras IR64', 'Beras Pandan Wangi', 'Beras Setra Ramos',
                'Beras Cianjur', 'Beras Premium', 'Beras Medium',
            ]) . ' ' . $this->faker->unique()->numerify('##'),
            'unit' => $this->faker->randomElement(['kg', 'karung', 'liter']),
            'current_stock' => 0,
        ];
    }
}
