<?php

namespace Database\Factories;

use App\Models\Market;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Market::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(15),
            'property_id' => \App\Models\Property::factory(),
        ];
    }
}
