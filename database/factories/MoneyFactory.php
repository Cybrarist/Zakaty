<?php

namespace Database\Factories;

use App\Enums\MoneyTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Money>
 */
class MoneyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'currency_id' => $this->faker->numberBetween(1, 1),
            'amount' => $this->faker->numberBetween(100, 100),
            'usd_amount' => $this->faker->numberBetween(100, 100),
            'type' => $this->faker->randomElement([MoneyTypeEnum::Cash->value]),
            'user_id' => $this->faker->numberBetween(1, 1),
        ];
    }
}
