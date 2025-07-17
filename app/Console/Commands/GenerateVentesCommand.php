<?php

namespace App\Console\Commands;

use App\Services\ReferenceService;
use App\Models\Client;
use App\Models\Unite;
use App\Models\User;
use App\Models\Vente;
use App\Models\VenteLigne;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Console\Command;
use App\Models\Article;

class GenerateVentesCommand extends Command
{
    protected $signature = 'generate:ventes {type} {count=1} {exercice=null}';

    protected $description = 'Command description';

    public function handle(): void
    {
        $count = $this->argument('count');
        $bar = $this->output->createProgressBar($count);
        $client = Client::first();

        if (!$client) {
            $this->error('Il ya aucun client.');
            return;
        }
        $exercice = $this->argument('exercice') ?? now()->format('Y');
        $this->line($exercice);
        $this->generer_vente($bar, $count,$exercice);
        $bar->finish();
        $this->info(($this->argument('count')) . ' vente generé au nom de ' . $client->nom);
    }

    function calculate_ttc(float $ht, float $reduction, float $tva, float $quantite): string
    {
        $ht = round($ht - $reduction, 2);
        $tva = (1 + $tva / 100);
        $ttc = round($ht * $tva, 2) * $quantite;
        return round($ttc, 2);
    }

    function calculate_tva_amount(float $ht, float $reduction, float $tva, float $quantite): float
    {
        return +number_format(round(($ht - $reduction) * ($tva / 100), 10) * $quantite, 2, '.', '');
    }

    function generer_vente($bar, $count, $exercice)
    {
        $factory = Factory::create('fr');
        for ($i = 1; $i <= $count; $i++) {
            $vente_reference = ReferenceService::generateReference($this->argument('type'),Carbon::create($exercice),$exercice);
            $o_vente = new Vente();
            $o_vente->reference = $vente_reference;
            $o_vente->client_id = Client::inRandomOrder()->first()->id;
            $o_vente->statut = "validé";
            $o_vente->note = 'Ce document est generé pour un test';
            $o_vente->date_document = Carbon::create($exercice,rand(1,12))->addDays(rand(1,30))->toDateString();
            $o_vente->date_emission = $o_vente->date_document;
            $o_vente->date_expiration = Carbon::create($exercice,rand(1,12))->addDays(rand(1,30))->addDays(15)->toDateString();
            $o_vente->type_document = $this->argument('type');
            $o_vente->statut_paiement = 'non_paye';
            $o_vente->created_by = User::first()->id;
            $o_vente->save();
            ReferenceService::incrementCompteur($this->argument('type'),$exercice);
            $vente_ht = 0;
            $vente_ttc = 0;
            $vente_tva = 0;
            $vente_reduction = 0;
            for ($j = 1; $j <= 8; $j++) { // You can change the number of items as needed
                $o_ligne = new VenteLigne();
                $o_ligne->vente_id = $o_vente->id;
                // Randomly select an article
                $random_article = Article::inRandomOrder()->first();
                $o_ligne->article_id = $random_article->id; // Assigning the randomly selected article's ID to vente ligne
                $o_ligne->unit_id = Unite::first()->id;
                $o_ligne->mode_reduction = 'fixe';
                $o_ligne->nom_article = $random_article->designation;
                $o_ligne->description = $factory->sentence(9);
                $o_ligne->ht = $random_article->prix_vente ?? $factory->randomFloat(2, 1, 999.99);
                $o_ligne->revient = null;
                $o_ligne->quantite = $factory->randomNumber(2) + 1;
                $o_ligne->taxe = $random_article->taxe ?? $factory->randomElement([0, 20]);
                $o_ligne->total_ttc = $this->calculate_ttc($o_ligne->ht, 0, $o_ligne->taxe, $o_ligne->quantite);
                $o_ligne->position = $j - 1;
                $o_ligne->save();

                $vente_ht += ($o_ligne->ht) * $o_ligne->quantite;
                $vente_reduction += 0 * $o_ligne->quantite;
                $vente_tva += $this->calculate_tva_amount($o_ligne->ht, 0, $o_ligne->taxe, $o_ligne->quantite);
                $vente_ttc += $o_ligne->total_ttc;
            }
            $o_vente->update([
                'total_ht' => $vente_ht,
                'total_tva' => $vente_tva,
                'total_reduction' => $vente_reduction,
                'total_ttc' => $vente_ttc,
                'solde' => $vente_ttc,
            ]);
            $bar->advance();
            $bar->display();
            $this->line(' <fg=green>' . $bar->getEstimated() . 'ms </>');
        }
    }
}
