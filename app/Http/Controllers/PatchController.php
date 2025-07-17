<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Article;
use App\Models\Compte;
use App\Models\Dashboard;
use App\Models\DemandeTransfert;
use App\Models\DemandeTransfertLigne;
use App\Models\Depense;
use App\Models\Magasin;
use App\Models\Paiement;
use App\Models\Stock;
use App\Models\TransactionStock;
use App\Models\User;
use App\Models\Vente;
use App\Services\GlobalService;
use App\Services\ModuleService;
use App\Services\PaiementService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Event\Runtime\PHP;
use function Symfony\Component\Translation\t;

class PatchController extends Controller
{
    public function apply_patch($ref)
    {
        switch ($ref) {
            case  'pos_expense_without_payments' :
                $this->pos_expense_without_payments();
                break;
            case  'recount_stock_from_mvt' :
                $this->recount_stock_from_mvt();
                break;
            case 'pos_stock_recalc':
                $this->pos_stock_recalc();
                break;
            case 'dashboards_by_user':
                $this->dashboards_by_user();
                break;
            default :
                echo "No Patch is applied";
        }
    }

    public function recount_stock_from_mvt()
    {
        $articles = Article::all();
        echo "Ce Patch est faite pour recalculer le stock </br>";
        foreach ($articles as $article) {
            $stock = Stock::where('article_id', $article->id)->first();
            $quantite = TransactionStock::where('article_id', $article->id)->sum('qte_entree') - TransactionStock::where('article_id', $article->id)->sum('qte_sortir');
            if ($stock) {
                echo "Stock Article " . $article->reference . " est mettre à jour avec un stock de " . $quantite . " </br>";
                $stock->update(
                    [
                        'quantite' => $quantite
                    ]
                );
            } else {
                echo "Stock Article " . $article->reference . " est crée avec un stock de " . $quantite . " </br>";

                Stock::create([
                    'article_id' => $article->id,
                    'quantite' => $quantite
                ]);
            }
        }
    }

    public function pos_expense_without_payments()
    {
        echo "Ce Patch est faite pour fixer les anomalies de données généré par la bug de création de dépense pos sans paiement:</br>";
        $depenses = Depense::where('statut_paiement', 'paye')->get();
        {
            $total = 0;
            $changed = 0;
            foreach ($depenses as $expense) {
                $total++;
                $count = Paiement::where('payable_type', 'App\\Models\\Depense')->where('payable_id', $expense->id)->count();
                if ($count == 0) {
                    $changed++;
                    $pay = new Paiement();
                    $pay->payable_id = $expense->id;
                    $pay->payable_type = Depense::class;
                    $pay->compte_id = 1;
                    $pay->methode_paiement_key = 'especes';
                    $pay->comptable = 1;
                    $pay->date_paiement = $expense->date_operation;
                    $pay->decaisser = $expense->montant;
                    $pay->encaisser = 0;
                    $pay->pos_session_id = $expense->pos_session_id;
                    $pay->magasin_id = 2;
                    $pay->created_by = 3;
                    $pay->save();
                    echo "Paiement ajouté pour dépense id = " . $expense->id . "(" . $expense->reference . ")</br>";
                }
            }
            echo "TOTAL des dépense traités : " . $total . '</br>';
            echo "TOTAL des paiements crées :" . $changed . '</br>';
        }
    }

