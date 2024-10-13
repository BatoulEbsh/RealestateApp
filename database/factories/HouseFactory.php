<?php

namespace Database\Factories;

use App\Models\House;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class HouseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = House::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number_of_rooms' => $this->faker->randomNumber(0),
            'number_of_bathroom' => $this->faker->randomNumber(0),
            'number_of_balcony' => $this->faker->randomNumber(0),
            'description' => $this->faker->sentence(15),
            'direction' => $this->faker->text,
            'property_id' => \App\Models\Property::factory(),
        ];
    }
}
