<?php

namespace App\Http\Controllers\Api\caisse;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\parfums\DepenseResource;
use App\Http\Resources\Api\parfums\HistoryResource;
use App\Models\Article;
use App\Models\Client;
use App\Models\Compte;
use App\Models\Depense;
use App\Models\PosSession;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Services\LogService;
use App\Services\ModuleService;
use App\Services\PaiementService;
use App\Services\PosService;
use App\Services\ReferenceService;
use App\Services\StockService;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;

class VenteController extends Controller
{
    public function sauvegarder(Request $request)
    {
        $rules = [
            'lignes' => 'array|min:1|required',
            'lignes.*.id' => 'exists:articles,id|required',
            'lignes.*.quantity' => ['required', 'min:1', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'lignes.*.prix' => 'numeric|min:0|required',
            'client' => 'exists:clients,id|required',
            'type' => 'required|in:bc,br',
            'exercice' => 'required|date_format:Y',
            'session_id' => 'required|exists:pos_sessions,id'
        ];
        $attributes = [
            'lignes' => 'lignes',
            'lignes.*.id' => 'article',
            'lignes.*.quantity' => 'quantité',
            'lignes.*.prix' => 'prix',
            'client' => 'client',
            'type' => 'type',
            'exercice' => 'exercice',
            'session_id' => 'session'
        ];
        Validator::make($request->all(), $rules, [], $attributes)->validate();
        $type = $request->get('type') === 'br' ? (PosService::getValue('type_retour') ?? 'br') : (PosService::getValue('type_vente') ?? 'bc');
        Session::put('exercice', $request->get('exercice'));
        DB::beginTransaction();
        // Sauvegarder la vente
        try {
            // ------------------ ### Definir la session ### ------------------
            $o_pos_session = PosSession::find($request->get('session_id'));

            // ------------------ ### Vérifier si la session est ouverte ### ------------------
            if (!$o_pos_session->ouverte) {
                return response('Cette session n\'est pas ouverte ! ', 500);
            }

            // ------------------ ### Créer la vente ### ------------------
            $data = [
                'created_by' => auth()->id(),
                'client_id' => $request->get('client'),
                'commercial_id' => null,
                'commission_par_defaut' => null,
                'reference' => ReferenceService::generateReference($type, Carbon::now()),
                "statut" => "validé",
                "objet" => 'Point de vente',
                'date_document' => now()->toDateString(),
                'date_emission' => Carbon::createFromFormat('d/m/Y', now()->format('d/m/Y'))->toDateString(),
                'type_document' => $type,
                'statut_paiement' => 'non_paye',
                'pos_session_id' => $o_pos_session->id,
                'magasin_id' => $o_pos_session->magasin_id,
                'note' => null
            ];
            if (in_array($type, ['dv', 'fa', 'fp', 'bc'])) {
                $data['date_expiration'] = Carbon::createFromFormat('d/m/Y', now()->format('d/m/Y'))->toDateString();
            }
            $lignes = $request->get('lignes', []);
            // --- Vérification limite de crédit ---
            $client = Client::find($request->get('client'));
            if ($client) {
                $creditInfo = $this->checkEncaissementCredit($client);
                $typeCourant = $type;
                $totalTtcCourant = 0;
                if (count($lignes) > 0) {
                    foreach ($lignes as $ligne) {
                        $ht = $ligne['prix'] ?? 0;
                        $reduction = 0;
                        $taxe = $ligne['taxe'] ?? (Article::find($ligne['id'])->taxe ?? 0);
                        $quantite = $ligne['quantity'] ?? 0;
                        $htReduit = $ht - $reduction;
                        $ttc = round(($htReduit * (1 + $taxe / 100)) * $quantite, 2);
                        $totalTtcCourant += $ttc;
                    }
                }
                if (
                    $request->get('credit') &&
                    in_array($typeCourant, $creditInfo['encaissement_types']) &&
                    (
                        // Vérification de la limite de crédit par montant
                        ($client->limite_de_credit > 0 &&
                        $creditInfo['total_non_paye'] + $totalTtcCourant > $creditInfo['limite_credit']) ||

                        // Vérification de la limite de crédit par nombre de ventes
                        ($client->limite_ventes_impayees > 0 &&
                        $creditInfo['total_ventes_impayees'] + 1 > $creditInfo['limite_ventes_impayees'])
                    )
                ){
                    $message = ($client->limite_de_credit > 0 &&
                               $creditInfo['total_non_paye'] + $totalTtcCourant > $creditInfo['limite_credit'])
                               ? "Limite de crédit (montant) dépassée pour ce client."
                               : "Limite du nombre de ventes impayées dépassée pour ce client.";

                    return response()->json(['error' => $message], 422);
                }
            }
            // --- Fin vérification crédit ---
            $o_vente = Vente::create($data);
            $vente_ht = 0;
            $vente_ttc = 0;
            $vente_tva = 0;
            $vente_reduction = 0;
            if (count($lignes) > 0) {
                foreach ($lignes as $key => $ligne) {
                    $o_article = Article::find($ligne['id']);
                    $o_ligne = new VenteLigne();
                    $o_ligne->vente_id = $o_vente->id;
                    $o_ligne->article_id = $ligne['id'];
                    $o_ligne->unit_id = $o_article->unite_id;
                    $o_ligne->mode_reduction = 'fixe';
                    $o_ligne->nom_article = $ligne['name'];
                    $o_ligne->ht = $ligne['prix'];
                    $o_ligne->quantite = $ligne['quantity'];
                    $o_ligne->taxe = $o_article->taxe;
                    $o_ligne->reduction = 0;
                    $o_ligne->total_ttc = $this->calculate_ttc($o_ligne->ht, 0, $o_ligne->taxe, $o_ligne->quantite);
                    $o_ligne->position = $key;
                    $o_ligne->magasin_id = $o_pos_session->magasin_id;
                    $o_ligne->save();
                    // ------------------ ### Stock ### ------------------
                    if (in_array($type, ModuleService::stockEntrerTypes())) {
                        StockService::stock_entre($o_article->id, $o_ligne->quantite, now()->format('Y-m-d'), Vente::class, $o_vente->id, $o_pos_session->magasin_id);
                    } elseif (in_array($type, ModuleService::stockSortirTypes())) {
                        StockService::stock_sortir($o_article->id, $o_ligne->quantite, now()->format('Y-m-d'), Vente::class, $o_vente->id, $o_pos_session->magasin_id);
                    }

                    // ------------------ ### Calculer les totaux ### ------------------
                    $vente_ht += ($o_ligne->ht) * $o_ligne->quantite;
                    $vente_reduction += 0;
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
            }
            $o_compte = Compte::where('principal', 1)->first() ?? Compte::first();
            $paiement_data = [
                'i_date_paiement' => now()->format('d/m/Y'),
                'i_compte_id' => $o_compte->id,
                'i_method_key' => 'especes',
                'client_id' => $request->get('id'),
                'i_montant' => $vente_ttc,
                'i_session_id' => $o_pos_session->id,
            ];
            // ------------------ ### Receipt ### ------------------
            if (PosService::getValue('ticket')) {
                $template = PosService::getValue('ticket_template');
                if (PosService::getValue('double_ticket_template')) {
                    $template_rendered = view('documents.ventes.double_receipt', compact('o_vente', 'template'))->render();
                } else {
                    $template_rendered = view('documents.ventes.receiptt', compact('o_vente', 'template'))->render();
                }
            }
            // -------------------------------------------------------
            PaiementService::add_paiement(Vente::class, $o_vente->id, $paiement_data, $o_pos_session->magasin_id, $o_pos_session->id);
            ReferenceService::incrementCompteur($type);
            DB::commit();
            // ------------------ ### Response ### ------------------
            $repsonse = ['message' => $o_vente->reference . ' ajoutée avec succès ! ', 'template' => $template_rendered ?? null];
            return response($repsonse, 200);
        } catch (Exception $exception) {
            LogService::logException($exception);
            DB::rollBack();
            return response('Erreur lors de l\'ajout de la vente ! ', 500);
        }
    }
    public function sauvegarder_avec_paiement(Request $request)
    {
        \Log::info($request->all());

        $rules = [
            'lignes' => 'array|min:1|required',
            'lignes.*.id' => 'exists:articles,id|required',
            'lignes.*.quantity' => ['required', 'min:1', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'lignes.*.prix' => 'numeric|min:0|required',
            'client' => 'exists:clients,id|required',
            'type' => 'required|in:bc,br',
            'exercice' => 'required|date_format:Y',
            'paiement.i_compte_id' => 'required|exists:comptes,id',
            'paiement.i_montant' => 'required|min:1|numeric',
            'paiement.i_method_key' => ['required', 'exists:methodes_paiement,key'],
            'session_id' => 'required|exists:pos_sessions,id'
        ];
        $attributes = [
            'lignes' => 'lignes',
            'client' => 'client',
            'type' => 'type',
            'exercice' => 'exercice',
            'paiement.i_compte_id' => 'compte',
            'paiement.i_montant' => 'montant',
            'paiement.i_method_key' => 'méthode de paiement',
            'paiement.i_date' => 'date prevu',
            'paiement.i_date_paiement' => 'date de paiement',
            'paiement.i_reference' => 'reference',
            'session_id' => 'session'
        ];
        Validator::make($request->all(), $rules, [], $attributes)->validate();
        $type = $request->get('type') === 'br' ? (PosService::getValue('type_retour') ?? 'br') : (PosService::getValue('type_vente') ?? 'bc');
        Session::put('exercice', $request->get('exercice'));
        DB::beginTransaction();
        // Sauvegarder la vente
        try {
            // ------------------ ### Definir la session ### ------------------
            $o_pos_session = PosSession::find($request->get('session_id'));

            // ------------------ ### Vérifier si la session est ouverte ### ------------------
            if (!$o_pos_session->ouverte) {
                return response('Cette session n\'est pas ouverte ! ', 500);
            }

            // ------------------ ### Créer la vente ### ------------------
            $data = [
                'created_by' => auth()->id(),
                'client_id' => $request->get('client'),
                'commercial_id' => null,
                'commission_par_defaut' => null,
                'reference' => $request->input('paiement.i_reference') ?? ReferenceService::generateReference($type, Carbon::now()),
                "statut" => "validé",
                "objet" => 'Point de vente',
                'date_document' => now()->toDateString(),
                'date_emission' => Carbon::createFromFormat('d/m/Y', now()->format('d/m/Y'))->toDateString(),
                'type_document' => $type,
                'statut_paiement' => 'non_paye',
                'note' => null,
                'pos_session_id' => $o_pos_session->id,
                'magasin_id' => $o_pos_session->magasin_id,
            ];
            if (in_array($type, ['dv', 'fa', 'fp', 'bc'])) {
                $data['date_expiration'] = Carbon::createFromFormat('d/m/Y', now()->format('d/m/Y'))->toDateString();
            }
            $lignes = $request->get('lignes', []);
            // --- Vérification limite de crédit ---
            $client = Client::find($request->get('client'));
            if ($client) {
                $creditInfo = $this->checkEncaissementCredit($client);
                $typeCourant = $type;
                $totalTtcCourant = 0;
                if (count($lignes) > 0) {
                    foreach ($lignes as $ligne) {
                        $ht = $ligne['prix'] ?? 0;
                        $reduction = 0;
                        $quantite = $ligne['quantity'] ?? 0;
                        $htReduit = $ht - $reduction;
                        $ttc = round(($htReduit  * $quantite), 2);
                        $totalTtcCourant += $ttc;
                    }
                }
                // Montant payé
                $montantPaye = $request->input('paiement.i_montant', 0);
                // Montant à créditer
                $montantCredit = $totalTtcCourant - $montantPaye;

                if (
                    $montantCredit > 0 && // Seulement si une partie est à crédit
                    in_array($typeCourant, $creditInfo['encaissement_types']) &&
                    (
                        // Vérification de la limite de crédit par montant
                        ($client->limite_de_credit > 0 &&
                        $creditInfo['total_non_paye'] + $montantCredit > $creditInfo['limite_credit']) ||

                        // Vérification de la limite de crédit par nombre de ventes
                        ($client->limite_ventes_impayees > 0 &&
                        $creditInfo['total_ventes_impayees'] + 1 > $creditInfo['limite_ventes_impayees'])
                    )
                ) {
                    $message = ($client->limite_de_credit > 0 &&
                               $creditInfo['total_non_paye'] + $montantCredit > $creditInfo['limite_credit'])
                               ? "Limite de crédit (montant) dépassée pour ce client."
                               : "Limite du nombre de ventes impayées dépassée pour ce client.";

                    return response()->json(['error' => $message], 422);
                }
            }
            // *-------------------------


            $o_vente = Vente::create($data);
            $vente_ht = 0;
            $vente_ttc = 0;
            $vente_tva = 0;
            $vente_reduction = 0;
            if (count($lignes) > 0) {
                foreach ($lignes as $key => $ligne) {
                    $o_article = Article::find($ligne['id']);
                    $o_ligne = new VenteLigne();
                    $o_ligne->vente_id = $o_vente->id;
                    $o_ligne->article_id = $ligne['id'];
                    $o_ligne->unit_id = $o_article->unite_id;
                    $o_ligne->mode_reduction = 'fixe';
                    $o_ligne->nom_article = $ligne['name'];
                    $o_ligne->ht = $ligne['prix'];
                    $o_ligne->quantite = $ligne['quantity'];
                    $o_ligne->taxe = $o_article->taxe;
                    $o_ligne->reduction = 0;
                    $o_ligne->total_ttc = $this->calculate_ttc($o_ligne->ht, 0, $o_ligne->taxe, $o_ligne->quantite);
                    $o_ligne->position = $key;
                    $o_ligne->magasin_id = $o_pos_session->magasin_id;
                    $o_ligne->save();
                    // ------------------ ### Stock ### ------------------
                    if (in_array($type, ModuleService::stockEntrerTypes())) {
                        StockService::stock_entre($o_article->id, $o_ligne->quantite, now()->format('Y-m-d'), Vente::class, $o_vente->id, $o_pos_session->magasin_id);
                    } elseif (in_array($type, ModuleService::stockSortirTypes())) {
                        StockService::stock_sortir($o_article->id, $o_ligne->quantite, now()->format('Y-m-d'), Vente::class, $o_vente->id, $o_pos_session->magasin_id);
                    }

                    // ------------------ ### Calculer les totaux ### ------------------
                    $vente_ht += ($o_ligne->ht) * $o_ligne->quantite;
                    $vente_reduction += 0;
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
            }
            // ------------------ ### Receipt ### ------------------
            if (PosService::getValue('ticket')) {
                $template = PosService::getValue('ticket_template');
                if (PosService::getValue('double_ticket_template')) {
                    $template_rendered = view('documents.ventes.double_receipt', compact('o_vente', 'template'))->render();
                } else {
                    $template_rendered = view('documents.ventes.receiptt', compact('o_vente', 'template'))->render();
                }
            }
            $paiement = $request->get('paiement');
            $paiement['i_date_paiement'] = now()->format('d/m/Y');
            PaiementService::add_paiement(Vente::class, $o_vente->id, $paiement, $o_pos_session->magasin_id, $o_pos_session->id);
            ReferenceService::incrementCompteur($type);
            DB::commit();
            // ------------------ ### Response ### ------------------
            $repsonse = [
                'message' => $o_vente->reference . ' ajoutée avec succès ! ',
                'template' => $template_rendered ?? null,
                'vente_id' => $o_vente->id
            ];
            return response($repsonse, 200);
        } catch (Exception $exception) {
            LogService::logException($exception);
            DB::rollBack();
            return response('Erreur lors de l\'ajout de la vente ! ', 500);
        }
    }
    public function ajouter_paiement(Request $request)
    {
        $rules = [
            'vente_id' => 'required|exists:ventes,id',
            'paiement.i_compte_id' => 'required|exists:comptes,id',
            'paiement.i_montant' => 'required|min:1|numeric',
            'paiement.i_method_key' => ['required', 'exists:methodes_paiement,key'],
            'paiement.i_date' => [Rule::requiredIf(in_array($request->i_method_key, ['cheque', 'lcn'])), 'date_format:d/m/Y', 'nullable'],
            'paiement.i_reference' => [Rule::requiredIf(in_array($request->i_method_key, ['cheque', 'lcn'])), 'max:255'],
            'session_id' => 'required|exists:pos_sessions,id'
        ];
        $attributes = [
            'vente_id' => 'vente',
            'paiement.i_compte_id' => 'compte',
            'paiement.i_montant' => 'montant',
            'paiement.i_method_key' => 'méthode de paiement',
            'paiement.i_date' => 'date prevu',
            'paiement.i_date_paiement' => 'date de paiement',
            'paiement.i_reference' => 'reference',
            'session_id' => 'session'
        ];
        Validator::make($request->all(), $rules, [], $attributes)->validate();
        DB::beginTransaction();
        try {
            // ------------------ ### Definir la session ### ------------------
            $o_pos_session = PosSession::find($request->get('session_id'));

            // ------------------ ### Vérifier si la session est ouverte ### ------------------
            if (!$o_pos_session->ouverte) {
                return response('Cette session n\'est pas ouverte ! ', 500);
            }

            $o_vente = Vente::findOrFail($request->get('vente_id'));

            // ------------------ ### Receipt ### ------------------
            if (PosService::getValue('ticket')) {
                $template = PosService::getValue('ticket_template');
                if (PosService::getValue('double_ticket_template')) {
                    $template_rendered = view('documents.ventes.double_receipt', compact('o_vente', 'template'))->render();
                } else {
                    $template_rendered = view('documents.ventes.receiptt', compact('o_vente', 'template'))->render();
                }
            }

            $paiement = $request->get('paiement');
            $paiement['i_date_paiement'] = now()->format('d/m/Y');
            PaiementService::add_paiement(Vente::class, $o_vente->id, $paiement, $o_pos_session->magasin_id, $o_pos_session->id);
            DB::commit();
            // ------------------ ### Response ### ------------------
            $repsonse = [
                'message' => 'Paiement ajouté avec succès à ' . $o_vente->reference . ' !',
                'template' => $template_rendered ?? null,
                'vente_id' => $o_vente->id
            ];
            return response($repsonse, 200);
        } catch (Exception $exception) {
            LogService::logException($exception);
            DB::rollBack();
            return response('Erreur lors de l\'ajout du paiement ! ', 500);
        }
    }

    public function history(Request $request) {
        $ventes = Vente::where('pos_session_id',$request->get('session_id'))->where('type_document',(PosService::getValue('type_vente') ?? 'bc'))->get();
        $ventes = HistoryResource::collection($ventes);
        $retours = Vente::where('pos_session_id',$request->get('session_id'))->where('type_document',(PosService::getValue('type_retour') ?? 'br'))->get();
        $retours = HistoryResource::collection($retours);
        $depenses = Depense::where('pos_session_id',$request->get('session_id'))->get();
        $depenses = DepenseResource::collection($depenses);
        return [
            'depenses'=>$depenses,
            'ventes'=>$ventes,
            'retours' =>$retours
        ];
    }

    public function ticket(Request $request,$id){
        $o_vente = Vente::findOrFail($id);
        $template = PosService::getValue('ticket_template');
        return $template_rendered = view('documents.ventes.receiptt', compact('o_vente', 'template'))->render();
    }
    /**
     * @param float $ht
     * @param float $reduction
     * @param float $tva
     * @param float $quantite
     * @return string
     */


    function calculate_ttc(float $ht, float $reduction, float $tva, float $quantite): string
    {
        $ht = round($ht - $reduction, 2);
        $tva = (1 + $tva / 100);
        $ttc = round($ht * $tva, 2) * $quantite;
        return round($ttc, 2);
    }

    /**
     * @param float $ht
     * @param float $reduction
     * @param float $tva
     * @param float $quantite
     * @return float
     */
    function calculate_tva_amount(float $ht, float $reduction, float $tva, float $quantite): float
    {
        return +number_format(round(($ht - $reduction) * ($tva / 100), 10) * $quantite, 2, '.', '');
    }

    /**
     * Vérifie l'encours de crédit d'un client.
     *
     * Cette méthode calcule le total des ventes non payées pour un client donné,
     * en fonction des types de documents considérés comme encaissables, et retourne
     * la limite de crédit, le total non payé et les types d'encaissement.
     *
     */
    private function checkEncaissementCredit(Client $client)
    {
        $encaissementTypes = \App\Services\ModuleService::getEncaissementTypes();
        $limiteCredit = $client->limite_de_credit ?? 0;
        $limiteVentesImpayees = $client->limite_ventes_impayees ?? 0;

        // Calcul du montant total non payé
        $totalNonPaye = \App\Models\Vente::where('client_id', $client->id)
            ->whereIn('type_document', $encaissementTypes)
            ->where('statut_paiement', 'non_paye')
            ->sum('total_ttc');
        $totalSoldePartiel = \App\Models\Vente::where('client_id', $client->id)
            ->whereIn('type_document', $encaissementTypes)
            ->where('statut_paiement', 'partiellement_paye')
            ->sum('solde');

        // Comptage du nombre de ventes non payées ou partiellement payées
        $countNonPaye = \App\Models\Vente::where('client_id', $client->id)
            ->whereIn('type_document', $encaissementTypes)
            ->where('statut_paiement', 'non_paye')
            ->count();
        $countPartielPaye = \App\Models\Vente::where('client_id', $client->id)
            ->whereIn('type_document', $encaissementTypes)
            ->where('statut_paiement', 'partiellement_paye')
            ->count();
        $totalVentesImpayees = $countNonPaye + $countPartielPaye;

        return [
            'limite_credit' => $limiteCredit,
            'total_non_paye' => $totalNonPaye + $totalSoldePartiel,
            'limite_ventes_impayees' => $limiteVentesImpayees,
            'total_ventes_impayees' => $totalVentesImpayees,
            'encaissement_types' => $encaissementTypes
        ];
    }

}
