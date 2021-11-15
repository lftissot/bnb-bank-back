<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->paragraph(1),
            'amount' => $this->faker->randomFloat(2,-100,100),
            'type' => $this->faker->randomFloat(0,0,1),
            'status' => $this->faker->randomFloat(0,0,1),
            'executed_on' => $this->faker->dateTime(),
        ];
    }
}
