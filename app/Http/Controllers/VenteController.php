<?php

namespace App\Http\Controllers;

use App\Http\Requests\VenteStoreRequest;
use App\Http\Requests\VenteUpdateRequest;
use App\Models\Client;
use App\Models\Commercial;
use App\Models\Compte;
use App\Models\DocumentsParametre;
use App\Models\GlobalSetting;
use App\Models\Magasin;
use App\Models\MethodesPaiement;
use App\Models\PieceJointe;
use App\Models\Promesse;
use App\Models\RelanceSettings;
use App\Models\Tag;
use App\Models\Taxe;
use App\Models\Template;
use App\Models\Unite;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Services\GlobalService;
use App\Services\LogService;
use App\Services\ModuleService;
use App\Services\PaiementService;
use App\Services\ReferenceService;
use App\Services\SmtpService;
use App\Services\StockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;
use function auth;

class VenteController extends Controller
{
    /**
     * @param Request $request
     * @param string $type
     * @return View|Application|Factory|JsonResponse|\Illuminate\Contracts\Foundation\Application
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function liste(Request $request, string $type)
    {
        $this->guard_custom(['vente.liste']);
        // ------------------- ### Ajax logic ### -------------------
        if ($request->ajax()) {
            $o_ventes = Vente::query()->where('type_document', $type)->with('client');
            // -------------------### Filters ###-------------------

            if (!$request->get('ignore_exercice')){
                $o_ventes->whereYear('date_emission', session()->get('exercice'));
            }
            if ($request->get('client_id')) {
                $o_ventes->where('client_id', $request->get('client_id'));
            }
            if ($request->get('magasin_id')) {
                $o_ventes->where('magasin_id', $request->get('magasin_id'));
            }
            if ($request->get('affaire_id')) {
                $o_ventes->where('affaire_id', $request->get('affaire_id'));
            }
            if ($request->get('commercial_id')) {
                $o_ventes->where('commercial_id', $request->get('commercial_id'));
            }
            if ($request->get('livraison_id')) {
                $o_ventes->where('methode_livraison_id', $request->get('livraison_id'));
            }
            if ($request->get('statut')) {
                $o_ventes->where('statut', $request->get('statut'));
            }
            if ($request->filled('statut_controle')) {
                if ($request->statut_controle === 'controle') {
                    $o_ventes->where('is_controled', 1);
                } elseif ($request->statut_controle === 'non_controle') {
                    $o_ventes->where('is_controled', 0);
                }
            }
            if ($request->get('reference')) {
                $search = '%' . $request->get('reference') . '%';
                $o_ventes->where('reference', 'LIKE', $search);
            }
            if ($request->get('total_ttc') != null) {
                $search = $request->get('total_ttc');
                $o_ventes->where('total_ttc', $search);
            }
            if ($request->get('date_emission')) {
                $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_emission'))[0]))->toDateString();
                $end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_emission'))[1]))->toDateString();
                if ($start === $end) {
                    $o_ventes->whereDate('date_emission', $end);
                }
                $o_ventes->where(function ($query) use ($start, $end) {
                    $query->whereDate('date_emission', '>=', $start)->whereDate('date_emission', '<=', $end)->orWhereNull('date_emission');
                });
            }
            if ($request->get('date_expiration')) {
                $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_expiration'))[0]))->toDateString();
                $end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_expiration'))[1]))->toDateString();
                if ($start === $end) {
                    $o_ventes->whereDate('date_expiration', $end);
                }
                $o_ventes->where(function ($query) use ($start, $end) {
                    $query->whereDate('date_expiration', '>=', $start)->whereDate('date_expiration', '<=', $end)->orWhereNull('date_expiration');
                });
            }
            if ($request->get('statut_paiement')) {
                $o_ventes->where('statut_paiement', $request->get('statut_paiement'));
            }
            if (count($request->get('balises', [])) > 0) {
                $balises = $request->get('balises', []);
                $ids = DB::table('taggables')->where('taggable_type', Vente::class)->whereIn('tag_id', $balises)->pluck('taggable_id');
                $o_ventes->whereIn('id', $ids);
            }
            if ($request->get('order') && $request->get('columns') ){
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order){
                    $o_ventes->orderByRaw(''.$columns[$order['column']]['data'].' '.$order['dir']);
                }
            }

            if ($request->get('promesses_a_traiter')){
                $o_ventes->whereHas('promesses',function ($query){
                   $query->where('date','<',Carbon::today()->toDateString())->whereNull('statut');
                });
            }
            // ------------------- ### End of filters ### -------------------
//            $o_ventes = $o_ventes->get();
            $table = DataTables::of($o_ventes)->order(function (){

            });
            // ------------------- ### Columns formatting ### -------------------
            $table->editColumn('client_id', function ($vents) {
                return $vents->client->nom;
            })->addColumn('actions', function ($row) use ($type) {
                $show = 'afficher';
                $edit = 'modifier';
                $delete = 'supprimer';
                $crudRoutePart = 'ventes';
                $id = $row?->id;
                $attrs = ['type' => $type];
                $action = '';
                if ($row->statut !== 'validé') {
                    $action .= view(
                        'partials.__datatable-action',
                        compact(
                            'show',
                            'edit',
                            'delete',
                            'crudRoutePart',
                            'id',
                            'attrs'
                        )
                    )->render();
                } else {
                    $action .= view(
                        'partials.__datatable-action',
                        compact(
                            'show',
                            'crudRoutePart',
                            'id',
                            'attrs'
                        )
                    )->render();
                }
                $action .= '<button id="clone-btn" data-href="' . route('ventes.clone_modal', [$type, $row->id]) . '" class="d-inline btn btn-sm btn-soft-info mx-1">
                <i class="fa fa-clone"></i>
             </button>';
                return $action;
            })->editColumn('reference', function ($row) {
                return $row->reference ?? 'Brouillon';
            })->editColumn('total_ttc', function ($row) {
                return ($row->total_ttc ?? 0) . ' MAD';
            })->editColumn('objet', function ($row) {
                return '<p class="text-truncate text-nowrap m-0 p-0" style="max-width: 250px">' . $row->objet . '</p>';
            })->editColumn('statut', function ($row) {
                $color = 'secondary';
                if ($row->statut == "validé") {
                    $color = 'success';
                }
                return '<div class="badge w-100 bg-soft-' . $color . '" >' .ucfirst($row->statut) . '</div>';
            })->addColumn(
                'selectable_td',
                function ($contact) {
                    $id = $contact->id;
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            )->editColumn('statut_paiement', function ($row) {

                $color = 'danger';
                switch ($row->statut_paiement) {
                    case "partiellement_paye":
                        $color = 'warning';
                        break;
                    case "paye":
                        $color = 'success';
                        break;
                    case "en_cours":
                        $color = 'info';
                        break;
                    case "solde":
                        $color = 'purple';
                        break;
                }
                return '<div class="badge w-100 bg-soft-' . $color . '" >' . __('ventes.' . $row->statut_paiement) . '</div>';
            })->addColumn('convertir_de',function ($row){
                $text = '';
                foreach ($row->document_parent as $parent){
                    $text.=' <a target="_blank" class="alert-link"
                                               href="'.route('ventes.afficher',[$parent->type_document,$parent->id]).'">'.$parent->reference.'</a>,';
                }
                return $text;
            });
            $table->rawColumns(['actions', 'selectable_td', 'objet', 'statut_paiement','convertir_de','statut']);
            // ------------------- ### End of columns formatting ### -------------------
            return $table->make();
        }
        // ------------------- ### End of ajax logic ### -------------------
        $o_client = Client::get(['id', 'nom as text']);
        $status = Vente::$status;
        $balises = Tag::all();
        $status_paiement = Vente::STATUTS_DE_PAIEMENT;
        $payabale_types = ModuleService::getPayabaleTypes();
        $filter = $request->get('f');
        $o_magasins = Magasin::all(['id','nom as text']);
        return view("ventes.liste", compact('o_client', "type", 'status', 'status_paiement', 'balises', 'payabale_types','filter','o_magasins'));
    }

    /**
     * @param string $type
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function ajouter(string $type): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $this->guard_custom(['vente.sauvegarder']);
        $o_commercils = Commercial::get(['id', 'nom as text', "commission_par_defaut"]);
        $o_unites = Unite::get(['id', "nom"]);
        $o_taxes = Taxe::where('active', '1')->get(["valeur", "nom"]);
        $globals = GlobalService::get_all_globals();
        $prix_revient = $globals->prix_revient;
        $o_magasins = \request()->user()->magasins()->where('active','=','1')->get(['magasin_id as id','nom as text']);
        $templates = Template::all();
        $magasins_count = Magasin::where('active', '=', '1')->count();



        return view('ventes.ajouter', compact('magasins_count','o_commercils', 'type', "o_unites", "o_taxes", "prix_revient", 'o_magasins', 'globals','templates'));
    }

    /**
     * @param Request $request
     * @param string $type
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function sauvegarder(VenteStoreRequest $request, string $type): RedirectResponse
    {
        $this->guard_custom(['vente.sauvegarder']);
        $globals = GlobalService::get_all_globals();
        $date_permission = !$request->user()->can('vente.date');
        DB::beginTransaction();
        try {
            // --- Vérification limite de crédit ---
            $client = Client::find($request->get('client_id'));
            $creditInfo = $this->checkEncaissementCredit($client);
            $typeCourant = $type;
            $totalTtcCourant = 0;
            $lignes = $request->get('lignes', []);
            if (count($lignes) > 0) {
                foreach ($lignes as $ligne) {
                    $ht = $ligne['i_prix_ht'] ?? 0;
                    $reduction = $ligne['i_reduction'] ?? 0;
                    $taxe = $ligne['i_taxe'] ?? 0;
                    $quantite = $ligne['i_quantite'] ?? 0;
                    $htReduit = $ht - $reduction;
                    $ttc = round(($htReduit * (1 + $taxe / 100)) * $quantite, 2);
                    $totalTtcCourant += $ttc;
                }
            }
            if (
                $client->limite_de_credit >0 && in_array($typeCourant, $creditInfo['encaissement_types']) &&
                ($creditInfo['total_non_paye'] + $totalTtcCourant > $creditInfo['limite_credit'])
            ) {
                return redirect()->back()->withInput()->with('error', "Limite de crédit dépassée pour ce client.");
            }
            // --- Fin vérification crédit ---

            // ------------------- ### Magasin ### -------------------
            $magasin_id = $request->get('magasin_id') ?? Magasin::first()->id;
            if (!$request->user()->magasins()->where('magasin_id', $magasin_id)->exists()) {
                session()->flash('warning', "Magasin n'est pas accessible");
                return redirect()->back()->withInput($request->input());
            }
            // ------------------- ### End of Magasin ### -------------------
            $data = [
                'created_by' => auth()->id(),
                'client_id' => $request->get('client_id'),
                'commercial_id' => $request->get('commercial_id') ?? null,
                'commission_par_defaut' => $request->get('commercial_id') ? $request->get('i_commercial_pourcentage') : null,
                'reference' => null,
                "statut" => "brouillon",
                "objet" => $request->get('objet'),
                'date_document' => now()->toDateString(),
                'date_emission' => $date_permission ? Carbon::today()->toDateString() : Carbon::createFromFormat('d/m/Y', $request->get('date_emission'))->toDateString(),
                'type_document' => $type,
                'statut_paiement' => 'non_paye',
                'note' => $request->get('i_note'),
                'magasin_id' => $magasin_id,
                'template_id' => $request->get('template_id') ,

            ];
            if (in_array($type, ['dv', 'fa', 'fp', 'bc'])) {
                $data['date_expiration'] = $date_permission ? Carbon::today()->addDays(15)->toDateString() :  Carbon::createFromFormat('d/m/Y', $request->get('date_expiration'))->toDateString();
            }
            if ($type === 'dv') {
                $data['statut_com'] = 'créé';
            }
            $o_vente = Vente::create($data);
            $template = $o_vente->template;
            $lignes = $request->get('lignes', []);
            // dd($lignes);
            $vente_ht = 0;
            $vente_ttc = 0;
            $vente_tva = 0;
            $vente_reduction = 0;
            if (count($lignes) > 0) {
                foreach ($lignes as $key => $ligne) {
                    if ($ligne['i_reduction_mode'] === 'fixe') {
                        $reduction = $ligne['i_reduction'];
                    } else if ($ligne['i_reduction_mode'] === 'pourcentage') {
                        $reduction = round($ligne['i_prix_ht'] * (($ligne['i_reduction'] ?? 0) / 100), 2);
                    }
                    $o_ligne = new VenteLigne();
                    $o_ligne->vente_id = $o_vente->id;
                    $o_ligne->article_id = $ligne['i_article_id'];
                    $o_ligne->unit_id = $ligne['i_unite'];
                    $o_ligne->mode_reduction = $ligne['i_reduction_mode'];
                    $o_ligne->nom_article = $ligne['i_article'];
                    $o_ligne->description = $ligne['i_description'];
                    $o_ligne->ht = $ligne['i_prix_ht'];
                    $o_ligne->revient = $ligne['i_prix_revient'];
                    $o_ligne->quantite = $ligne['i_quantite'];
                    $o_ligne->taxe = $ligne['i_taxe'];
                    $o_ligne->reduction = $ligne['i_reduction'] ?? 0;
                    $o_ligne->total_ttc = $this->calculate_ttc($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0.00);
                    $o_ligne->position = $key;
                    $o_ligne->magasin_id = $lignes['i_magasin_id'] ?? $magasin_id;
                    $o_ligne->save();
                    $vente_ht += ($o_ligne->ht) * $o_ligne->quantite;
                    $vente_reduction += $reduction * $o_ligne->quantite;
                    $vente_tva += $this->calculate_tva_amount($o_ligne->ht, $reduction, $o_ligne->taxe, $o_ligne->quantite);
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
            $o_vente->tags()->sync($request->get('balises', []));
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Création')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference,
                ])
                ->log('Création de '. __('ventes.' . $type).' ' . $o_vente->reference ?? '-');
            return redirect()->route('ventes.afficher', [ 'id'=> $o_vente->id ,'type' => $type])->with('success', __('ventes.' . $type) . " ajouté avec succès");
        } catch (Exception $exception) {
            LogService::logException($exception);
            DB::rollBack();
            return redirect()->route('ventes.liste', ['type' => $type])->with('error', "Une erreur s'est produite lors de l'ajout du Vente");
        }
    }

    /**
     * @param string $type
     * @param int $id
     * @return View|Application|Factory|RedirectResponse|\Illuminate\Contracts\Foundation\Application
     */
    public function modifier(string $type, int $id): View|Application|Factory|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $this->guard_custom(['vente.mettre_a_jour']);
        $o_vente = Vente::with('lignes.article')->find($id);
        if (!$o_vente) {
            abort(404);
        }
        if ($o_vente->statut === 'validé') {
            session()->flash('warning', __('ventes.' . $type) . " est déjà validé !");
            return redirect()->route('ventes.liste', [$type]);
        }
        $o_commercils = Commercial::get(['id', 'nom', "commission_par_defaut"]);
        $o_unites = Unite::get(['id', "nom"]);
        $o_taxes = Taxe::where('active', '1')->get(["valeur", "nom"]);
        $globals = GlobalService::get_all_globals();
        $modifier_reference = $o_vente->reference && $globals->modifier_reference;
        $prix_revient = $globals->prix_revient;
        $o_magasins = \request()->user()->magasins()->get(['magasin_id as id','nom as text']);
        $magasins_count = Magasin::where('active', '=', '1')->count();
        $globals = GlobalService::get_all_globals();
        $templates = Template::all();
        return view('ventes.modifer', compact('magasins_count','o_commercils', 'type', "o_unites", "o_taxes", 'o_vente', 'modifier_reference', 'prix_revient', 'o_magasins','globals','templates'));
    }

    /**
     * @param Request $request
     * @param string $type
     * @param int $id
     * @return Factory|View|Application|RedirectResponse|\Illuminate\Contracts\Foundation\Application
     */
    public function afficher(Request $request, string $type, int $id): Factory|View|Application|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $this->guard_custom(['vente.afficher']);

        $o_vente = Vente::find($id);
        if (!$o_vente || $o_vente->type_document !== $type) {
            return redirect()->route('ventes.liste', $type)->with('error', __('ventes.' . $type) . " n'existe pas");
        }
        $payabale_types = ModuleService::getPayabaleTypes();
        $globals = GlobalService::get_all_globals();
        $prix_revient = $globals->prix_revient;
        $is_controled=GlobalSetting::first()->controle;
        return view('ventes.afficher', compact('o_vente', 'type', 'payabale_types', 'prix_revient','is_controled','globals'));
    }

    /**
     * @param Request $request
     * @param string $type
     * @param int $id
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function mettre_a_jour(VenteUpdateRequest $request, string $type, int $id): RedirectResponse
    {
        $this->guard_custom(['vente.mettre_a_jour']);
        $date_permission = !$request->user()->can('vente.date');
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            abort(404);
        }
        // ------------------- ### Check if sell is confirmed ### -------------------
        if ($o_vente->statut === 'confirmé') {
            session()->flash('warning', __('ventes.' . $type) . " est déjà confirmé !");
            return redirect()->route('ventes.liste', [$type]);
        }
        DB::beginTransaction();
        try {
            // ------------------- ### Magasin ### ----------------
            $magasin_id = $request->get('magasin_id') ?? $o_vente->magasin_id ?? Magasin::first()->id;

            if (!$request->user()->magasins()->where('magasin_id', $magasin_id)->exists()) {
                session()->flash('warning', "Magasin n'est pas accessible");
                return redirect()->back()->withInput($request->input());
            }
            // ----------------------------------------------------
            $data = [
                'client_id' => $request->get('client_id'),
                'commercial_id' => $request->get('commercial_id') ?? null,
                'commission_par_defaut' => $request->get('commercial_id') ? $request->get('i_commercial_pourcentage') : null,
                "objet" => $request->get('objet'),
                'date_document' => now()->toDateString(),
                'date_emission' => $date_permission ? Carbon::today()->toDateString()  :  Carbon::createFromFormat('d/m/Y', $request->get('date_emission'))->toDateString(),
                'note' => $request->get('i_note'),
                'magasin_id' => $magasin_id,
                'template_id' => $request->get('template_id') // Mise à jour du template
            ];
            if (GlobalService::get_modifier_reference()) {
                $data['reference'] = $request->get('i_reference');
            }
            if (in_array($type, ['dv', 'fa', 'fp', 'bc'])) {
                $data['date_expiration'] = $date_permission ? Carbon::today()->toDateString() :  Carbon::createFromFormat('d/m/Y', $request->get('date_expiration'))->toDateString();
            }
            $o_vente->update($data);
            $lignes = $request->get('lignes', []);
            $vente_ht = 0;
            $vente_ttc = 0;
            $vente_tva = 0;
            $vente_reduction = 0;
            $exist_lignes = [];
            if (count($lignes) > 0) {
                foreach ($lignes as $key => $ligne) {
                    if (array_key_exists('id', $ligne)) {
                        $o_ligne = VenteLigne::find($ligne['id']);
                    } else {
                        $o_ligne = new VenteLigne();
                    }
                    if ($ligne['i_reduction_mode'] === 'fixe') {
                        $reduction = $ligne['i_reduction'];
                    } else if ($ligne['i_reduction_mode'] === 'pourcentage') {
                        $reduction = round($ligne['i_prix_ht'] * (($ligne['i_reduction'] ?? 0) / 100), 2);
                    }
                    $o_ligne->article_id = $ligne['i_article_id'];
                    $o_ligne->unit_id = $ligne['i_unite'];
                    $o_ligne->vente_id = $o_ligne->vente_id != null ? $o_ligne->vente_id : $o_vente->id;
                    $o_ligne->mode_reduction = $ligne['i_reduction_mode'];
                    $o_ligne->nom_article = $ligne['i_article'];
                    $o_ligne->description = $ligne['i_description'];
                    $o_ligne->ht = $ligne['i_prix_ht'];
                    $o_ligne->revient = $ligne['i_prix_revient'];
                    $o_ligne->quantite = $ligne['i_quantite'];
                    $o_ligne->taxe = $ligne['i_taxe'];
                    $o_ligne->reduction = $ligne['i_reduction'] ?? 0;
                    $o_ligne->total_ttc = $this->calculate_ttc($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0);
                    $o_ligne->position = $key;
                    $o_ligne->magasin_id = $ligne['i_magasin_id'] ?? $magasin_id;
                    $o_ligne->save();
                    $exist_lignes[] = $o_ligne->id;
                    $vente_ht += ($o_ligne->ht) * $o_ligne->quantite;
                    $vente_reduction += $reduction * $o_ligne->quantite;
                    $vente_tva += $this->calculate_tva_amount($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0);
                    $vente_ttc += $o_ligne->total_ttc;
                }
                $o_vente->update([
                    'total_ht' => $vente_ht,
                    'total_tva' => $vente_tva,
                    'total_reduction' => $vente_reduction,
                    'total_ttc' => $vente_ttc,
                    'solde' => $vente_ttc - $o_vente->encaisser,
                    'statut_paiement' => PaiementService::get_payable_statut($vente_ttc, $o_vente->encaisser, (round($vente_ttc, 2) - $o_vente->encaisser))
                ]);
                VenteLigne::whereNotIn('id', $exist_lignes)->where('vente_id', $o_vente->id)->delete();
            }
            $o_vente->tags()->sync($request->get('balises', []));
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Modification')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference,
                ])
                ->log('Modification de ' . __('ventes.' . $type) . ' ' . ($o_vente->reference ?? '-'));
            return redirect()->route('ventes.afficher', ['id'=> $o_vente->id,'type' => $type])->with('success', __('ventes.' . $type) . '  modifié avec succès');
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            return redirect()->route('ventes.liste', ['type' => $type])->with('error', "Un problème est survenu lors de la modification");
        }
    }

    /**
     * @param string $type
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response|void
     */
    public function supprimer(string $type, int $id)
    {
        $this->guard_custom(['vente.supprimer']);

        if (\request()->ajax()) {
            $o_vente = Vente::find($id);
            if ($o_vente) {
                $o_vente->paiements()->delete();
                $o_vente->delete();
                activity()
                    ->causedBy(Auth::user())
                    ->event('Suppression')
                    ->withProperties([
                        'subject_type' => Vente::class,
                        'subject_id' => $o_vente->id,
                        'subject_reference' => $o_vente->reference,
                    ])
                    ->log('Suppression de '. __('ventes.' . $type).' ' . $o_vente->reference ?? '-' );

                return response('Document supprimé  avec succès', 200);
            } else {
                return response('Erreur', 404);
            }
        }
        abort(404);
    }

    /**
     * @param string $type
     * @param int $id
     * @return Response
     */
