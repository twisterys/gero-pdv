<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Famille;
use App\Models\Unite;
use App\Services\ReferenceService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $pv = $this->faker->randomFloat(2,1,1000);
        $pa = $this->faker->randomFloat(2,1,1000);
        $prix_revient = $pv - $pa;
        return [
            'reference' => ReferenceService::generateReference('art'),
            'designation' => $this->faker->word(),
            'prix_vente' => $pv,
            'prix_achat' => $pa,
            'unite_id' => 1,
            'famille_id' => null,
            'description' => $this->faker->paragraph(),
            'prix_revient'=>  $prix_revient,
            'stockable' => true,
            'numero_serie' => $this->faker->numerify($this->faker->word()),
            'code_barre' => $this->faker->uuid(),
            'taxe' => 20.0,
        ];
    }
}
