<?php

namespace Database\Factories;

use App\Enums\KaratEnum;
use App\Enums\PreciousMetalColorEnum;
use App\Enums\PreciousMetalTypeEnum;
use App\Enums\WeightEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Silver>
 */
class SilverFactory extends Factory
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
            'weight' =>1,
            'weight_unit' => $this->faker->randomElement(WeightEnum::values()),
            'color' => $this->faker->randomElement(PreciousMetalColorEnum::values()),
            'type' => $this->faker->randomElement(PreciousMetalTypeEnum::values()),
            'karat' => $this->faker->randomElement(KaratEnum::values()),
            'user_id' => $this->faker->numberBetween(1, 1),
        ];
    }
}
