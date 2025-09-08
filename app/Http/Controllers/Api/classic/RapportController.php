<?php

namespace App\Http\Controllers\Api\classic;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\PosSession;
use App\Models\VenteLigne;
use App\Services\PosService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    public function stock(Request $request)
    {
        // ------------------ ### Definir la session ### ------------------
        $o_pos_session = PosSession::find($request->get('session_id'));

        // ------------------ ### Vérifier si la session est ouverte ### ------------------
        if (!$o_pos_session->ouverte) {
            return response('Cette session n\'est pas ouverte ! ', 500);
        }

        $stock = DB::table('articles as a')
            ->leftJoin('transaction_stocks as ts', function ($join) use ($o_pos_session) {
                $join->on('a.id', '=', 'ts.article_id')
                    ->where('ts.magasin_id', $o_pos_session->magasin_id);
            })
            ->select(
                'a.id',
                'a.designation',
                'a.reference',
                DB::raw('COALESCE(SUM(ts.qte_entree) - SUM(ts.qte_sortir), 0) AS stock'),
            )
            ->groupBy('a.id');

        return response($stock->get(), 200);
    }

    /**
     * Generate a client-article sales report in matrix format.
     *
     * This method retrieves sales data for the specified session,
     * aggregates it by client and article, and formats it into a
     * matrix structure. Each row represents a client, and each column
     * represents an article. The matrix contains quantities and total
     * TTC values for each client-article combination.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * Expected Request Parameters:
     * - session_id (string|int): The POS session ID to filter sales data.
     *
     * Response Format:
     * {
     *     "clients": ["Client A", "Client B", ...],
     *     "articles": ["Article1", "Article2", ...],
     *     "data": {
     *         "Client A": {
     *             "Article1": {"quantite": 5, "total_ttc": 500},
     *             "Article2": {"quantite": 3, "total_ttc": 300},
     *             ...
     *         },
     *         "Client B": {
     *             "Article1": {"quantite": 0, "total_ttc": 0},
     *             ...
     *         }
     *         ...
     *     }
     * }
     */
    public function article_client_rapport(Request $request)
    {
        $o_pos_session = PosSession::find($request->get('session_id'));
        if (!$o_pos_session->ouverte) {
            return response('Cette session n\'est pas ouverte ! ', 500);
        }

        $magasin_id = $o_pos_session->magasin_id;
        $today = Carbon::today()->format('Y-m-d');
        $type_document = PosService::getValue('type_vente') ?? 'bc';

        // --- Step 1: Get correct sales data (quantities and totals) without joining payments ---
        // This query now accurately sums sales lines without duplication.
        $salesData = VenteLigne::whereHas('vente', function ($query) use ($magasin_id, $today, $type_document) {
            $query->where('magasin_id', $magasin_id)
                ->where('date_document', '=', $today)
                ->where('type_document', $type_document);
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
        $matrixData = [];
        $clientTotals = [];

        // --- Step 2: Process the sales data ---
        foreach ($salesData as $item) {
            $clientName = $item->nom;
            $articleRef = $item->designation;

            // Initialize client if not seen before
            if (!isset($clientTotals[$clientName])) {
                $clients[$clientName] = true;
                $clientTotals[$clientName] = [
                    'total_ttc' => 0,
                    'total_paye' => 0, // Default to 0, will be filled by the next query
                    'total_creance' => 0,
                ];
            }

            if (!isset($articles[$articleRef])) {
                $articles[$articleRef] = true;
            }

            // Populate matrix data with sales info
            $matrixData[$clientName][$articleRef] = [
                'quantite' => floor($item->quantite) == $item->quantite ? (int)$item->quantite : $item->quantite,
                'total_ttc' => $item->total_ttc
            ];

            // Add to client's total turnover
            $clientTotals[$clientName]['total_ttc'] += $item->total_ttc;
        }

        // --- Step 3: Get total paid amounts for each client  ---
        $clientPayments = DB::table('ventes')
            ->join('clients', 'clients.id', '=', 'ventes.client_id')
            ->leftJoin('paiements', function ($join) {
                $join->on('paiements.payable_id', '=', 'ventes.id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('ventes.magasin_id', $magasin_id)
            ->where('ventes.date_document', '=', $today)
            ->where('ventes.type_document', $type_document)
            ->whereNotNull('ventes.pos_session_id')
            ->select(
                'clients.nom',
                DB::raw('COALESCE(SUM(paiements.encaisser), 0) as total_paye')
            )
            ->groupBy('clients.id', 'clients.nom')
            ->get();

        // --- Step 4: Merge payment data and calculate debt (créance) ---
        foreach ($clientPayments as $payment) {
            if (isset($clientTotals[$payment->nom])) {
                $clientTotals[$payment->nom]['total_paye'] = (float) $payment->total_paye;
            }
        }

        foreach ($clientTotals as $name => $totals) {
            $clientTotals[$name]['total_creance'] = $totals['total_ttc'] - $totals['total_paye'];
        }

        // --- Step 5: Final data preparation for the view ---
        $clientNames = array_keys($clients);
        $articleRefs = array_keys($articles);

        // Pad the matrix with zeros for clients who didn't buy a specific article
        foreach ($clientNames as $client) {
            foreach ($articleRefs as $article) {
                if (!isset($matrixData[$client][$article])) {
                    $matrixData[$client][$article] = [
                        'quantite' => 0,
                        'total_ttc' => 0
                    ];
                }
            }
        }

        // Calculate grand totals for the footer
        $grand_total_ttc = array_sum(array_column($clientTotals, 'total_ttc'));
        $grand_total_paye = array_sum(array_column($clientTotals, 'total_paye'));
        $grand_total_creance = array_sum(array_column($clientTotals, 'total_creance'));

        return response()->json([
            'clients' => $clientNames,
            'articles' => $articleRefs,
            'data' => $matrixData,
            'client_totals' => $clientTotals,
            'totals' => [
                'total_ttc' => $grand_total_ttc,
                'total_paye' => $grand_total_paye,
                'total_creance' => $grand_total_creance,
            ]
        ]);
    }
    /**
     * Generate a supplier-article purchase report in matrix format.
     *
     * This method retrieves purchase data for the specified session's store,
     * aggregates it by supplier and article, and formats it into a
     * matrix structure. Each row represents a supplier, and each column
     * represents an article. The matrix contains quantities and total
     * TTC values for each supplier-article combination.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * Expected Request Parameters:
     * - session_id (string|int): The POS session ID to get the magasin_id.
     *
     * Response Format:
     * {
     *     "fournisseurs": ["Fournisseur A", "Fournisseur B", ...],
     *     "articles": ["Article1", "Article2", ...],
     *     "data": {
     *         "Fournisseur A": {
     *             "Article1": {"quantite": 5, "total_ttc": 500},
     *             "Article2": {"quantite": 3, "total_ttc": 300},
     *             ...
     *         },
     *         "Fournisseur B": {
     *             "Article1": {"quantite": 0, "total_ttc": 0},
     *             ...
     *         }
     *         ...
     *     }
     * }
     */
    public function article_fournisseur_rapport(Request $request)
    {
        // Get the magasin_id from the session
        $o_pos_session = PosSession::find($request->get('session_id'));

        if (!$o_pos_session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $magasin_id = $o_pos_session->magasin_id;

        $rapport = DB::table('achat_lignes')
            ->where('achats.magasin_id', $magasin_id)->where('date_emission', '=', Carbon::today()->format('Y-m-d'))
            ->join('achats', 'achats.id', '=', 'achat_lignes.achat_id')
            ->join('articles', 'articles.id', '=', 'achat_lignes.article_id')
            ->join('fournisseurs', 'fournisseurs.id', '=', 'achats.fournisseur_id')
            ->leftJoin('paiements', function ($join) {
                $join->on('paiements.payable_id', '=', 'achats.id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Achat');
            })
            ->select(
                'fournisseurs.nom',
                'fournisseurs.id as fournisseur_id',
                'articles.designation',
                'achat_lignes.article_id',
                DB::raw('SUM(achat_lignes.quantite) as quantite'),
                DB::raw('SUM(achat_lignes.total_ttc) as total_ttc'),
                DB::raw('SUM(paiements.encaisser) as montant')
            )
            ->groupBy('fournisseurs.id', 'fournisseurs.nom', 'achat_lignes.article_id', 'articles.designation')
            ->get();

        $fournisseurs = [];
        $articles = [];
        $matrixData = [];

        foreach ($rapport as $item) {
            $fournisseurName = $item->nom;
            $articleRef = $item->designation;

            if (!isset($fournisseurs[$fournisseurName])) {
                $fournisseurs[$fournisseurName] = true;
            }
            if (!isset($articles[$articleRef])) {
                $articles[$articleRef] = true;
            }

            // Directly populate matrix data
            $matrixData[$fournisseurName][$articleRef] = [
                'quantite' => floor($item->quantite) == $item->quantite ? (int)$item->quantite : $item->quantite,
                'total_ttc' => $item->total_ttc
            ];
        }

        $fournisseurNames = array_keys($fournisseurs);
        $articleRefs = array_keys($articles);

        foreach ($fournisseurNames as $fournisseur) {
            foreach ($articleRefs as $article) {
                if (!isset($matrixData[$fournisseur][$article])) {
                    $matrixData[$fournisseur][$article] = [
                        'quantite' => 0,
                        'total_ttc' => 0
                    ];
                }
            }
        }

        return response()->json([
            'fournisseurs' => $fournisseurNames,
            'articles' => $articleRefs,
            'data' => $matrixData
        ]);
    }

    /**
     * Generate a receivables report.
     *
     * This method retrieves sales data where the payment status is not 'paye' or 'solde',
     * and includes information about the last payment for each sale.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * Expected Request Parameters:
     * - session_id (string|int): The POS session ID to get the magasin_id.
     *
     * Response Format:
     * [
     *     {
     *         "reference": "REF001",
     *         "client_name": "Client A",
     *         "last_payment_method": "Espèces",
     *         "last_payment_date": "01/01/2023",
     *         "cheque_lcn_number": "CHQ123",
     *         "sale_date": "01/01/2023",
     *         "is_controlled": true,
     *         "creance_amount": 1000
     *     },
     *     ...
     * ]
     */
    public function creance_rapport(Request $request)
    {
        // Get the magasin_id from the session
        $o_pos_session = PosSession::find($request->get('session_id'));

        if (!$o_pos_session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $magasin_id = $o_pos_session->magasin_id;

        // Build a receivables report that includes:
        // - ventes with payments made today for older sales
        // - plus ventes that are not fully paid yet (regardless of a payment today)
        // We union both sets and compute remaining amount per sale.

        // Query A: ventes with a payment today (classic behavior)
        $creances = Paiement::where('date_paiement', '=', Carbon::today()->format('Y-m-d'))
            ->where('paiements.magasin_id', $magasin_id)->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })->join('clients', 'ventes.client_id', '=', 'clients.id')
            ->join('magasins', 'magasins.id', '=', 'ventes.magasin_id')
            ->where('ventes.date_emission', '<', Carbon::today()->format('Y-m-d'))
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


        // Convert the creances to an array for easier manipulation
        $creancesArray = json_decode(json_encode($creances), true);

        foreach ($creancesArray as &$creance) {
            // Ensure statut_paiement is always set

            $creance['statut_paiement'] = ucfirst(__('ventes.'.$creance['statut_paiement']));

        }

        // Return the modified array
        return response()->json($creancesArray);
    }

    /**
     * Generate a treasury report for a POS session.
     *
     * This method retrieves financial data for the specified session,
     * including total sales, cash, checks, LCN, expenses, and remaining cash.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * Expected Request Parameters:
     * - session_id (string|int): The POS session ID to get the financial data.
     *
     * Response Format:
     * {
     *     "total_vente": 1000,
     *     "total_espece": 500,
     *     "total_cheque": 300,
     *     "total_lcn": 200,
     *     "total_depenses": 100,
     *     "reste_en_caisse": 400
     * }
     */
    public function tresorie_rapport(Request $request)
    {
        // Get the POS session
        $o_pos_session = PosSession::find($request->get('session_id'));

        if (!$o_pos_session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        // Get total sales for the session
        $total_vente_jour = DB::table('ventes')
            ->where('magasin_id', $o_pos_session->magasin_id)->where('date_document', '=', Carbon::today()->format('Y-m-d'))
//            ->whereNotNull('pos_session_id')
            ->sum('total_ttc');

        $total_vente_creance = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', Carbon::today()->format('Y-m-d'))
            ->where('ventes.date_emission', '<', Carbon::today()->format('Y-m-d'))
            ->where('paiements.magasin_id', $o_pos_session->magasin_id)
            ->sum('ventes.total_ttc');

        $total_vente = $total_vente_creance + $total_vente_jour;
        // Get total cash payments for the session


        $total_espece_jour = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', Carbon::today()->format('Y-m-d'))
            ->where('ventes.date_emission', '=', Carbon::today()->format('Y-m-d'))
            ->where('paiements.magasin_id', $o_pos_session->magasin_id)
            ->where('paiements.methode_paiement_key', 'especes')
            ->sum('paiements.encaisser');

        $total_espece_creance = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', Carbon::today()->format('Y-m-d'))
            ->where('ventes.date_emission', '<', Carbon::today()->format('Y-m-d'))
            ->where('paiements.magasin_id', $o_pos_session->magasin_id)
            ->where('paiements.methode_paiement_key', 'especes')
            ->sum('paiements.encaisser');

        $total_espece = $total_espece_creance + $total_espece_jour;

        $total_cheque_jour = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', Carbon::today()->format('Y-m-d'))
            ->where('ventes.date_emission', '=', Carbon::today()->format('Y-m-d'))
            ->where('paiements.magasin_id', $o_pos_session->magasin_id)
            ->where('paiements.methode_paiement_key', 'cheque')
            ->sum('paiements.encaisser');

        $total_cheque_creance = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', Carbon::today()->format('Y-m-d'))
            ->where('ventes.date_emission', '<', Carbon::today()->format('Y-m-d'))
            ->where('paiements.magasin_id', $o_pos_session->magasin_id)
            ->where('paiements.methode_paiement_key', 'cheque')
            ->sum('paiements.encaisser');

        $total_cheque = $total_cheque_creance + $total_cheque_jour;

        $total_lcn_jour = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', Carbon::today()->format('Y-m-d'))
            ->where('ventes.date_emission', '=', Carbon::today()->format('Y-m-d'))
            ->where('paiements.magasin_id', $o_pos_session->magasin_id)
            ->where('paiements.methode_paiement_key', 'lcn')
            ->sum('paiements.encaisser');

        $total_lcn_creance = DB::table('paiements')
            ->join('ventes', function ($join) {
                $join->on('ventes.id', '=', 'paiements.payable_id')
                    ->where('paiements.payable_type', '=', 'App\\Models\\Vente');
            })
            ->where('paiements.date_paiement', '=', Carbon::today()->format('Y-m-d'))
            ->where('ventes.date_emission', '<', Carbon::today()->format('Y-m-d'))
            ->where('paiements.magasin_id', $o_pos_session->magasin_id)
            ->where('paiements.methode_paiement_key', 'lcn')
            ->sum('paiements.encaisser');

        $total_lcn = $total_lcn_creance + $total_lcn_jour;

        // Get total expenses for the session
        $total_depenses = DB::table('depenses')
            ->where('date_operation', '=', Carbon::today()->format('Y-m-d'))
            ->where('magasin_id', $o_pos_session->magasin_id)
            ->sum('montant');

        // Calculate remaining cash
        $reste_en_caisse = $total_espece - $total_depenses;

        // Return the data
        return response()->json([
            'total_vente_jour' => $total_vente_jour,
            'total_vente_creance' => $total_vente_creance,
            'total_vente' => $total_vente,
            'total_espece_jour' => $total_espece_jour,
            'total_espece_creance' => $total_espece_creance,
            'total_espece' => $total_espece,
            'total_cheque_jour' => $total_cheque_jour,
            'total_cheque_creance' => $total_cheque_creance,
            'total_cheque' => $total_cheque,
            'total_lcn_jour' => $total_lcn_jour,
            'total_lcn_creance' => $total_lcn_creance,
            'total_lcn' => $total_lcn,
            'total_depenses' => $total_depenses,
            'reste_en_caisse' => $reste_en_caisse,

        ]);
    }
}
