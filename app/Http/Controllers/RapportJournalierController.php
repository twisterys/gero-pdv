<?php

namespace App\Http\Controllers;

use App\Models\Magasin;
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
        $magasinId = (int)$request->get('magasin_id');

        $rapport = VenteLigne::whereHas('vente', function ($q) use ($magasinId, $date) {
            $q->where('magasin_id', $magasinId)
                ->where('date_document', '=', $date)
                ->where('type_document', PosService::getValue('type_vente') ?? 'bc')
                ->whereNotNull('pos_session_id');
        })
            ->join('articles', 'articles.id', '=', 'vente_lignes.article_id')
            ->join('ventes', 'ventes.id', '=', 'vente_lignes.vente_id')
            ->leftJoin('paiements', function ($join) {
                $join->on('paiements.payable_id', '=', 'ventes.id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->join('clients', 'clients.id', '=', 'ventes.client_id')
            ->select(
                'clients.nom',
                'clients.id as client_id',
                'articles.designation',
                'article_id',
                DB::raw('SUM(vente_lignes.quantite) as quantite'),
                DB::raw('SUM(vente_lignes.total_ttc) as total_ttc'),
                DB::raw('COALESCE(SUM(paiements.encaisser), 0) as montant')
            )
            ->groupBy('clients.id', 'clients.nom', 'article_id', 'articles.designation')
            ->get();

        $clients = [];
        $articles = [];
        $matrix = [];
        $totals = [];
        foreach ($rapport as $r) {
            $c = $r['nom'];
            $a = $r['designation'];
            if (!isset($clients[$c])) {
                $clients[$c] = true;
                $totals[$c] = ['total_ttc' => 0, 'total_paye' => 0];
            }
            if (!isset($articles[$a])) {
                $articles[$a] = true;
            }
            $matrix[$c][$a] = [
                'quantite' => floor($r['quantite']) == $r['quantite'] ? (int)$r['quantite'] : (float)$r['quantite'],
                'total_ttc' => (float)$r['total_ttc']
            ];
            $totals[$c]['total_ttc'] += (float)$r['total_ttc'];
        }

        $clientPayments = DB::table('ventes')
            ->join('clients', 'clients.id', '=', 'ventes.client_id')
            ->leftJoin('paiements', function ($join) {
                $join->on('paiements.payable_id', '=', 'ventes.id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('ventes.magasin_id', $magasinId)
            ->where('ventes.date_document', '=', $date)
            ->where('ventes.type_document', PosService::getValue('type_vente') ?? 'bc')
            ->whereNotNull('ventes.pos_session_id')
            ->select('clients.nom', DB::raw('COALESCE(SUM(paiements.encaisser), 0) as total_paye'))
            ->groupBy('clients.id', 'clients.nom')
            ->get();

        foreach ($clientPayments as $p) {
            if (isset($totals[$p->nom])) {
                $totals[$p->nom]['total_paye'] = (float)$p->total_paye;
            }
        }

        return [
            'clients' => array_keys($clients),
            'articles' => array_keys($articles),
            'data' => $matrix,
            'client_totals' => $totals,
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

        $qA = DB::table('paiements')
            ->where('paiements.date_paiement', '=', $date)
            ->where('paiements.magasin_id', $magasinId)
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->join('clients', 'ventes.client_id', '=', 'clients.id')
            ->where('ventes.date_emission', '<=', $date)
            ->join('methodes_paiement', 'methodes_paiement.key', '=', 'paiements.methode_paiement_key')
            ->whereNotNull('ventes.pos_session_id')
            ->where('ventes.magasin_id', $magasinId)
            ->select(
                'ventes.id as vente_id',
                'ventes.reference',
                'clients.nom as client_name',
                'methodes_paiement.nom as last_payment_method',
                'paiements.date_paiement as last_payment_date',
                'paiements.cheque_lcn_reference',
                'ventes.date_emission as sale_date',
                'ventes.is_controled',
                'ventes.total_ttc',
                'ventes.statut_paiement',
                DB::raw('ventes.total_ttc - COALESCE((SELECT SUM(encaisser) FROM paiements WHERE payable_id = ventes.id AND payable_type = "App\\\\Models\\\\Vente"), 0) as creance_amount')
            );

        $qB = DB::table('ventes')
            ->leftJoin('clients', 'ventes.client_id', '=', 'clients.id')
            ->leftJoin('paiements', function ($join) {
                $join->on('paiements.payable_id', '=', 'ventes.id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->whereNotNull('ventes.pos_session_id')
            ->where('ventes.magasin_id', $magasinId)
            ->where('ventes.date_emission', '<=', $date)
            ->whereNotIn('ventes.statut_paiement', ['paye', 'solde'])
            ->where('ventes.date_document', '=', $date)
            ->select(
                'ventes.id as vente_id',
                'ventes.reference',
                'clients.nom as client_name',
                DB::raw('NULL as last_payment_method'),
                DB::raw('NULL as last_payment_date'),
                DB::raw('NULL as cheque_lcn_reference'),
                'ventes.date_emission as sale_date',
                'ventes.is_controled',
                'ventes.total_ttc',
                'ventes.statut_paiement',
                DB::raw('ventes.total_ttc - COALESCE((SELECT SUM(encaisser) FROM paiements WHERE payable_id = ventes.id AND payable_type = "App\\\\Models\\\\Vente"), 0) as creance_amount')
            );

        $rows = DB::query()
            ->fromSub($qA->unionAll($qB), 't')
            ->select(
                't.reference',
                't.client_name',
                DB::raw('MAX(t.last_payment_method) as last_payment_method'),
                DB::raw('MAX(t.last_payment_date) as last_payment_date'),
                DB::raw('MAX(t.cheque_lcn_reference) as cheque_lcn_reference'),
                't.sale_date',
                't.is_controled',
                't.total_ttc',
                't.statut_paiement',
                DB::raw('MAX(t.creance_amount) as creance_amount')
            )
            ->groupBy('t.reference', 't.client_name', 't.sale_date', 't.is_controled', 't.total_ttc', 't.statut_paiement')
            ->get();

        $array = json_decode(json_encode($rows), true);
        foreach ($array as &$r) {
            $r['statut_paiement'] = ucfirst(__('ventes.' . $r['statut_paiement']));
        }
        unset($r);

        return ['rows' => $array];
    }

    // 4) Trésorerie (entrées/sorties/solde du jour)
    private function computeTR(Request $request): array
    {
        $date = $request->get('date') ?: Carbon::today()->format('Y-m-d');
        $magasinId = (int)$request->get('magasin_id');

        $total_vente = DB::table('ventes')
            ->where('magasin_id', $magasinId)
            ->where('date_document', '=', $date)
            ->whereNotNull('pos_session_id')
            ->sum('total_ttc');

        $total_espece = DB::table('paiements')
            ->where('date_paiement', '=', $date)
            ->where('magasin_id', $magasinId)
            ->where('methode_paiement_key', 'especes')
            ->sum('encaisser');

        $total_cheque = DB::table('paiements')
            ->where('date_paiement', '=', $date)
            ->where('magasin_id', $magasinId)
            ->where('methode_paiement_key', 'cheque')
            ->sum('encaisser');

        $total_lcn = DB::table('paiements')
            ->where('date_paiement', '=', $date)
            ->where('magasin_id', $magasinId)
            ->where('methode_paiement_key', 'lcn')
            ->sum('encaisser');

        $total_depenses = DB::table('depenses')
            ->where('date_operation', '=', $date)
            ->where('magasin_id', $magasinId)
            ->sum('montant');

        return [
            'total_vente' => (float)$total_vente,
            'total_espece' => (float)$total_espece,
            'total_cheque' => (float)$total_cheque,
            'total_lcn' => (float)$total_lcn,
            'total_depenses' => (float)$total_depenses,
            'reste_en_caisse' => (float)$total_espece - (float)$total_depenses,
        ];
    }
}
