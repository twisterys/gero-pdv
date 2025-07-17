<?php

namespace Database\Factories;

use App\Models\FormeJuridique;
use App\Models\Fournisseur;
use App\Services\ReferenceService;
use Illuminate\Database\Eloquent\Factories\Factory;

class FournisseurFactory extends Factory
{
    protected $model = Fournisseur::class;

    // Constants for credit limit boundaries
    private const MIN_CREDIT_LIMIT = 10000;
    private const MAX_CREDIT_LIMIT = 1000000;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Relationship data
            'forme_juridique_id' => $this->getRandomFormeJuridiqueId(),

            // Identification data
            'reference' => ReferenceService::generateReference('fr'),
            'nom' => $this->generateCompanyOrPersonName(),
            'ice' => $this->faker->unique()->swiftBicNumber(),

            // Contact information
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->unique()->phoneNumber(),
            'adresse' => $this->faker->address(),

            // Financial information
            'limite_de_credit' => $this->faker->randomFloat(2, self::MIN_CREDIT_LIMIT, self::MAX_CREDIT_LIMIT),
            'rib' => $this->faker->unique()->iban(),

            // Additional information
            'note' => $this->faker->paragraph(),
        ];
    }

    /**
     * Get a random FormeJuridique ID from the database.
     *
     * @return int
     */
    private function getRandomFormeJuridiqueId(): int
    {
        return FormeJuridique::inRandomOrder()->first()->id;
    }

    /**
     * Generate either a company name or a person name.
     *
     * @return string
     */
    private function generateCompanyOrPersonName(): string
    {
        return $this->faker->unique()->randomElement([
            $this->faker->name(),
            $this->faker->company(),
        ]);
    }
}
