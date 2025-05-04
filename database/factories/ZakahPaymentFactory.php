<?php

namespace Database\Factories;

use App\Enums\ZakahPaymentMethodEnum;
use App\Enums\ZakahPaymentStatusEnum;
use App\Enums\ZakahPaymentTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ZakahPayment>
 */
class ZakahPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(100, 10000),
            'usd_amount' => $this->faker->numberBetween(100, 10000),
            'type' => $this->faker->randomElement(ZakahPaymentTypeEnum::values()),
            'status' => $this->faker->randomElement(ZakahPaymentStatusEnum::values()),
            'payment_method' => $this->faker->randomElement(ZakahPaymentMethodEnum::values()),
            'user_id' => 1
        ];
    }
}
