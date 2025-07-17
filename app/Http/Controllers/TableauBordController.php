<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Cheque;
use App\Models\Compte;
use App\Models\Depense;
use App\Models\GlobalSetting;
use App\Models\Magasin;
use App\Models\Module;
use App\Models\Operation;
use App\Models\Paiement;
use App\Models\Promesse;
use App\Models\Vente;
use App\Services\GlobalService;
use App\Services\ModuleService;
use App\Services\PosService;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class TableauBordController extends Controller
{
    public function modifier(){
        $tableaux_de_bord = DB::table('dashboards')->get();
        $global_settings = GlobalSetting::first();
        $current = $global_settings->dashboard;
        $current_date = $global_settings->dashboard_date;
        return view('parametres.tableau_bord.modifier',compact('tableaux_de_bord','current','current_date'));
    }
    public function mettre_a_jour(Request $request){
        $request->validate([
            'type'=>'exists:dashboards,function_name',
            'date'=>[Rule::in(['year','week','today','month']),'required']
        ]);
        GlobalSetting::first()->update(['dashboard'=>$request->input('type'),'dashboard_date'=>$request->input('date')]);
        session()->flash('success','Tableau de bord mise à jour');
        return redirect()->route('tableau_bord.modifier');
    }

    public function liste(Request $request)
    {

        $exercice_date = session()->get('exercice');

        $range = [Carbon::now()->setYear($exercice_date)->firstOfYear(), Carbon::now()->setYear($exercice_date)->lastOfYear()];

        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');
        if (GlobalService::get_all_globals()->dashboard_date){
            switch (GlobalService::get_all_globals()->dashboard_date) {
                case "week":
                    $date_picker_start = Carbon::now()->startOfWeek()->setYear($exercice_date)->format('d/m/Y');
                    $date_picker_end = Carbon::now()->endOfWeek()->setYear($exercice_date)->format('d/m/Y');
                    $range = [Carbon::now()->setYear($exercice_date)->startOfWeek(), Carbon::now()->setYear($exercice_date)->endOfWeek()];

                    break;
                case "month":
                    $date_picker_start = Carbon::now()->startOfMonth()->setYear($exercice_date)->format('d/m/Y');
                    $date_picker_end = Carbon::now()->endOfMonth()->setYear($exercice_date)->format('d/m/Y');
                    $range = [Carbon::now()->setYear($exercice_date)->startOfMonth(), Carbon::now()->setYear($exercice_date)->endOfMonth()];

                    break;
                case "today":
                    $date_picker_start = Carbon::now()->setYear($exercice_date)->format('d/m/Y');
                    $date_picker_end = Carbon::now()->setYear($exercice_date)->format('d/m/Y');
                    $range = [Carbon::today()->setYear($exercice_date), Carbon::today()->setYear($exercice_date)];

                    break;
            }
        }
        // -------------------------------------

        if ($request->get('i_date')) {
            $date = $request->get('i_date');
            $start_date = trim(explode('-', $date)[0]);
            $end_date = count(explode('-', $date)) > 1 ? trim(explode('-', $date)[1]) : null;
            $validator = Validator::make(['start' => $start_date, 'end' => $end_date], ['start' => 'date_format:d/m/Y', 'end' => 'date_format:d/m/Y']);
            if (!$validator->fails()) {
                $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $date)[0]))->toDateString();
                $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $date)[1]))->toDateString();
                $date_picker_start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $date)[0]))->format('d/m/Y');
                $date_picker_end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $date)[1]))->format('d/m/Y');
                $range = [$start_date, $end_date];
            }
        }
        $type = auth()->user()->dashboards()->first()?->function_name ?? GlobalService::get_all_globals()->dashboard;
        return $this->$type($range, $date_picker_start, $date_picker_end,$exercice_date);
    }

    //-------$$ POS TABLEAU DE BORD $$-----------
    /**
     * @param $range
     * @param $date_picker_start
     * @param $date_picker_end
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    function pos($range, $date_picker_start, $date_picker_end)
    {
        $ca = $this->ca_pos($range);
        $totals_achat =  Achat::whereBetween('date_emission', $range)->where('statut', 'validé')
        ->select([
            DB::raw("COALESCE(SUM(CASE WHEN type_document = 'faa' THEN total_ttc ELSE 0 END),0) as ttc"),
            DB::raw("COALESCE(SUM(CASE WHEN type_document = 'faa' THEN debit ELSE 0 END),0) as debit"),
        ])->first();
        $total_depenses = Depense::whereBetween('date_operation', $range)->sum('montant');
        $magasins = Magasin::leftJoin('ventes', function($join) use ($range) {
            $join->on('magasins.id', '=', 'ventes.magasin_id')
                ->whereBetween('ventes.date_emission', $range)
                ->whereNotNull('ventes.pos_session_id');
        })
            ->select([
                DB::raw("COALESCE(SUM(CASE WHEN type_document = '".PosService::getValue('type_vente')."' THEN total_ttc ELSE 0 END),0) as total_vente"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = '".PosService::getValue('type_retour')."' THEN total_ttc ELSE 0 END),0) as total_retour"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = '".PosService::getValue('type_retour')."' THEN 1 ELSE 0 END), 0) as count_retour "),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = '".PosService::getValue('type_vente')."' THEN 1 ELSE 0 END), 0) as count_vente "),
                'magasins.id','nom'
            ])->groupBy('magasins.id')->get();
        $alerte_stock = $this->alerte_stock();

        return view('tableau_de_bord.pos', compact( 'date_picker_start', 'date_picker_end','totals_achat','total_depenses','ca','magasins','alerte_stock'));
    }
    //-------$$ STOCK TABLEAU DE BORD $$-----------

    /**
     * @param $range
     * @param $date_picker_start
     * @param $date_picker_end
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    function stock($range, $date_picker_start, $date_picker_end)
    {
        $ca_fa = $this->ca_fa($range);
        $ca_fp = $this->ca_proforma($range);
        $proforma = in_array('fp', ModuleService::getActiveModules());
        $factures_echeance = $this->factures_echeance($range);
        $factures_achats_echeance = $this->factures_achats_echeance($range);
        $alerte_stock = $this->alerte_stock();
        $devis_ventes_echeance = $this->devis_ventes_echeance($range);

        return view('tableau_de_bord.stock', compact('ca_fa','ca_fp', 'date_picker_start', 'date_picker_end', 'proforma', 'factures_echeance','alerte_stock','factures_achats_echeance', 'devis_ventes_echeance'));
    }

    //-------$$ SERVICE TABLEAU DE BORD $$-----------

    /**
     * @param $range
     * @param $date_picker_start
     * @param $date_picker_end
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    function service($range, $date_picker_start, $date_picker_end)
    {
        $ca_fa = $this->ca_fa($range);
        $ca_fp = $this->ca_proforma($range);
        $proforma = in_array('fp', ModuleService::getActiveModules());
        $factures_echeance = $this->factures_echeance($range);
        $factures_achats_echeance = $this->factures_achats_echeance($range);
        $totals_achat = $this->totals_achat($range);
        $totals_vente = $this->totals_vente($range);
        $total_depenses = Depense::whereBetween('date_operation', $range)->sum('montant');
        $devis_ventes_echeance = $this->devis_ventes_echeance($range);

        return view('tableau_de_bord.service', compact('ca_fa', 'total_depenses','ca_fp', 'date_picker_start', 'date_picker_end', 'proforma', 'factures_echeance','totals_vente','totals_achat', 'factures_achats_echeance', 'devis_ventes_echeance'));
    }

    function tva($range, $date_picker_start, $date_picker_end)
    {
        $ventes = Vente::whereBetween('date_emission', $range)->where('statut', 'validé')
            ->select([
                DB::raw("COALESCE(SUM(CASE WHEN type_document in ('fa','fp') THEN total_ttc ELSE 0 END),0) as ca"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document in ('fa','fp') THEN solde ELSE 0 END),0) as creance"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document in ('fa','fp') THEN total_tva ELSE 0 END),0) as tva"),
            ])->first();
        $ca_vente = $ventes->ca;
        $tva_vente = $ventes->tva;
        $creance_client = $ventes->creance;
        $achats =  Achat::whereBetween('date_emission', $range)->where('statut', 'validé')
            ->select([
                DB::raw("COALESCE(SUM(CASE WHEN type_document in ('faa','fpa') THEN total_ttc ELSE 0 END),0) as ca"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document in ('faa','fpa') THEN debit ELSE 0 END),0) as creance"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document in ('faa','fpa') THEN total_tva ELSE 0 END),0) as tva"),
            ])->first();
        $ca_achat = $achats->ca;
        $tva_achat = $achats->tva;
        $creance_fournisseur = $achats->creance;
        $encaissements = Paiement::whereBetween('date_paiement',$range)->sum('encaisser');
        $decessements = Paiement::whereBetween('date_paiement',$range)->sum('decaisser');
        $chart_data = $this->chartData();
        $tva_facture = Vente::whereBetween('date_emission',$range)->sum('total_tva');
        $tva_recup = Paiement::where('payable_type', Vente::class)->whereBetween('date_paiement',$range)
            ->whereHasMorph('payable', Vente::class, function ($query) {
                $query->select('id');  // Only selecting 'id' to match the morph relationship
            })
            ->join('ventes', 'paiements.payable_id', '=', 'ventes.id') // Join with the 'ventes' table
            ->selectRaw("
        COALESCE(
            SUM(
                CASE
                    WHEN ventes.type_document IN ('fa', 'fp')
                    THEN ((paiements.encaisser * 100 / ventes.total_ttc) * ventes.total_tva / 100)
                    ELSE 0
                END
            ), 0
        ) as tva
    ")
            ->first()->tva;
        $total_banque = Paiement::whereHas('compte', function ($query) {
            $query->where('type', 'banque');
        })->sum('encaisser');
        $total_caisse = Paiement::whereHas('compte', function ($query) {
            $query->where('type', 'caisse');
        })->sum('encaisser');

        return view('tableau_de_bord.tva', compact('tva_recup','total_banque','total_caisse','tva_facture','ca_vente','chart_data','creance_client','creance_fournisseur','encaissements','decessements','ca_achat', 'date_picker_start', 'date_picker_end'));
    }


    function recouverement($range,$date_picker_start,$date_picker_end,$exercice_date){
        $range = [Carbon::now()->setYear($exercice_date)->firstOfYear(), Carbon::now()->setYear($exercice_date)->lastOfYear()];
        $fa_a_valider = Vente::where('type_document','fa')->where('statut','brouillon')->whereBetween('date_emission',$range)->count();
        $fa_non_paye = Vente::where('type_document','fa')->where('statut_paiement','non_paye')->whereBetween('date_emission',$range)->count();
        $fa_echeance =  Vente::whereBetween('date_emission', $range)
            ->where('statut', 'validé')->where('type_document', 'fa')->where('statut_paiement','non_paye')
            ->whereDate('date_expiration', "<", now()->toDateString())->count();
        $promesses_a_traiter = Promesse::whereNull('statut')->count();
        $promesses_a_traiter_table =Promesse::whereNull('statut')->orderby('date')->get();
        $factures_echeance = $this->factures_echeance($range);
        $factures_achats_echeance = $this->factures_achats_echeance($range);
        $devis_ventes_echeance = $this->devis_ventes_echeance($range);

//        $promesses_a_traiter = Vente::where('type_document','fa')->whereBetween('date_emission',$range)->whereHas('promesses',function ($query){
//            $query->whereNull('statut')->where('date','<',now()->toDateString());
//        })->count();
        return view('tableau_de_bord.recouverement', compact( 'date_picker_start', 'date_picker_end','fa_a_valider','fa_echeance','fa_non_paye','promesses_a_traiter','promesses_a_traiter_table','factures_echeance','factures_achats_echeance','devis_ventes_echeance'));

    }


    /**
     * @param $range
     * @return array
     */
    #[ArrayShape([0 => "array", 'recette' => "int|mixed"])] function ca_fa($range)
    {
        $data = Vente::whereBetween('date_emission', $range)->where('statut', 'validé')
            ->select([
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fa' THEN total_ttc ELSE 0 END),0) as ca"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fa' THEN total_ht ELSE 0 END),0) as ca_ht"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fa' THEN solde ELSE 0 END),0) as creance"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fa' THEN 1 ELSE 0 END), 0) as count "),
            ])->first()->toArray();
        $recette = Paiement::with('payable')->whereHasMorph('payable', Vente::class, function ($query) {
            $query->where('type_document', 'fa')->where('statut', 'validé');
        })->whereBetween('date_paiement', $range)->sum('encaisser');
        return [...$data, 'recette' => $recette];
    }

    /**
     * @param $range
     * @return array
     */
    #[ArrayShape([0 => "array", 'recette' => "int|mixed"])] function ca_proforma($range)
    {
        $data = Vente::whereBetween('date_emission', $range)->where('statut', 'validé')
            ->select([
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fp' THEN total_ttc ELSE 0 END),0) as ca"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fp' THEN total_ht ELSE 0 END),0) as ca_ht"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fp' THEN solde ELSE 0 END),0) as creance"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fp' THEN 1 ELSE 0 END), 0) as count"),
            ])->first()->toArray();
        $recette = Paiement::with('payable')->whereHasMorph('payable', Vente::class, function ($query) {
            $query->where('type_document', 'fp')->where('statut', 'validé');
        })->whereBetween('date_paiement', $range)->sum('encaisser');
        return [...$data, 'recette' => $recette];
    }

    /**
     * @param $range
     * @return array
     */
    function totals_vente($range)
    {
        return Vente::whereBetween('date_emission', $range)->where('statut', 'validé')
            ->select([
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fa' THEN total_ht ELSE 0 END),0) as ht"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'fa' THEN total_tva ELSE 0 END),0) as tva"),
            ])->first()->toArray();

    }

    /**
     * @param $range
     * @return array
     */
    function totals_achat($range)
    {
        return Achat::whereBetween('date_emission', $range)->where('statut', 'validé')
            ->select([
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'faa' THEN total_ht ELSE 0 END),0) as ht"),
                DB::raw("COALESCE(SUM(CASE WHEN type_document = 'faa' THEN total_tva ELSE 0 END),0) as tva"),
            ])->first()->toArray();
    }

    /**
     * @param $range
     * @return mixed
     */
    function ca_pos($range){
        $type = PosService::getValue('type_vente');
        return Vente::whereBetween('date_emission', $range)->where('statut', 'validé')
            ->select([
                DB::raw("COALESCE(SUM(CASE WHEN type_document ='".$type."' THEN total_ttc ELSE 0 END),0) as ca"),
            ])->first()->ca;
    }

    /**
     * @param $date_range
     * @return Vente[]|\LaravelIdea\Helper\App\Models\_IH_Vente_C
     */
    function factures_echeance($date_range)
    {
        return Vente::where('statut', 'validé')
            ->where('solde', '>', '0')->whereIn('type_document', ModuleService::getEncaissementTypes())
            ->whereDate('date_expiration', "<", now()->toDateString())->get();
    }

    function factures_achats_echeance($date_range)
    {
        return Achat::where('statut', 'validé')
            ->where('debit', '>', '0')->whereIn('type_document', ModuleService::getDecaissementTypes())
            ->whereDate('date_expiration', "<", now()->toDateString())->get();
    }

    function devis_ventes_echeance($date_range){
        return Vente::where('type_document' ,'dv')
            ->where('statut_com' ,'envoyé')
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    function alerte_stock()
    {
        return DB::table('stocks as s')->join('articles as a', 'a.id', '=', 's.article_id')
            ->whereColumn('a.quantite_alerte', '>=', 's.quantite')
            ->select('a.reference', 'a.designation', 's.quantite', 'a.quantite_alerte')
            ->orderBy('s.quantite')->get();
    }
    /**
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function chartData()
    {
        $date = Carbon::now()->setYear(session()->get('exercice'));
        $range = [$date->firstOfYear()->toDateString(), $date->lastOfYear()->toDateString()];
        $data = [];
        $mos = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

        // -------------------------------------
        $encaissement = DB::table('ventes')->join('paiements', function (JoinClause $join) {
            $join->on('ventes.id', '=', 'paiements.payable_id')
                ->where('paiements.payable_type', '=', Vente::class);
        })->whereIN('type_document', ['fa'])
            ->whereNot('statut', 'brouillon')
            ->whereBetween('date_paiement', $range)
            ->selectRaw('EXTRACT(MONTH FROM date_paiement) as mois,Sum(paiements.encaisser) as total')
            ->groupByRaw('EXTRACT(MONTH FROM date_paiement)')->get();

        // -------------------------------------

        $achats = DB::table((new Achat())->getTable())->whereIn('type_document', ['faa'])->whereNot('statut', 'brouillon')
            ->whereBetween('date_emission', $range)
            ->selectRaw('EXTRACT(MONTH FROM date_emission) as mois,Sum(total_ttc) as total')
            ->groupByRaw('EXTRACT(MONTH FROM date_emission)')->get();

        // -------------------------------------

        $ca = DB::table((new Vente())->getTable())->whereIn('type_document', ['fa'])
            ->whereNot('statut', 'brouillon')
            ->whereBetween('date_emission', $range)
            ->selectRaw('EXTRACT(MONTH FROM date_emission) as mois,Sum(total_ttc) as total')
            ->groupByRaw('EXTRACT(MONTH FROM date_emission)')->get();

        // -------------------------------------

        $commandes = DB::table((new Vente())->getTable())
            ->where('type_document', 'bc')
            ->whereNot('statut', 'brouillon')
            ->selectRaw('EXTRACT(MONTH FROM date_emission) as mois,Sum(total_ttc) as total')
            ->groupByRaw('EXTRACT(MONTH FROM date_emission)')->get();

        // -------------------------------------

        for ($i = 1; $i <= 12; $i++) {
            $data[] = (object)[
                'y' => $mos[$i - 1],
                'encaissement' => $encaissement->where('mois', $i)->first() ? $encaissement->where('mois', $i)->first()->total : 0,
                'ca' => $ca->where('mois', $i)->first() ? $ca->where('mois', $i)->first()->total : 0,
                'achats' => $achats->where('mois', $i)->first() ? $achats->where('mois', $i)->first()->total : 0,
                'commandes' => $commandes->where('mois', $i)->first() ? $commandes->where('mois', $i)->first()->total : 0,
                'depenses' => 0,
            ];
        }

        return $data;
    }


    function audinord1($range, $date_picker_start, $date_picker_end)
    {
        $ca_fa = $this->ca_fa($range);
        $ca_fp = $this->ca_proforma($range);
     $total_depense = Depense::whereBetween('date_operation',$range)->sum('montant') ;

        /** @var  $creance_client : La créance ici contient les factures proforma aussi ne suit pas l'exercice*/
        $creance_client = Vente::whereIn('type_document',['fa','fp'])->where('statut','validé')->sum('solde');

        $nbr_facture_br = Vente::where('statut','brouillon')->whereIn('type_document',['fa','fp'])->count() ;

        $encaissements = Paiement::whereBetween('date_paiement',$range)->sum('encaisser');

        /** @var  $chart_data : présentation des ventes et achats annuelle */
        // TODO : A vérifier ce garphe
        $chart_data = $this->chartData();
        $tva_facture = Vente::whereBetween('date_emission',$range)->sum('total_tva');
        $tva_achat = Achat::whereBetween('date_emission',$range)->sum('total_tva');
        $tva_recup = $tva_facture - $tva_achat ;
        $cheque_a_encaisser = Cheque::where('type', 'encaissement')
            ->where('date_echeance', '<=', Carbon::today())
            ->where('statut', 'en_attente')
            ->count();

        $cheque_a_decaisser = Cheque::where('type', 'decaissement')
            ->where('date_echeance', '<=', Carbon::today())
            ->where('statut', 'en_attente')
            ->count();

        $cheque_encours = "Prochainement" ;
        $comptes = Compte::get() ;
        $factures_echeance = $this->factures_echeance($range);
        $proforma = in_array('fp', ModuleService::getActiveModules());


        $total_caisse = Paiement::whereHas('compte', function ($query) {
            $query->where('type', 'caisse');
        })->sum('encaisser');

        return view('tableau_de_bord.audinord1', compact('factures_echeance','cheque_encours','total_depense','nbr_facture_br','cheque_encours','tva_recup','comptes','chart_data','creance_client','encaissements', 'date_picker_start', 'date_picker_end','ca_fa','ca_fp','proforma','cheque_a_decaisser','cheque_a_encaisser'));
    }
    function audinord2($range, $date_picker_start, $date_picker_end)
    {
        return $this->audinord1($range, $date_picker_start, $date_picker_end);
    }
}
