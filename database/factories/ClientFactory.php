<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'forme_juridique' => array_rand(Client::getFormJuridiqueTypes()),
            'reference' => $this->faker->unique()->word(),
            'nom' => $this->faker->name(),
            'ice' => $this->faker->randomNumber(5).$this->faker->randomNumber(5).$this->faker->randomNumber(5),
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->phoneNumber(),
            'note' => $this->faker->paragraph(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'limite_de_credit' => $this->faker->randomFloat(2,1,10000),
            'adresse' => $this->faker->address(),
        ];
    }
}
