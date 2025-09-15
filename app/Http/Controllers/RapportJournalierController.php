<?php

namespace App\Http\Controllers;

use App\Models\Magasin;
use App\Models\Paiement;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Services\PosService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RapportJournalierController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date') ?: Carbon::today()->format('Y-m-d');
        $magasinId = $request->get('magasin_id') ?: (int)Magasin::query()->value('id');
        $magasins = Magasin::all(['id', 'nom']);

        $data = $this->buildReports($date, (int)$magasinId);
        return view('rapports.journalier', compact('date', 'magasins', 'magasinId') + $data);
    }

    public function filtrer(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
            'magasin_id' => 'required|exists:magasins,id',
        ]);

        $date = $request->input('date');
        $magasinId = (int)$request->input('magasin_id');
        $magasins = Magasin::all(['id', 'nom']);

        $data = $this->buildReports($date, $magasinId);
        return view('rapports.journalier', compact('date', 'magasins', 'magasinId') + $data);
    }

    // Endpoints AJAX optionnels
    public function ac(Request $request)
    {
        return response()->json($this->computeAC($request));
    }

    public function af(Request $request)
    {
        return response()->json($this->computeAF($request));
    }

    public function cr(Request $request)
    {
        return response()->json($this->computeCR($request));
    }

    public function tr(Request $request)
    {
        return response()->json($this->computeTR($request));
    }

    private function buildReports(string $date, int $magasin_id): array
    {
        $req = new Request(['date' => $date, 'magasin_id' => $magasin_id]);
        return [
            'ac' => $this->computeAC($req),
            'af' => $this->computeAF($req),
            'cr' => $this->computeCR($req),
            'tr' => $this->computeTR($req),
        ];
    }

    // 1) Vente Client / Article (calqué sur POS Parfums)
    private function computeAC(Request $request): array
    {
        $date = $request->get('date') ?: Carbon::today()->format('Y-m-d');
        $magasinId = (int) $request->get('magasin_id');
        $type_document = PosService::getValue('type_vente') ?? 'bc';

        // --- Step 1: Fetch sales data (no paiements join to avoid duplication) ---
        $salesData = VenteLigne::whereHas('vente', function ($q) use ($magasinId, $date, $type_document) {
            $q->where('magasin_id', $magasinId)
                ->where('date_document', '=', $date)
                ->where('type_document', $type_document)
                ->whereNotNull('pos_session_id');
        })
            ->join('articles', 'articles.id', '=', 'vente_lignes.article_id')
            ->join('ventes', 'ventes.id', '=', 'vente_lignes.vente_id')
            ->join('clients', 'clients.id', '=', 'ventes.client_id')
            ->select(
                'clients.nom',
                'clients.id as client_id',
                'articles.designation',
                'article_id',
                DB::raw('SUM(vente_lignes.quantite) as quantite'),
                DB::raw('SUM(vente_lignes.total_ttc) as total_ttc')
            )
            ->groupBy('clients.id', 'clients.nom', 'article_id', 'articles.designation')
            ->get();

        $clients = [];
        $articles = [];
        $matrix = [];
        $totals = [];

        // --- Step 2: Process sales data ---
        foreach ($salesData as $r) {
            $c = $r->nom;
            $a = $r->designation;

            if (!isset($clients[$c])) {
                $clients[$c] = true;
                $totals[$c] = ['total_ttc' => 0, 'total_paye' => 0, 'total_creance' => 0];
            }

            if (!isset($articles[$a])) {
                $articles[$a] = true;
            }

            $matrix[$c][$a] = [
                'quantite' => floor($r->quantite) == $r->quantite ? (int) $r->quantite : (float) $r->quantite,
                'total_ttc' => (float) $r->total_ttc,
            ];

            $totals[$c]['total_ttc'] += (float) $r->total_ttc;
        }

        // --- Step 3: Aggregate payments separately ---
        $clientPayments = DB::table('ventes')
            ->join('clients', 'clients.id', '=', 'ventes.client_id')
            ->leftJoin('paiements', function ($join) {
                $join->on('paiements.payable_id', '=', 'ventes.id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('ventes.magasin_id', $magasinId)
            ->where('ventes.date_document', '=', $date)
            ->where('ventes.type_document', $type_document)
            ->whereNotNull('ventes.pos_session_id')
            ->select('clients.nom', DB::raw('COALESCE(SUM(paiements.encaisser), 0) as total_paye'))
            ->groupBy('clients.id', 'clients.nom')
            ->get();

        foreach ($clientPayments as $p) {
            if (isset($totals[$p->nom])) {
                $totals[$p->nom]['total_paye'] = (float) $p->total_paye;
            }
        }

        // --- Step 4: Compute créances ---
        foreach ($totals as $c => &$t) {
            $t['total_creance'] = $t['total_ttc'] - $t['total_paye'];
        }
        unset($t);

        // --- Step 5: Grand totals ---
        $grand_total_ttc = array_sum(array_column($totals, 'total_ttc'));
        $grand_total_paye = array_sum(array_column($totals, 'total_paye'));
        $grand_total_creance = array_sum(array_column($totals, 'total_creance'));

        return [
            'clients' => array_keys($clients),
            'articles' => array_keys($articles),
            'data' => $matrix,
            'client_totals' => $totals,
            'totals' => [
                'total_ttc' => $grand_total_ttc,
                'total_paye' => $grand_total_paye,
                'total_creance' => $grand_total_creance,
            ],
        ];
    }

    // 2) Article / Fournisseur (adaptez selon vos relations)
    private function computeAF(Request $request): array
    {
        $date = $request->get('date') ?: Carbon::today()->format('Y-m-d');
        $magasinId = (int)$request->get('magasin_id');

        $rapport = DB::table('achat_lignes')
            ->join('achats', 'achats.id', '=', 'achat_lignes.achat_id')
            ->join('articles', 'articles.id', '=', 'achat_lignes.article_id')
            ->join('fournisseurs', 'fournisseurs.id', '=', 'achats.fournisseur_id')
            ->leftJoin('paiements', function ($join) {
                $join->on('paiements.payable_id', '=', 'achats.id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Achat');
            })
            ->where('achats.magasin_id', $magasinId)
            ->where('achats.date_emission', '=', $date)
            ->select(
                'fournisseurs.nom',
                'fournisseurs.id as fournisseur_id',
                'articles.designation',
                'achat_lignes.article_id',
                DB::raw('SUM(achat_lignes.quantite) as quantite'),
                DB::raw('SUM(achat_lignes.total_ttc) as total_ttc'),
                DB::raw('COALESCE(SUM(paiements.encaisser), 0) as montant')
            )
            ->groupBy('fournisseurs.id', 'fournisseurs.nom', 'achat_lignes.article_id', 'articles.designation')
            ->get();

        $fournisseurs = [];
        $articles = [];
        $matrix = [];

        foreach ($rapport as $item) {
            $fName = $item->nom;
            $aName = $item->designation;
            $fournisseurs[$fName] = true;
            $articles[$aName] = true;
            $matrix[$fName][$aName] = [
                'quantite' => floor($item->quantite) == $item->quantite ? (int)$item->quantite : (float)$item->quantite,
                'total_ttc' => (float)$item->total_ttc,
            ];
        }

        $fNames = array_keys($fournisseurs);
        $aNames = array_keys($articles);

        foreach ($fNames as $f) {
            foreach ($aNames as $a) {
                if (!isset($matrix[$f][$a])) {
                    $matrix[$f][$a] = ['quantite' => 0, 'total_ttc' => 0];
                }
            }
        }

        return [
            'fournisseurs' => $fNames,
            'articles' => $aNames,
            'data' => $matrix,
        ];
    }

    // 3) Paiements / Crédits (simplifié)
    private function computeCR(Request $request): array
    {
        $date = $request->get('date') ?: Carbon::today()->format('Y-m-d');
        $magasinId = (int)$request->get('magasin_id');

        $rows = Paiement::where('date_paiement', '=', $date)
            ->where('paiements.magasin_id', $magasinId)->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })->join('clients', 'ventes.client_id', '=', 'clients.id')
            ->join('magasins', 'magasins.id', '=', 'ventes.magasin_id')
            ->where('ventes.date_emission', '<', $date)
            ->join('methodes_paiement', 'methodes_paiement.key', '=', 'paiements.methode_paiement_key')
            ->select(
                'ventes.reference',
                'clients.nom as client_name',
                'magasins.nom as magasin_name',
                'methodes_paiement.nom as last_payment_method',
                'paiements.date_paiement as last_payment_date',
                'paiements.encaisser as total_paiement_today',
                'paiements.cheque_lcn_reference',
                'ventes.date_emission as sale_date',
                'ventes.is_controled',
                'ventes.total_ttc',
                'ventes.statut_paiement',
                DB::raw('ventes.total_ttc - COALESCE((SELECT SUM(encaisser) FROM paiements WHERE payable_id = ventes.id AND payable_type = "App\\\\Models\\\\Vente"), 0) as creance_amount')
            )->get();
        $array = json_decode(json_encode($rows), true);
        foreach ($array as &$r) {
            $r['statut_paiement'] = ucfirst(__('ventes.' . $r['statut_paiement']));
        }
        unset($r);

        $total_paiements = collect($rows)->sum('total_paiement_today');
        return [
            'rows' => $array,
            'total_paiements' => $total_paiements,
        ];
    }

    // 4) Trésorerie (entrées/sorties/solde du jour)
    private function computeTR(Request $request): array
    {
        $date = $request->get('date') ?: Carbon::today()->format('Y-m-d');
        $magasinId = (int)$request->get('magasin_id');

        $total_vente_jour = DB::table('ventes')
            ->where('magasin_id', $magasinId)->where('date_document', '=',$date)
            ->whereNotNull('pos_session_id')
            ->sum('total_ttc');

        $total_vente_creance = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=',$date)
            ->where('ventes.date_emission', '<', $date)
            ->where('paiements.magasin_id', $magasinId)
            ->sum('ventes.total_ttc');

        $total_vente = $total_vente_creance + $total_vente_jour;
        // Get total cash payments for the session


        $total_espece_jour = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', $date)
            ->where('ventes.date_emission', '=', $date)
            ->where('paiements.magasin_id', $magasinId)
            ->where('paiements.methode_paiement_key', 'especes')
            ->sum('paiements.encaisser');

        $total_espece_creance = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=',$date)
            ->where('ventes.date_emission', '<', $date)
            ->where('paiements.magasin_id', $magasinId)
            ->where('paiements.methode_paiement_key', 'especes')
            ->sum('paiements.encaisser');

        $total_espece = $total_espece_creance + $total_espece_jour;

        $total_cheque_jour = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=',$date )
            ->where('ventes.date_emission', '=', $date)
            ->where('paiements.magasin_id', $magasinId)
            ->where('paiements.methode_paiement_key', 'cheque')
            ->sum('paiements.encaisser');

        $total_cheque_creance = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', $date)
            ->where('ventes.date_emission', '<', $date)
            ->where('paiements.magasin_id', $magasinId)
            ->where('paiements.methode_paiement_key', 'cheque')
            ->sum('paiements.encaisser');

        $total_cheque = $total_cheque_creance + $total_cheque_jour;

        $total_lcn_jour = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', $date)
            ->where('ventes.date_emission', '=', $date)
            ->where('paiements.magasin_id', $magasinId)
            ->where('paiements.methode_paiement_key', 'lcn')
            ->sum('paiements.encaisser');

        $total_lcn_creance = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', $date)
            ->where('ventes.date_emission', '<',$date)
            ->where('paiements.magasin_id', $magasinId)
            ->where('paiements.methode_paiement_key', 'lcn')
            ->sum('paiements.encaisser');

        $total_lcn = $total_lcn_creance + $total_lcn_jour;

        // Get total expenses for the session
        $total_depenses = DB::table('depenses')
            ->where('date_operation', '=',$date)
            ->where('magasin_id', $magasinId)
            ->sum('montant');
        // Calculate remaining cash
        $reste_en_caisse = $total_espece - $total_depenses;
        return [
            'total_vente_jour' => (float)$total_vente_jour,
            'total_vente_creance' => (float)$total_vente_creance,
            'total_vente' => (float)$total_vente,
            'total_espece_jour' => (float)$total_espece_jour,
            'total_espece_creance' => (float)$total_espece_creance,
            'total_espece' => (float)$total_espece,
            'total_cheque_jour' => (float)$total_cheque_jour,
            'total_cheque_creance' => (float)$total_cheque_creance,
            'total_cheque' => (float)$total_cheque,
            'total_lcn_jour' => (float)$total_lcn_jour,
            'total_lcn_creance' => (float)$total_lcn_creance,
            'total_lcn' => (float)$total_lcn,
            'total_depenses' => (float)$total_depenses,
            'reste_en_caisse' => (float)$reste_en_caisse,

        ];
    }
}