    public function pos_stock_recalc()
    {
        echo "Ce Patch est faite pour fixer les anomalies de données généré par la bug de stock limites check lors d'écriture de stock par pos:</br>";

        $modules = ModuleService::getModules();
        $achats_processed = 0;
        $ventes_processed = 0;
        $demandes_processed = 0;


        $ventes = Vente::whereBetween('date_document', [Carbon::create(2024, 8, 6)->toDateString(), Carbon::today()->toDateString()])->where('statut', 'validé')->get();
        $achats = Achat::whereBetween('created_at', [Carbon::create(2024, 8, 6)->toDateString(), Carbon::today()->toDateString()])->where('statut', 'validé')->get();
        $demandes_de_transferts = DemandeTransfert::whereBetween('created_at', [Carbon::create(2024, 8, 6)->toDateString(), Carbon::today()->toDateString()])->where('statut', 'Livrée')->get();

        $articles_table = [];
        $articles = Article::all();
        $magasins = Magasin::pluck('id');
        foreach ($articles as $article) {
            $articles_table[$article->id]['reference'] = $article->reference;
            foreach ($magasins as $magasin) {
                $articles_table[$article->id][$magasin]['before'] =$article->magasin_stock($magasin);
            }
        }

        foreach ($ventes as $vente) {
            StockService::stock_revert(Vente::class, $vente->id);
            foreach ($vente->lignes as $ligne) {
                if ($ligne['article_id']) {
                    if (in_array($vente->type_document, $modules->where('action_stock', 'sortir')->pluck('type')->toArray())) {
                        StockService::stock_sortir($ligne['article_id'], $ligne->quantite, Carbon::createFromFormat('d/m/Y', $vente->date_emission)->format('Y-m-d'), Vente::class, $vente->id, $ligne->magasin_id);
                    } elseif (in_array($vente->type_document, $modules->where('action_stock', 'entrer')->pluck('type')->toArray())) {
                        StockService::stock_entre($ligne['article_id'], $ligne->quantite, Carbon::createFromFormat('d/m/Y', $vente->date_emission)->format('Y-m-d'), Vente::class, $vente->id, $ligne->magasin_id);
                    }
                }
            }
            $ventes_processed++;
        }

        foreach ($achats as $achat) {
            StockService::stock_revert(Achat::class, $achat->id);
            if (count($achat->lignes) > 0) {
                if (in_array($achat->type_document, $modules->where('action_stock', 'entrer')->pluck('type')->toArray())) {
                    foreach ($achat->lignes as $ligne) {
                        if ($ligne->article_id) {
                            StockService::stock_entre($ligne['article_id'], $ligne->quantite, Carbon::createFromFormat('d/m/Y', $achat->date_emission)->format('Y-m-d'), Achat::class, $achat->id, $ligne->magasin_id);
                        }
                    }
                } elseif (in_array($achat->type_document, $modules->where('action_stock', 'sortir')->pluck('type')->toArray())) {
                    foreach ($achat->lignes as $ligne) {
                        if ($ligne->article_id) {
                            StockService::stock_sortir($ligne['article_id'], $ligne->quantite, Carbon::createFromFormat('d/m/Y', $achat->date_emission)->format('Y-m-d'), Achat::class, $achat->id, $ligne->magasin_id);
                        }
                    }
                }
            }
            $achats_processed++;
        }

        foreach ($demandes_de_transferts as $demandes_de_transfert) {
            foreach ($demandes_de_transfert->lignes as $ligne) {
                TransactionStock::where('stockable_type', DemandeTransfert::class)->where('stockable_id', $demandes_de_transfert->id)->delete();
                StockService::stock_sortir($ligne->article_id, $ligne->quantite_livre, now()->toDateString(), DemandeTransfert::class, $demandes_de_transfert->id, $demandes_de_transfert->magasin_sortie_id);
            }
            $demandes_processed++;
        }

        echo "achats traites:" . $achats_processed . '/' . $achats->count();
        echo "<br>";
        echo "demandes traites :" . $demandes_processed . '/' . $demandes_de_transferts->count();
        echo "<br>";
        echo "vente traites :" . $ventes_processed . '/' . $ventes->count();
        echo "<br>";

        echo "====================================================================================================================================================================================================================================================================================================================================================";
        echo "<br>";
        echo "<table>";
        echo "<tr>";
        echo "<th>Article</th>";
        foreach ($magasins as $magasin) {
            echo "<th>" . $magasin . "</th>";
        }
        echo "</tr>";
        foreach ($articles as $article) {
            foreach ($magasins as $magasin) {
                $articles_table[$article->id][$magasin]['after'] = $article->magasin_stock($magasin);
            }
        }
        foreach ($articles_table as $article) {
            echo "<tr>";
            echo "<th>" . $article['reference'] . "</th>";
            foreach ($magasins as $magasin) {
                $style = $article[$magasin]['before']!= $article[$magasin]['after'] ?  "style='color:red;'" : null ;
                echo "<th ".$style." >" . $article[$magasin]['before'] . " - " . $article[$magasin]['after'] . "</th>";
            }
            echo "</tr>";
        }
        echo "</table>";


    }

    private function dashboards_by_user()
    {
        $dashboard_function_name = GlobalService::get_all_globals()->dashboard;
        $dashboard = Dashboard::where('function_name',$dashboard_function_name)->first();
        if (!$dashboard){
            $dashboard = Dashboard::first();
        }
        echo"<table><tr><td>User</td><td>Dashboard</td></tr>";
        foreach (User::all() as $user) {
            $user->dashboards()->sync([$dashboard->id]);
            echo "<tr><td>".$user->name."</td><td>".$dashboard->name."</td></tr>";
        }
        echo "<br> Patch Done";
    }

}
