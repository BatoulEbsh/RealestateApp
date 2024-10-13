<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name_ar' => $this->faker->text(255),
            'name_en' => $this->faker->text(255),
        ];
    }
}
