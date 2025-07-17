<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Commercial;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CommercialFactory extends Factory
{
    protected $model = Commercial::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->name(),
            'ice' => $this->faker->randomNumber(5).$this->faker->randomNumber(5).$this->faker->randomNumber(5),
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->word(),
            'note' => $this->faker->sentence(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'commission_par_defaut' => $this->faker->randomFloat(2,1,100),
            'objectif' => $this->faker->randomFloat(2,1,10000),
            'secteur' => $this->faker->word(),
            'type_commercial' => ['externe','interne'][rand(0,1)],
        ];
    }
}
