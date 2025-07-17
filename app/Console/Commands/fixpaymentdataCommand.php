<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use App\Models\Vente;
use Illuminate\Console\Command;

class fixpaymentdataCommand extends Command
{
    protected $signature = 'fixpaiementdata';

    protected $description = 'Command description';

    public function handle(): void
    {

        $ventes  = Vente::with('paiements')->where('encaisser','>','total_ttc')
            ->get();


        foreach ($ventes as $vente) {
            $amount = 0 ;
            foreach ($vente->paiements as $paiement) {
                $amount += $paiement->encaisser;
            }
            $vente->encaisser = $amount;
            $vente->solde = $vente->total_ttc - $vente->encaisser;

            $vente->statut_paiement = $this->get_payable_statut($vente->total_ttc, $vente->encaisser, $vente->solde);
            $vente->save();
        }


    }

    public  function get_payable_statut(float|string $payable_total, float|string $montant_paye = 0, float|string $montant_impaye = 0)
    {
        if ($montant_impaye <= 0 && round($montant_paye, 2) >= round($payable_total, 2)) {
            $paiment_statut = 'paye';
        } elseif ($montant_paye > 0 && round($payable_total, 2) > round($montant_paye, 2)) {
            $paiment_statut = 'partiellement_paye';
        } else {
            $paiment_statut = 'non_paye';
        }
        return $paiment_statut;
    }
}
