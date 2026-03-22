<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => $this->faker->regexify('[A-Z0-9]{3}-[A-Z0-9]{4}'),
            'item_name' => $this->faker->words(3, true),
            'cost' => $this->faker->randomFloat(2, 5, 100),
            'selling_price' => $this->faker->randomFloat(2, 10, 200),
        ];
    }
}
