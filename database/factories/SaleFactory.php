<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 200);
        $pricePerUnit = $this->faker->randomFloat(2, 8000, 25000);

        return [
            'user_id' => User::factory(),
            'product_id' => null,
            'item_name' => $this->faker->randomElement([
                'Beras IR64', 'Beras Pandan Wangi', 'Beras Setra', 'Beras Premium',
            ]),
            'quantity' => $quantity,
            'unit' => $this->faker->randomElement(['kg', 'karung']),
            'price_per_unit' => $pricePerUnit,
            'total_price' => round($quantity * $pricePerUnit, 2),
            'buyer' => $this->faker->optional()->name(),
            'sale_date' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
