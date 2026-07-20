<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 500);
        $pricePerUnit = $this->faker->randomFloat(2, 5000, 20000);

        return [
            'user_id' => User::factory(),
            'product_id' => null,
            'item_name' => $this->faker->randomElement([
                'Beras IR64', 'Beras Pandan Wangi', 'Beras Setra', 'Beras Medium',
            ]),
            'quantity' => $quantity,
            'unit' => $this->faker->randomElement(['kg', 'karung']),
            'price_per_unit' => $pricePerUnit,
            'total_price' => round($quantity * $pricePerUnit, 2),
            'supplier' => $this->faker->optional()->company(),
            'purchase_date' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