//



    public function telecharger(string $type, int $id)
    {
        $this->guard_custom(['vente.telecharger']);
        $o_vente = Vente::find($id);
        $pdf = $this->generate_pdf($o_vente);
        return $pdf->stream($o_vente->client->nom . ' ' . $o_vente->date_emission . " " . $o_vente->reference . ".pdf");
    }

    private function generate_pdf(Vente $o_vente){
        $o_template = $o_vente->template ?? DocumentsParametre::get()->first()->template;
        $template = 'documents.ventes.' . $o_template->blade;
        $images = [
            'image_en_tete' => $o_template->image_en_tete ? $this->base64_img($o_template->image_en_tete) : null,
            'image_en_bas' => $o_template->image_en_bas ? $this->base64_img($o_template->image_en_bas) : null,
            'image_arriere_plan' => $o_template->image_arriere_plan ? $this->base64_img($o_template->image_arriere_plan) : null,
            'cachet'=> $o_template->cachet? $this->base64_img($o_template->cachet):null,
            'logo' => $o_template->logo ? $this->base64_img($o_template->logo) : null,
        ];
        $type = $o_vente->type_document;
        return Pdf::loadView($template, compact('type', 'o_vente', 'o_template', 'images'))
            ->setOptions(['defaultFont' => 'Rubik'])
            ->set_option("isPhpEnabled", true);
    }

    /**
     * @param string $image
     * @return string
     */
    public function base64_img(string $image): string|null
    {
        $path = 'public/documents/' . $image;
        if (Storage::disk('external_storage')->exists($path)) {
            $type = pathinfo($image, PATHINFO_EXTENSION);
            $data = Storage::disk('external_storage')->get($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        }
        return null;
    }


    /**
     * @param string $type
     * @param int $id
     * @return Application|View|Factory|Response|JsonResponse|\Illuminate\Contracts\Foundation\Application|ResponseFactory
     */
    public function validation_modal(string $type, int $id): Application|View|Factory|Response|JsonResponse|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['vente.valider']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response()->json(__('ventes.' . $type) . " n'existe pas", 404);
        }
        if ($o_vente->statut !== 'brouillon') {
            return response(__('ventes.' . $type) . '  est déjà validé', 403);
        }
        if (!$o_vente->reference) {
            $reference = ReferenceService::generateReference($type, Carbon::createFromFormat('d/m/Y',$o_vente->date_emission));
        } else {
            $reference = $o_vente->reference;
        }
        return view('ventes.partials.validation_modal', compact('o_vente', 'type', 'reference'));
    }

    public function devalidation_modal(string $type, int $id): Application|View|Factory|Response|JsonResponse|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['vente.devalider']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response()->json(__('ventes.' . $type) . " n'existe pas", 404);
        }
        if ($o_vente->statut !== 'validé') {
            return response(__('ventes.' . $type) . '  doit être validé', 403);
        }
        //$reference = ReferenceService::generateReference($type,Carbon::createFromFormat('d/m/Y',$o_vente->date_emission));
        return view('ventes.partials.devalidation_modal', compact('o_vente', 'type'));
    }

    /**
     * @param string $type
     * @param int $id
     * @return RedirectResponse
     */
    public function valider(string $type, int $id): RedirectResponse
    {
        $this->guard_custom(['vente.valider']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            session()->flash('error', __('ventes.' . $type) . " n'existe pas");
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        }
        if ($o_vente->statut !== 'brouillon') {
            session()->flash('warning', __('ventes.' . $type) . '  est déjà validé !');
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        }
        DB::beginTransaction();
        try {
            if (!$o_vente->reference) {
                $reference = ReferenceService::generateReference($type, Carbon::createFromFormat('d/m/Y', $o_vente->date_emission));
                ReferenceService::incrementCompteur($type);
                $o_vente->update([
                    'reference' => $reference,
                ]);
            }
            $o_vente->update([
                'statut' => 'validé'
            ]);
            DB::commit();
            if (\request()->user()->can('stock.*')){
                $this->stock($id);
            }
            activity()
                ->causedBy(Auth::user())
                ->event('Validation')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference,
                ])
                ->log('Validation de '. __('ventes.' . $type).' ' . $o_vente->reference ?? '-' );
            session()->flash('success', __('ventes.' . $type) . '  validé !');
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', 'Erreur !');
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        }
    }

    public function devalider(string $type, int $id): RedirectResponse
    {
        $this->guard_custom(['vente.devalider']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            session()->flash('error', __('ventes.' . $type) . " n'existe pas");
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        }
        if ($o_vente->statut === 'brouillon') {
            session()->flash('warning', __('ventes.' . $type) . "  n'est pas validé !");
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        }
        DB::beginTransaction();
        //reverse stock
        if ($o_vente->statut === 'validé' && \request()->user()->can('stock.*')) {
            $this->stock_reverse($o_vente->id);
        }
        try {
            $o_vente->update([
                'statut' => 'brouillon'
            ]);
            if ($o_vente->type_document ==='av'){
                if(count($o_vente->vente_solde)>0){
                    foreach ($o_vente->vente_solde as $o_vente_solde){
                        $o_vente_solde->update([
                            'solde' => $o_vente_solde->solde +$o_vente->total_ttc,
                           'avoir_solde' => false,
                           'statut_paiement' => PaiementService::get_payable_statut($o_vente_solde->total_ttc,$o_vente_solde->encaisser,$o_vente_solde->solde+$o_vente->total_ttc)
                        ]);
                        $o_vente->vente_solde()->detach($o_vente_solde->id);
                    }
                }
            }
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Dévalidation')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference,
                ])
                ->log('Dévalidation de '. __('ventes.' . $type).' ' . $o_vente->reference ?? '-' );
            session()->flash('success', __('ventes.' . $type) . '  dévalidé !');
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::emergency($exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
            session()->flash('error', 'Erreur !');
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        }
    }



    /**
     * @param string $type
     * @param int $id
     * @return Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
     */
    public function paiement_modal(string $type, int $id): Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['paiement.vente']);
        $payable_modules = ModuleService::getPayabaleTypes();
        if (!in_array($type, $payable_modules)) {
            return response(__('ventes.' . $type) . " n'est pas payable !", 404);
        }
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response(__('ventes.' . $type) . " n'existe pas !", 404);
        }
        if ($o_vente->solde == 0) {
            return response(__('ventes.' . $type) . " est déja payé !", 403);
        }
        $comptes = Compte::all();
        $methodes = MethodesPaiement::where('actif','=','1')->get();
        $o_magasins = \request()->user()->magasins()->where('active','=','1')->get(['magasin_id as id','nom as text']);
        $magasins_count = Magasin::where('active', '=', '1')->count();
        return view('ventes.partials.paiement_modal', compact('o_vente', 'type', 'comptes', 'methodes', 'o_magasins', 'magasins_count'));
    }
    /**
     * @param string $type
     * @param int $id
     * @return Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
     */
    public function promesse_modal(string $type, int $id): Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['paiement.vente']);
        $payable_modules = ModuleService::getPayabaleTypes();
        if (!in_array($type, $payable_modules)) {
            return response(__('ventes.' . $type) . " n'est pas payable !", 404);
        }
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response(__('ventes.' . $type) . " n'existe pas !", 404);
        }
        if ($o_vente->solde == 0) {
            return response(__('ventes.' . $type) . " est déja payé !", 403);
        }
        return view('ventes.partials.promesse_modal', compact('o_vente', 'type'));
    }

    /**
     * @param Request $request
     * @param string $type
     * @param int $id
     * @return Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
     */
    public function payer(Request $request, string $type, int $id): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['paiement.vente']);
        $payable_modules = ModuleService::getPayabaleTypes();
        if (!in_array($type, $payable_modules)) {
            return response(__('ventes.' . $type) . " n'est pas payable !", 404);
        }
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response(__('ventes.' . $type . " n'existe pas !"), 404);
        }
        $magasin_id = $request->get('magasin_id');
        if (!$request->user()->magasins()->where('magasin_id', $magasin_id)->exists()) {
            session()->flash('warning', "Magasin n'est pas accessible");
            return redirect()->back()->withInput($request->input());
        }
        $attributes = [
            'i_compte_id' => "compte",
            'i_montant' => 'montant de paiement',
            'i_method_key' => 'méthode de paiement ',
            'i_date' => 'date prévu',
            'i_date_paiement' => 'date de paiement',
            'i_reference' => 'référence de chéque',
            'i_note' => 'note',
            'i_comptable' => 'comptable',
            'magasin_id' => 'magasin'
        ];
        $validation = Validator::make($request->all(), [
            'i_compte_id' => 'required|exists:comptes,id',
            'i_montant' => 'required|min:1|numeric|max:' . $o_vente->solde,
            'i_method_key' => ['required', 'exists:methodes_paiement,key'],
            'i_date' => [Rule::requiredIf(in_array($request->i_method_key, ['cheque', 'lcn'])), 'date_format:d/m/Y', 'nullable'],
            'i_date_paiement' => ['required', 'date_format:d/m/Y'],
            'i_reference' => [Rule::requiredIf(in_array($request->i_method_key, ['cheque', 'lcn'])), 'max:255'],
            'i_comptable' => ['nullable', Rule::in('1', '0')],
            'magasin_id' => ['required', 'exists:magasins,id']
        ], [], $attributes);
        if ($validation->fails()) {
            $messaget = '';
            foreach ($validation->messages()->getMessages() as $message) {
                $messaget .= $message[0] . '<br>';
            }
            return response($messaget, 400);
        }
        DB::beginTransaction();
        try {
            PaiementService::add_paiement(Vente::class, $o_vente->id, $request->all(), $request->get('magasin_id'));
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Paiement')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference,
                ])
                ->log('Paiement du montant '.$request->get('i_montant').' DH' );
            return response('Paiement réussi', 200);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            return response('Erreur', 500);
        }
    }

    /**
     * @param Request $request
     * @param string $type
     * @param int $id
     * @return Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
     */
    public function promesse(Request $request, string $type, int $id): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['promesse.sauvegarder']);
        $payable_modules = ModuleService::getPayabaleTypes();
        if (!in_array($type, $payable_modules)) {
            return response(__('ventes.' . $type) . " n'est pas payable !", 404);
        }
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response(__('ventes.' . $type . " n'existe pas !"), 404);
        }
        $attributes = [
            'i_montant' => 'montant de paiement',
            'i_date' => 'date',
            'i_type'=>'type'
        ];
        $validation = Validator::make($request->all(), [
            'i_montant' => 'required|min:1|numeric|max:' . $o_vente->solde,
            'i_date' => ['required', 'date_format:d/m/Y'],
            'i_type'=>['required','in:promesse,prevision']
        ], [], $attributes);
        if ($validation->fails()) {
            $messaget = '';
            foreach ($validation->messages()->getMessages() as $message) {
                $messaget .= $message[0] . '<br>';
            }
            return response($messaget, 400);
        }
        DB::beginTransaction();
        try {
            Promesse::create([
                'type' => $request->get('i_type'),
                'date'=>Carbon::createFromFormat('d/m/Y', $request->get('i_date'))->toDateString(),
                'montant'=>$request->get('i_montant'),
                'vente_id'=>$o_vente->id
            ]);
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Paiement')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference,
                ])
                ->log('Promesse du montant '.$request->get('i_montant').' DH' );
            return response('Promesse réussi', 200);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            return response('Erreur', 500);
        }
    }

    /**
     * @param string $type
     * @param int $id
     * @return Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
     */
    public function conversion_modal(string $type, int $id): Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['vente.convertir']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response(__('ventes.' . $type) . " n'exist pas !", 404);
        }
        if ($o_vente->statut === 'brouillon') {
            return response(__('ventes.' . $type) . " n'est pas validé!", 402);
        }
        $active_modules = ModuleService::getActiveModules();
        $types = array_filter(Vente::TYPES,function ($type) use($active_modules){
           return in_array($type,$active_modules);
       });

        return view('ventes.partials.conversion_modal', compact('types', 'o_vente'));
    }

    public function statut_com_modal(string $type, int $id): Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['vente.mettre_a_jour']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response(__('ventes.' . $type) . " n'exist pas !", 404);
        }
        $statut_com = Vente::$statut_com;
        return view('ventes.partials.statut_com_modal', compact('statut_com', 'o_vente'));
    }

    public function changer_status(Request $request, string $type, int $id)
    {
        $this->guard_custom(['vente.mettre_a_jour']);

        $o_vente = Vente::find($id);
        if (!$o_vente) {
            session()->flash('error', __('ventes.' . $type) . " n' existe pas !");
            return redirect()->route('ventes.liste', $type);
        }
        DB::beginTransaction();
        try {
            if ($request->input('i_type') !== $o_vente->statut_com) {
                $o_vente->statut_com = $request->input('i_type');
                $o_vente->save();

                DB::commit();
                activity()
                    ->causedBy(Auth::user())
                    ->event('Changement de statut')
                    ->withProperties([
                        'subject_type' => Vente::class,
                        'subject_id' => $o_vente->id,
                        'subject_reference' => $o_vente->reference,
                    ])
                    ->log('Changement de statut de ' . __('ventes.' . $type) . ' ' . ($o_vente->reference ?? null) . ' à ' . __('ventes.' . $request->get('i_type')));
            }
            session()->flash('success', 'Statut de  ' . __('ventes.' . $o_vente->type_document) . ' modifié  avec succès');
            return redirect()->route('ventes.afficher', [$o_vente->type_document, $o_vente->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', "Une erreur s'est produite dans le processus");
            return redirect()->route('ventes.liste', $type);
        }
    }


    public function clone_modal(string $type, int $id): Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['vente.cloner']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response(__('ventes.' . $type) . " n'exist pas !", 404);
        }
        return view('ventes.partials.clone_modal', compact( 'o_vente'));
    }

    public function history_modal(string $type, int $id) {
        $this->guard_custom(['vente.historique']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response(__('ventes.' . $type) . " n'exist pas !", 404);
        }
        $activities = Activity::whereJsonContains('properties->subject_id', $id)
            ->where('properties->subject_type', Vente::class)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ventes.partials.historique_modal', compact('activities'));
    }

    /**
     * @param Request $request
     * @param string $type
     * @param int $id
     * @return RedirectResponse
     */

    public function convertir(Request $request, string $type, int $id): RedirectResponse
    {
        $this->guard_custom(['vente.convertir']);
        $date_permission = !$request->user()->can('vente.date');
        $request->validate([
            'date_emission' => 'required|date_format:d/m/Y',
        ]);

        $o_vente = Vente::find($id);
        if (!$o_vente) {
            session()->flash('error', __('ventes.' . $type) . " n' existe pas !");
            return redirect()->route('ventes.liste', $type);
        }

        $types = array_diff(Vente::TYPES, [$type]);
        $active_modules = ModuleService::getActiveModules();

        if (!in_array($request->get('i_type'), $types) || !in_array($request->get('i_type'), $active_modules)) {
            session()->flash('error', "veuillez choisir un document validé vers lequel convertir");
            return redirect()->route('ventes.afficher', [$type, $id]);
        }


        DB::beginTransaction();
        try {
            $data = [
                'created_by' => auth()->id(),
                'client_id' => $o_vente->client_id,
                'commercial_id' => $o_vente->commercial_id,
                'reference' => null,
                "statut" => "brouillon",
                "objet" => $o_vente->objet,

                'date_document' => Carbon::now()->toDateString(),
                'date_emission' => $date_permission ? Carbon::today()->toDateString() : Carbon::createFromFormat('d/m/Y', $request->input("date_emission"))->toDateString(),
                'date_expiration' =>  $date_permission ? Carbon::today()->addDays(15)->toDateString() : Carbon::createFromFormat('d/m/Y', $request->input("date_emission"))->addDays(15)->toDateString(),
//                'date_document' => $o_vente->date_document,
//                'date_emission' => $dateEmission,
//                'date_expiration' => $o_vente->date_expiration ? Carbon::createFromFormat('d/m/Y', $o_vente->date_expiration)->toDateString() : null,

                'type_document' => $request->get('i_type'),
                'statut_paiement' => 'non_paye',
                'note' => $o_vente->note,
                'total_ht' => $o_vente->total_ht,
                'total_tva' => $o_vente->total_tva,
                'total_reduction' => $o_vente->total_reduction,
                'total_ttc' => $o_vente->total_ttc,
                'solde' => $o_vente->total_ttc,
                'magasin_id' => $o_vente->magasin_id,
            ];

            if ($o_vente->type_document === 'dv') {
                $data['statut_com'] = 'créé';
            }
            $o_converted_vente = Vente::create($data);
            $o_vente->documents_en_relation()->attach([$o_converted_vente->id]);

            foreach ($o_vente->lignes as $ligne) {
                $o_ligne = new VenteLigne();
                $o_ligne->vente_id = $o_converted_vente->id;
                $o_ligne->article_id = $ligne->article_id;
                $o_ligne->unit_id = $ligne->unit_id;
                $o_ligne->mode_reduction = $ligne->mode_reduction;
                $o_ligne->nom_article = $ligne->nom_article;
                $o_ligne->description = $ligne->description;
                $o_ligne->ht = $ligne->ht;
                $o_ligne->quantite = $ligne->quantite;
                $o_ligne->taxe = $ligne->taxe;
                $o_ligne->reduction = $ligne->reduction;
                $o_ligne->total_ttc = $ligne->total_ttc;
                $o_ligne->position = $ligne->position;
                $o_ligne->magasin_id = $ligne->magasin_id;
                $o_ligne->save();
            }

            DB::commit();

            activity()
                ->causedBy(Auth::user())
                ->event('Conversion')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference,
                ])
                ->log('Conversion de ' . __('ventes.' . $type) . ' ' . $o_vente->reference ?? '-' . ' à ' . __('ventes.' . $request->get('i_type')));

            session()->flash('success', __('ventes.' . $o_converted_vente->type_document) . ' ajouté avec succès');
            return redirect()->route('ventes.afficher', [$o_converted_vente->type_document, $o_converted_vente->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', "Une erreur s'est produite dans le processus");
            return redirect()->route('ventes.liste', $type);
        }
    }

    /**
     * @param string $type
     * @param int $id
     * @return RedirectResponse
     */
    public function cloner(string $type, int $id, Request $request): RedirectResponse
    {
        $this->guard_custom(['vente.cloner']);
        $date_permission = !$request->user()->can('vente.date');
        $rules = [
            'date_emission' => 'required|date_format:d/m/Y'
        ];
        $attr = [
            'date_emission' => 'date d\'émission'
        ];
        $validation = \Validator::make($request->all(), $rules, [], $attr);
        if ($validation->fails()) {
            $messaget = '';
            foreach ($validation->messages()->getMessages() as $message) {
                $messaget .= $message[0] . '<br>';
            }
            session()->flash('error', $messaget);
            return redirect()->back();
        }

        $o_vente = Vente::find($id);
        if (!$o_vente) {
            session()->flash('error', __('ventes.' . $type) . " n' exist pas !");
            return redirect()->route('ventes.liste', $type);
        }
        DB::beginTransaction();
        try {
            $data = [
                'created_by' => auth()->id(),
                'client_id' => $o_vente->client_id,
                'commercial_id' => $o_vente->commercial_id,
                'reference' => null,
                "statut" => "brouillon",
                "objet" => $o_vente->objet,
                'date_emission' =>  $date_permission ? Carbon::today()->toDateString()  : Carbon::createFromFormat('d/m/Y', $request->input("date_emission"))->toDateString(),
                'date_document' => Carbon::now()->toDateString(),
                'date_expiration' => $date_permission ? Carbon::today()->addDays(15)->toDateString() : Carbon::createFromFormat('d/m/Y', $request->input("date_emission"))->addDays(15)->toDateString(),
                'type_document' => $type,
                'statut_paiement' => 'non_paye',
                'note' => $o_vente->note,
                'total_ht' => $o_vente->total_ht,
                'total_tva' => $o_vente->total_tva,
                'total_reduction' => $o_vente->total_reduction,
                'total_ttc' => $o_vente->total_ttc,
                'solde' => $o_vente->total_ttc,
                'magasin_id'=> $o_vente->magasin_id
            ];
            if ($o_vente->type_document === 'dv') {
                $data['statut_com'] = 'créé';
            }

            $o_cloned_vente = Vente::create($data);
            foreach ($o_vente->lignes as $ligne) {
                $o_ligne = new VenteLigne();
                $o_ligne->vente_id = $o_cloned_vente->id;
                $o_ligne->article_id = $ligne->article_id;
                $o_ligne->unit_id = $ligne->unit_id;
                $o_ligne->mode_reduction = $ligne->mode_reduction;
                $o_ligne->nom_article = $ligne->nom_article;
                $o_ligne->description = $ligne->description;
                $o_ligne->ht = $ligne->ht;
                $o_ligne->quantite = $ligne->quantite;
                $o_ligne->taxe = $ligne->taxe;
                $o_ligne->reduction = $ligne->reduction;
                $o_ligne->total_ttc = $ligne->total_ttc;
                $o_ligne->position = $ligne->position;
                $o_ligne->magasin_id = $ligne->magasin_id;
                $o_ligne->save();
            }
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Clonage')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference,
                ])
                ->log('Clonage de '. __('ventes.' . $type).' ' . $o_vente->reference ?? '-' );

            session()->flash('success', __('ventes.' . $type) . ' cloné avec succès');
            return redirect()->route('ventes.afficher', [$type, $o_cloned_vente->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', "Une erreur s'est produite dans le processus");
            return redirect()->route('ventes.liste', $type);
        }
    }

    /**
     * @param Request $request
     * @param string $type
     * @return Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
     */
    public function convertir_multi_modal(Request $request, string $type): Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['vente.convertir_mass']);

        $ventes = Vente::whereIn('id', $request->get('ids'))->get();
        if ($ventes->groupBy('client_id')->count() > 1) {
            return response('Vous ne pouvez pas convertir des documents avec un client différent', 400);
        }
        $types = array_diff(Vente::TYPES, [$type]);
        $ventes_ids = implode(',', $ventes->pluck('id')->toArray());
        return view('ventes.partials.conversion_multi_modal', compact('type', 'types', 'ventes_ids'));
    }

    /**
     * @param Request $request
     * @param string $type
     * @return RedirectResponse
     */
    public function convertir_multi(Request $request, string $type): RedirectResponse
    {
        $this->guard_custom(['vente.convertir_mass']);

        $ids = explode(',', $request->get('ids'));
        $ventes = Vente::whereIN('id', $ids)->where('type_document', $type)->get();
        DB::beginTransaction();
        try {
            $data = [
                'created_by' => auth()->id(),
                'client_id' => $ventes[0]->client_id,
                'commercial_id' => null,
                'reference' => null,
                "statut" => "brouillon",
                "objet" => null,
                'date_document' => now()->toDateString(),
                'date_emission' => now()->toDateString(),
                'type_document' => $request->get('i_type'),
                'statut_paiement' => 'non_paye',
                'note' => null,
                'magasin_id'=>$ventes[0]->magasin_id
            ];
            if (in_array($type, ['dv', 'fa', 'fp', 'bc'])) {
                $data['date_expiration'] = now()->toDateString();
            }
            $o_vente = Vente::create($data);
            $lignes = [];
            foreach ($ventes as $vente) {
                foreach ($vente->lignes as $ligne) {
                    $lignes[] = $ligne;
                }
            }
            $vente_ht = 0;
            $vente_ttc = 0;
            $vente_tva = 0;
            $vente_reduction = 0;
            if (count($lignes) > 0) {
                foreach ($lignes as $key => $ligne) {
                    if ($ligne['mode_reduction'] === 'fixe') {
                        $reduction = $ligne['reduction'];
                    } else if ($ligne['mode_reduction'] === 'pourcentage') {
                        $reduction = $ligne['ht'] * ($ligne['reduction'] / 100);
                    }
                    $o_ligne = new VenteLigne();
                    $o_ligne->vente_id = $o_vente->id;
                    $o_ligne->article_id = $ligne['article_id'];
                    $o_ligne->unit_id = $ligne['unit_id'];
                    $o_ligne->mode_reduction = $ligne['mode_reduction'];
                    $o_ligne->nom_article = $ligne['nom_article'];
                    $o_ligne->description = $ligne['description'];
                    $o_ligne->ht = $ligne['ht'];
                    $o_ligne->quantite = $ligne['quantite'];
                    $o_ligne->taxe = $ligne['taxe'];
                    $o_ligne->reduction = $ligne['reduction'] ?? 0;
                    $o_ligne->total_ttc = $this->calculate_ttc($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0);
                    $o_ligne->position = $key;
                    $o_ligne->magasin_id = $ligne['magasin_id'];
                    $o_ligne->save();
                    $vente_ht += ($o_ligne->ht) * $o_ligne->quantite;
                    $vente_reduction += $reduction * $o_ligne->quantite;
                    $vente_tva += $this->calculate_tva_amount($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0);
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
            $o_vente->document_parent()->attach($ids);
            DB::commit();
            return redirect()->route('ventes.liste', ['type' => $request->get('i_type')])->with('success', __('ventes.' . $request->get('i_type')) . " ajouté avec succès");
        } catch (Exception $exception) {
            LogService::logException($exception);
            DB::rollBack();
            return redirect()->route('ventes.liste', ['type' => $type])->with('error', "Une erreur s'est produite lors de l'ajout du Vente");
        }
    }

    public function solde_select(Request $request,$type){
        if ($request->ajax()){
            $o_avoir = Vente::findOrFail($request->get('id'));
            $search = '%'.$request->get('term').'%';
            $result= Vente::whereIn('type_document',['fa'])->where('statut','validé')->whereNot('type_document','av')->where('client_id',$o_avoir->client_id)
                ->where('reference','LIKE',$search)->where('total_ttc','>=',$o_avoir->total_ttc)->get(['reference as text','id','total_ttc']);
            return $result;
        }
        abort(404);
    }

    public function solde(Request $request,$type,$id){
        $o_avoir = Vente::findOrFail($id);
        $o_vente_a_solde = Vente::findOrFail($request->get('vente_id'));
        if (!$o_avoir || $o_avoir->type_document != $type || !$o_vente_a_solde) {
            session()->flash('error', __('ventes.' . $type) . " n' exist pas !");
            return redirect()->route('ventes.liste', $type);
        }
        if ($o_avoir->type_document != 'av'){
            session()->flash('warning'," Le document doit être avoir !");
            return redirect()->route('ventes.afficher', [$type,$id]);
        }
        if ($o_avoir->statut !== 'validé'){
            session()->flash('warning',__('ventes.' . $type) . '  doit être validé');
            return redirect()->route('ventes.afficher', [$type,$id]);
        }
        if ($o_vente_a_solde->client_id != $o_avoir->client_id){
            session()->flash('error',__('ventes.' . $type) . '  doit être de même client');
            return redirect()->route('ventes.afficher', [$type,$id]);
        }
        $o_vente_a_solde->update([
           'solde' => $o_vente_a_solde->solde - $o_avoir->total_ttc,
            'statut_paiement' => PaiementService::get_payable_statut($o_vente_a_solde->total_ttc,$o_vente_a_solde->encaisser + $o_avoir->total_ttc,$o_vente_a_solde->solde - $o_avoir->total_ttc),
            'avoir_solde' => true,
        ]);
        $o_avoir->vente_solde()->attach($o_vente_a_solde->id);
        session()->flash('success',__('ventes.'.$o_vente_a_solde->type_document).' est soldé !');
        return redirect()->route('ventes.afficher',[$o_vente_a_solde->type_document,$o_vente_a_solde->id]);

    }

    /**
     * Marque une vente comme contrôlée.
     *
     * @param int $id L'identifiant de la vente à contrôler.
     * @return \Illuminate\Http\JsonResponse Retourne une réponse JSON indiquant le succès ou l'échec de l'opération.
     */
    public function controle($type, $id)
    {
        $this->guard_custom(['vente.controler']);
        try {

            $vente = Vente::findOrFail($id);
            $vente->update([
                'is_controled' => true,
                'controled_at' => now(),
                'controled_by' => auth()->id()
            ]);

            activity()
                ->causedBy(Auth::user())
                ->event('Contrôle')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $vente->id,
                    'subject_reference' => $vente->reference,
                ])
                ->log('Contrôle effectué sur ' . __('ventes.' . $type) . ' ' . $vente->reference);

            return response()->json([
                'success' => true,
                'message' => 'Vente contrôlée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du contrôle de la vente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function attacher_piece_jointe(Request $request, $type, $id)
    {
        $this->guard_custom(['vente.piece_jointe_attacher']);
        if (!GlobalSetting::first()->pieces_jointes) {
            abort(404);
        }
        // Validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'url' => 'required|url'
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {
            $o_vente = Vente::find($id);
            if (!$o_vente) {
                abort(404);
            }
            // ajouter une piece jointe
            $piece_jointe = new PieceJointe();
            $piece_jointe->document()->associate($o_vente);
            $piece_jointe->title = $request->get('title');
            $piece_jointe->url = $request->get('url');
            $piece_jointe->save();

            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Piece jointe')
                ->withProperties([
                    'subject_type' => Vente::class,
                    'subject_id' => $o_vente->id,
                    'subject_reference' => $o_vente->reference ?? '',
                ])
            ->log('Pièce jointe ' . ' ' . $piece_jointe->title . ' attachée au document' );
            session()->flash('success', "Pièce jointe attachée avec succès");
            return response('Pièce jointe attaché', 200);
        } catch (Exception $e) {
            LogService::logException($e);
            DB::rollBack();
            return response('Erreur lors de l\'attachement de la pièce jointe', 500);
        }
    }


    public function supprimer_piece_jointe($type, $id){
        $this->guard_custom(['vente.piece_jointe_supprimer']);

        $o_piece_jointe = PieceJointe::findOrFail($id);
        if (!$o_piece_jointe) {
            abort(404);
        }
        $o_piece_jointe->delete();
        $o_vente = Vente::find($o_piece_jointe->document_id);
        activity()
            ->causedBy(Auth::user())
            ->event('Piece jointe')
            ->withProperties([
                'subject_type' => Vente::class,
                'subject_id' => $o_vente->id,
                'subject_reference' => $o_vente->reference,
            ])
            ->log('Pièce jointe ' . ' ' . $o_piece_jointe->title . ' supprimée du document' );
        session()->flash('success', "Pièce jointe supprimée avec succès");
        return redirect()->route('ventes.afficher', [$type, $o_piece_jointe->document_id]);
    }
    public function relancer_modal(string $type, int $id)
    {
        $this->guard_custom(['vente.relancer']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response()->json(__('ventes.' . $type) . " n'existe pas", 404);
        }
        $template = RelanceSettings::getActive($type);

        if (!$template) {
            return response('Aucun modèle de relance actif trouvé', 500);
        }
        return view('ventes.partials.relancer_modal', compact('o_vente', 'type', 'template'));
    }

    public function edit_template_relancer_modal(string $type, int $id)
    {
        $this->guard_custom(['vente.relancer']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response()->json(__('ventes.' . $type) . " n'existe pas", 404);
        }
        $template = RelanceSettings::getActive($type);

        if (!$template) {
            return response('Aucun modèle de relance actif trouvé', 500);
        }
        return view('ventes.partials.edit_template_modal', compact('o_vente', 'type', 'template'));
    }

    public function relancer(Request $request , $type, $id)
    {
        $this->guard_custom(['vente.relancer']);
        $o_vente = Vente::find($id);
        if (!$o_vente) {
            return response(__('ventes.' . $type) . " n'existe pas !", 404);
        }

        $template = RelanceSettings::getActive($type);
        if (!$template) {
            session()->flash('error', "Aucun modèle de relance actif trouvé");
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        }

        $destinations = $o_vente->client->email;
        if (!$destinations){
            session()->flash('error', "Aucun email trouvé pour le client");
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        }

        try {

            $emailBody = str_replace(
                [
                    '[CLIENT]',
                    '[DATE_EXPIRATION]',
                    '[DATE_EMISSION]',
                    '[TOTAL]',
                    '[SOLDE]',
                    '[REFERENCE]'
                ],
                [
                    $o_vente->client->nom ,
                    $o_vente->date_expiration ,
                    $o_vente->date_emission ,
                    $o_vente->total_ttc,
                    $o_vente->solde,
                    $o_vente->reference,
                ],
                $request->has('content') ? $request->get('content') : $template->content
            );
            $subject =
                str_replace(
                    [
                        '[CLIENT]',
                        '[DATE_EXPIRATION]',
                        '[DATE_EMISSION]',
                        '[TOTAL]',
                        '[SOLDE]',
                        '[REFERENCE]'
                    ],
                    [
                        $o_vente->client->nom ,
                        $o_vente->date_expiration ,
                        $o_vente->date_emission ,
                        $o_vente->total_ttc,
                        $o_vente->solde,
                        $o_vente->reference,
                    ],$request->has('subject') ? $request->get('subject'): $template->subject
                );

            if ($template->emails_cc) {
                $emails_cc = explode(';', $template->emails_cc);
            } else {
                $emails_cc = [];
            }

            $base64PdfContent = base64_encode($this->generate_pdf($o_vente)->output());
            $filename = $o_vente->client->nom . ' ' . $o_vente->date_emission . " " . $o_vente->reference . ".pdf";
            $attachments = [
                [
                    'data' => $base64PdfContent,
                    'name' => $filename,
                    'is_base64' => true,
                    'options' => [
                        'mime' => 'application/pdf',
                    ]
                ]
            ];
            $smtpService = new SmtpService();
            $smtpService->send($destinations, $subject, $emailBody, \Session::get('tenant'), $attachments, $emails_cc);

            session()->flash('success', "Email de relance envoyé avec succès");
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        } catch (\Exception $e) {
            LogService::logException($e);
            session()->flash('error', "Une erreur s'est produite lors de l'envoi de l'email de relance");
            return redirect()->route('ventes.afficher', [$type, $o_vente->id]);
        }
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
        $totalNonPaye = \App\Models\Vente::where('client_id', $client->id)
            ->whereIn('type_document', $encaissementTypes)
            ->where('statut_paiement', 'non_paye')
            ->sum('total_ttc');
        return [
            'limite_credit' => $limiteCredit,
            'total_non_paye' => $totalNonPaye,
            'encaissement_types' => $encaissementTypes
        ];
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
     * @param $vente
     * @return void
     */
    function stock_reverse($vente): void
    {
        StockService::stock_revert(Vente::class, $vente);
    }

    /**
     * @param $vente
     * @return void
     */
    function stock($vente): void
    {
        $modules = ModuleService::getModules();
        $o_vente = Vente::find($vente);
        if ($o_vente->statut === 'validé') {
            foreach ($o_vente->lignes as $ligne) {
                if ($ligne['article_id']) {
                    if (in_array($o_vente->type_document, $modules->where('action_stock', 'sortir')->pluck('type')->toArray())) {
                        StockService::stock_sortir($ligne['article_id'], $ligne->quantite, Carbon::createFromFormat('d/m/Y', $o_vente->date_emission)->format('Y-m-d'), Vente::class, $o_vente->id, $ligne->magasin_id);
                    } elseif (in_array($o_vente->type_document, $modules->where('action_stock', 'entrer')->pluck('type')->toArray())) {
                        StockService::stock_entre($ligne['article_id'], $ligne->quantite, Carbon::createFromFormat('d/m/Y', $o_vente->date_emission)->format('Y-m-d'), Vente::class, $o_vente->id, $ligne->magasin_id);
                    }
                }
            }
        }
    }
}
