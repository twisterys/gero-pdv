<?php

namespace Database\Factories;

use App\Models\Achat;
use App\Models\AchatLigne;
use App\Models\Article;
use App\Models\Fournisseur;
use App\Models\Template;
use App\Services\PaiementService;
use App\Services\ReferenceService;
use App\Services\StockService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achat> */
class AchatFactory extends Factory
{
    /**
     * Default tax rate used for purchase items
     */
    private const DEFAULT_TAX_RATE = 20;

    /**
     * Default store ID
     */
    private const DEFAULT_STORE_ID = 1;

    /**
     * Default unit ID
     */
    private const DEFAULT_UNIT_ID = 1;

    /**
     * Default user ID for created/controlled by fields
     */
    private const DEFAULT_USER_ID = 1;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Achat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(Achat::TYPES);

        // Check if there are any suppliers in the database
        $fournisseurs = Fournisseur::get();
        if ($fournisseurs->isEmpty()) {
            // Create some suppliers if none exist
            $fournisseurs = Fournisseur::factory(5)->create();
        }

        return [
            'reference' => ReferenceService::generateReference($type),
            'reference_externe' => 'REF-' . $this->faker->unique()->randomNumber(4),
            'objet' => $this->faker->sentence(),
            'statut' => 'validé',
            'type_document' => $type,
            'date_expiration' => Carbon::now()->addMonths($this->faker->numberBetween(4, 15)),
            'date_emission' => Carbon::now(),
            'fichier_document' => null,
            'note' => $this->faker->paragraph(),
            'statut_paiement' => 'non_paye',
            'piece_jointe' => null,
            'total_ht' => 0,
            'total_tva' => 0,
            'total_reduction' => 0,
            'total_ttc' => 0,
            'debit' => 0,
            'credit' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_by' => self::DEFAULT_USER_ID,
            'magasin_id' => self::DEFAULT_STORE_ID,
            'is_controled' => false,
            'controled_at' => null,
            'controled_by' => null,
            'fournisseur_id' => $fournisseurs->random()->id,
            'template_id' => 1,
        ];
    }

    /**
     * Generate purchase lines and update purchase totals.
     *
     * @return $this
     */
    public function withLignes()
    {
        $this->afterMaking(function (Achat $achat) {
            $lineCount = $this->faker->numberBetween(1, 10);

            // Inline implementation of createPurchaseLines
            $totals = [
                'ht' => 0,
                'tva' => 0,
                'reduction' => 0,
                'ttc' => 0
            ];

            // Check if there are any articles in the database
            $articles = Article::get();
            if ($articles->isEmpty()) {
                // Create some articles if none exist
                $articles = Article::factory(10)->create();
            }

            for ($i = 0; $i < $lineCount; $i++) {
                $article = $articles->random();
                $quantity = $this->faker->numberBetween(1, 10);
                $reduction = $article->prix_achat * $this->faker->numberBetween(0, 90) / 100;

                $ligne = $this->createPurchaseLine($achat, $article, $quantity, $reduction, $i);
                StockService::stock_entre(
                    $article->id,
                    $quantity,
                    Carbon::now()->format('Y-m-d'),
                    AchatLigne::class,
                    $ligne->id
                );

                // Update purchase totals
                $totals['ht'] += $article->prix_achat * $quantity;
                $totals['tva'] += $this->calculateTvaAmount($article->prix_achat, $reduction, self::DEFAULT_TAX_RATE, $quantity);
                $totals['reduction'] += $reduction * $quantity;
                $totals['ttc'] += $this->calculateTotalTtc($article->prix_achat, $reduction, self::DEFAULT_TAX_RATE, $quantity);
            }

            $achat->update([
                'total_ht' => $totals['ht'],
                'total_tva' => $totals['tva'],
                'total_reduction' => $totals['reduction'],
                'total_ttc' => $totals['ttc'],
                'debit' => $totals['ttc'],
                'credit' => 0
            ]);

            // Comment this out temporarily to isolate the issue
            // $this->createRandomPayment($achat);
        });

        return $this;
    }

    /**
     * Create a single purchase line.
     *
     * @param Achat $achat
     * @param Article $article
     * @param int $quantity
     * @param float $reduction
     * @param int $position
     * @return AchatLigne
     */
    private function createPurchaseLine(Achat $achat, Article $article, int $quantity, float $reduction, int $position): AchatLigne
    {
        return AchatLigne::create([
            'article_id' => $article->id,
            'achat_id' => $achat->id,
            'position' => $position,
            'quantite' => $quantity,
            'ht' => $article->prix_achat,
            'taxe' => self::DEFAULT_TAX_RATE,
            'mode_reduction' => 'fixe',
            'reduction' => $reduction,
            'total_ttc' => $this->calculateTotalTtc($article->prix_achat, $reduction, self::DEFAULT_TAX_RATE, $quantity),
            'nom_article' => $article->designation,
            'magasin_id' => self::DEFAULT_STORE_ID,
            'unite_id' => self::DEFAULT_UNIT_ID,
        ]);
    }

    /**
     * Create a random payment for the purchase.
     *
     * @param Achat $achat
     * @return void
     */
    private function createRandomPayment(Achat $achat): void
    {
        $paymentAmount = $achat->total_ttc * $this->faker->numberBetween(1, 100) / 100;
        $paymentData = [
            'i_compte_id' => 1,
            'i_method_key' => 'especes',
            'i_montant' => $paymentAmount,
            'i_date_paiement' => Carbon::now()->format('d/m/Y'), // Add missing required parameter
        ];

        try {
            // Mock any dependencies or use a separate mock service for testing
            // Instead of directly calling PaiementService::add_paiement

            // For testing purposes, let's update just the purchase status
            $achat->update([
                'statut_paiement' => 'partiellement_paye',
                'debit' => $achat->debit - $paymentAmount,
                'credit' => $achat->credit + $paymentAmount
            ]);

            // Instead of this:
            // PaiementService::add_paiement(Achat::class, $achat->id, $paymentData, self::DEFAULT_USER_ID);
        } catch (\Exception $e) {
            // Log the error instead of dd()
            // Log::error('Failed to create payment: ' . $e->getMessage());
        }
    }

    /**
     * Calculate TVA amount based on base price, reduction, tax rate and quantity.
     *
     * @param float $ht Base price
     * @param float $reduction Reduction amount
     * @param float $tva Tax rate percentage
     * @param float $quantite Quantity
     * @return float
     */
    private function calculateTvaAmount(float $ht, float $reduction, float $tva, float $quantite): float
    {
        $baseAmount = ($ht - $reduction);
        $taxAmount = $baseAmount * ($tva / 100);
        return (float)number_format(round($taxAmount, 10) * $quantite, 2, '.', '');
    }

    /**
     * Calculate total price including tax based on base price, reduction, tax rate and quantity.
     *
     * @param float $ht Base price
     * @param float $reduction Reduction amount
     * @param float $tva Tax rate percentage
     * @param float $quantite Quantity
     * @return float
     */
    private function calculateTotalTtc(float $ht, float $reduction, float $tva, float $quantite): float
    {
        $baseAmount = ($ht - $reduction);
        $withTax = $baseAmount + ($baseAmount * ($tva / 100));
        return (float)number_format(round($withTax, 10) * $quantite, 2, '.', '');
    }
}
