<?php

namespace Database\Factories;

use App\Models\Farm;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class FarmFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Farm::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number_of_rooms' => $this->faker->randomNumber(0),
            'number_of_pools' => $this->faker->randomNumber(0),
            'is_garden' => $this->faker->boolean,
            'is_bar' => $this->faker->boolean,
            'is_baby_pool' => $this->faker->boolean,
            'description' => $this->faker->sentence(15),
            'property_id' => \App\Models\Property::factory(),
        ];
    }
}
