<?php

namespace App\Http\Controllers;

use App\Http\Requests\AchatStoreRequest;
use App\Http\Requests\AchatUpdateRequest;
use App\Models\Achat;
use App\Models\AchatLigne;
use App\Models\Commercial;
use App\Models\Compte;
use App\Models\DocumentsParametre;
use App\Models\Fournisseur;
use App\Models\GlobalSetting;
use App\Models\Magasin;
use App\Models\MethodesPaiement;
use App\Models\PieceJointe;
use App\Models\Taxe;
use App\Models\Template;
use App\Models\Unite;
use App\Services\FileService;
use App\Services\GlobalService;
use App\Services\LogService;
use App\Services\ModuleService;
use App\Services\PaiementService;
use App\Services\ReferenceService;
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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\DataTables;
use function auth;

/**
 *
 */
class AchatController extends Controller
{
    /**
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View|Application|JsonResponse|RedirectResponse|\Illuminate\View\View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function liste(Request $request, string $type)
    {
        $this->guard_custom(['achat.liste']);
        // ------------------- ### Ajax logic ### -------------------
        if ($request->ajax()) {
            $o_achat = Achat::query()->where('type_document', $type)->whereYear('date_emission', session()->get('exercice'))->with('fournisseur');
            // ------------------- ### Filters ### -------------------

            if ($request->get('fournisseur_id')) {
                $o_achat->where('fournisseur_id', $request->get('fournisseur_id'));
            }
            if ($request->get('statut')) {
                $o_achat->where('statut', $request->get('statut'));
            }
            if ($request->get('reference')) {
                $search = '%' . $request->get('reference') . '%';
                $o_achat->where('reference', 'LIKE', $search);
            }
            if ($request->get('reference_interne')) {
                $search = '%' . $request->get('reference_interne') . '%';
                $o_achat->where('reference_interne', 'LIKE', $search);
            }
            if ($request->get('total_ttc') != null) {
                $search = $request->get('total_ttc');
                $o_achat->where('total_ttc', $search);
            }
            if ($request->get('date_emission')) {
                $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_emission'))[0]))->toDateString();
                $end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_emission'))[1]))->toDateString();
                if ($start === $end) {
                    $o_achat->where(function ($query) use ($start) {
                        $query->whereDate('date_emission', $start)->orWhereNull('date_emission');
                    });
                }
                $o_achat->where(function ($query) use ($start, $end) {
                    $query->whereDate('date_emission', '>=', $start)->whereDate('date_emission', '<=', $end)->orWhereNull('date_emission');
                });
            }
            if ($request->get('date_expiration')) {
                $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_expiration'))[0]))->toDateString();
                $end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_expiration'))[1]))->toDateString();
                if ($start === $end) {
                    $o_achat->where(function ($query) use ($start) {
                        $query->whereDate('date_expiration', $start)->orWhereNull('date_expiration');
                    });
                }
                $o_achat->where(function ($query) use ($start, $end) {
                    $query->whereDate('date_expiration', '>=', $start)->whereDate('date_expiration', '<=', $end)->orWhereNull('date_expiration');
                });
            }
            if ($request->get('statut_paiement')) {
                $o_achat->where('statut_paiement', $request->get('statut_paiement'));
            }
            if (count($request->get('balises', [])) > 0) {
                $balises = $request->get('balises', []);
                $ids = DB::table('taggables')->where('taggable_type', Achat::class)->whereIn('tag_id', $balises)->pluck('taggable_id');
                $o_achat->whereIn('id', $ids);
            }
            if ($request->filled('controle')){
                $o_achat->where('is_controled',$request->get('controle'));
            }
            if ($request->get('order') && $request->get('columns')) {
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order) {
                    $o_achat->orderByRaw('' . $columns[$order['column']]['data'] . ' ' . $order['dir']);
                }
            }
            // ------------------- ### End of filters ### -------------------
//            $o_achat = $o_achat->get();
            $table = DataTables::of($o_achat)->order(function ($e) {

            });
            // ------------------- ### Columns formatting ### -------------------
            $table->editColumn('fournisseur_id', function ($vents) {
                return $vents->fournisseur->nom;
            })->addColumn('actions', function ($row) use ($type) {
                $show = 'afficher';
                $edit = 'modifier';


                $delete = 'supprimer';
                $crudRoutePart = 'achats';
                $id = $row?->id;
                $attrs = ['type' => $type];
                if ($row->statut !== 'validé') {
                    $action = view(
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

                    $action .= '<button id="clone-btn" data-href="' . route('achats.clone_modal', [$type, $row->id]) . '" class="btn  btn-sm btn-soft-info mx-1">
                <i class="fa fa-clone"></i>
             </button>';
                    return $action;
                } else {
                    $action = view(
                        'partials.__datatable-action',
                        compact(
                            'show',
                            'crudRoutePart',
                            'id',
                            'attrs'
                        ))->render();
                    $action .= '<button id="clone-btn" data-href="' . route('achats.clone_modal', [$type, $row->id]) . '" class="btn  btn-sm btn-soft-info mx-1">
                <i class="fa fa-clone"></i>
             </button>';
                    return $action;
                }
            })->editColumn('reference', function ($row) {
                return $row->reference;
//            })->editColumn('date_expiration',function ($row){
//                return $row->date_expiration ? Carbon::make($row->date_expiration)->format('d/m/Y'):null;
            })->editColumn('total_ttc', function ($row) {
                return ($row->total_ttc ?? 0) . ' MAD';
            })->editColumn('objet', function ($row) {
                return '<p class="text-truncate text-nowrap m-0 p-0" style="max-width: 250px">' . $row->objet . '</p>';
            })->editColumn('statut', function ($row) {
                $color = 'secondary';
                if ($row->statut == "validé") {
                    $color = 'success';
                }
                return '<div class="badge w-100 bg-soft-' . $color . '" >' . ucfirst($row->statut) . '</div>';
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
                return '<div class="badge w-100 bg-soft-' . $color . '" >' . __('achats.' . $row->statut_paiement) . '</div>';
            })->editColumn('reference_interne', function ($row) {
                return $row->reference_interne ?? 'Brouillon';
            });
            $table->rawColumns(['actions', 'selectable_td', 'objet', 'statut_paiement', 'statut']);
            // ------------------- ### End of columns formatting ### -------------------
            return $table->make();

        }
        // ------------------- ### End of ajax logic ### -------------------
        $o_client = Fournisseur::get(['id', 'nom as text']);
        $status = Achat::$status;
        $status_paiement = Achat::STATUTS_DE_PAIEMENT;
        $payabale_types = ModuleService::getPayabaleTypes();
        return view("achats.liste", compact('o_client', "type", 'status', 'status_paiement', 'payabale_types'));
    }

    /**
     * @param string $type
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View|Application|\Illuminate\View\View
     */
    public function ajouter(string $type): Factory|View|Application|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->guard_custom(['achat.sauvegarder']);
        $o_commercils = Commercial::get(['id', 'nom as text', "commission_par_defaut"]);
        $o_unites = Unite::get(['id', "nom"]);
        $o_taxes = Taxe::where('active', '1')->get(["valeur", "nom"]);
        $o_magasins = \request()->user()->magasins()->where('active', '=', '1')->get(['magasin_id as id', 'nom as text']);
        $magasins_count = Magasin::where('active', '=', '1')->count();
        $globals = GlobalService::get_all_globals();
        $templates = Template::all();


        return view('achats.ajouter', compact('o_commercils', 'type', "o_unites", "o_taxes", "o_magasins", 'globals', 'templates', 'magasins_count'));
    }

    /**
     * @param AchatStoreRequest $request
     * @param string $type
     * @return RedirectResponse
     */
    public function sauvegarder(AchatStoreRequest $request, string $type): RedirectResponse
    {
        $this->guard_custom(['achat.sauvegarder']);
        $globals = GlobalService::get_all_globals();
        $date_permission = !$request->user()->can('achat.date');
        DB::beginTransaction();
        try {
            // ------------------- ### Magasin ### -------------------
            $magasin_id = $request->get('magasin_id') ?? Magasin::first()->id;
            if (!$request->user()->magasins()->where('magasin_id', $magasin_id)->exists()) {
                session()->flash('warning', "Magasin n'est pas accessible");
                return redirect()->back()->withInput($request->input());
            }
            // ------------------- ### Data of document defining ### -------------------
            $data = [
                'created_by' => auth()->id(),
                'fournisseur_id' => $request->get('fournisseur_id'),
                'reference' => $request->get('reference'),
                'commercial_id' => $request->get('commercial_id') ?? null,
                "statut" => "brouillon",
                "objet" => $request->get('objet'),
                'date_document' => now()->toDateString(),
                'date_emission' => $date_permission ? Carbon::today()->toDateString() :   Carbon::createFromFormat('d/m/Y', $request->get('date_emission'))->toDateString(),
                'type_document' => $type,
                'statut_paiement' => 'non_paye',
                'note' => $request->get('i_note'),
                'magasin_id' => $magasin_id,
                'template_id' => $request->get('template_id'),
            ];
            // ------------------- ### Check if date d'expiration is required ### -------------------
            if (in_array($type, ['dva', 'faa', 'bca'])) {
                $data['date_expiration'] = $date_permission ? Carbon::today()->addDays(15)->toDateString() :  Carbon::createFromFormat('d/m/Y', $request->get('date_expiration'))->toDateString();
            }
            // ------------------- ### End of data defining ### -------------------
            $o_achat = Achat::create($data);
            // ------------------- ### Document lines logic ### -------------------
            $lignes = $request->get('lignes', []);
            $achat_ht = 0;
            $achat_ttc = 0;
            $achat_tva = 0;
            $achat_reduction = 0;
            // ------------------- ### Check if the document has lines to loop over the lines ### -------------------
            if (count($lignes) > 0) {
                foreach ($lignes as $key => $ligne) {
                    if ($ligne['i_reduction_mode'] === 'fixe') {
                        $reduction = $ligne['i_reduction'];
                    } else if ($ligne['i_reduction_mode'] === 'pourcentage') {
                        $reduction = $ligne['i_prix_ht'] * ($ligne['i_reduction'] / 100);
                    }
                    $o_ligne = new AchatLigne();
                    $o_ligne->achat_id = $o_achat->id;
                    $o_ligne->article_id = $ligne['i_article_id'];
                    $o_ligne->unite_id = $ligne['i_unite'];
                    $o_ligne->mode_reduction = $ligne['i_reduction_mode'];
                    $o_ligne->nom_article = $ligne['i_article'];
                    $o_ligne->description = $ligne['i_description'];
                    $o_ligne->ht = $ligne['i_prix_ht'];
                    $o_ligne->quantite = $ligne['i_quantite'];
                    $o_ligne->taxe = $ligne['i_taxe'];
                    $o_ligne->reduction = $ligne['i_reduction'] ?? 0;
                    $o_ligne->total_ttc = $this->calculate_ttc($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0);
                    $o_ligne->position = $key;
                    $o_ligne->magasin_id = $ligne['i_magasin_id'] ?? $magasin_id;
                    $o_ligne->save();
                    $achat_ht += ($o_ligne->ht) * $o_ligne->quantite;
                    $achat_reduction += $reduction * $o_ligne->quantite;
                    $achat_tva += $this->calculate_tva_amount($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0);
                    $achat_ttc += $o_ligne->total_ttc;
                }
                $o_achat->update([
                    'total_ht' => $achat_ht,
                    'total_tva' => $achat_tva,
                    'total_reduction' => $achat_reduction,
                    'total_ttc' => $achat_ttc,
                    'debit' => $achat_ttc,
                    'credit' => 0
                ]);
            }
            $o_achat->tags()->sync($request->get('balises', []));
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Création')
                ->withProperties([
                    'subject_type' => Achat::class,
                    'subject_id' => $o_achat->id,
                    'subject_reference' => $o_achat->reference_interne,
                ])
                ->log('Création de ' . __('achats.' . $type) . ' ' . $o_achat->reference_interne ?? '-');
            session()->flash('success', __('achats.' . $type) . " ajouté avec succès");
            return redirect()->route('achats.afficher', ['id' => $o_achat->id, 'type' => $type]);
        } catch (Exception $exception) {
            LogService::logException($exception);
            DB::rollBack();
            session()->flash('error', __('achats.' . $type) . " n'a pas pu être ajouté");
            return redirect()->route('achats.liste', ['type' => $type])->with('error', "Une erreur s'est produite lors de l'ajout du Achat");
        }
    }

    /**
     * @param string $type
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View|Application|RedirectResponse|\Illuminate\View\View
     */
    public function modifier(string $type, int $id): \Illuminate\Contracts\Foundation\Application|Factory|View|Application|RedirectResponse|\Illuminate\View\View
    {
        $this->guard_custom(['achat.mettre_a_jour']);
        $o_achat = Achat::with('lignes.article')->find($id);
        if (!$o_achat) {
            abort(404);
        }
        if ($o_achat->statut === 'validé') {
            session()->flash('warning', __('achats.' . $type) . " est déjà confirmé !");
            return redirect()->route('achats.liste', [$type]);
        }
        $o_commercils = Commercial::get(['id', 'nom', "commission_par_defaut"]);
        $o_unites = Unite::get(['id', "nom"]);
        $o_taxes = Taxe::where('active', '1')->get(["valeur", "nom"]);
        $modifier_reference = $o_achat->reference && GlobalService::get_modifier_reference();
        $o_magasins = \request()->user()->magasins()->get(['magasin_id as id', 'nom as text']);
        $globals = GlobalService::get_all_globals();
        $templates = Template::all();
//        $templates = Template::where('nom', '!=', 'marchandise')->get();
        $magasins_count = Magasin::where('active', '=', '1')->count();


        return view('achats.modifer', compact('o_commercils', 'magasins_count', 'type', "o_unites", "o_taxes", 'o_achat', 'modifier_reference', 'o_magasins', 'globals', 'templates'));
    }

    /**
     * @param string $type
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|Factory|View|Application|RedirectResponse|\Illuminate\View\View
     */
    public function afficher(string $type, int $id)
    {
        $this->guard_custom(['achat.afficher']);
        $o_achat = Achat::find($id);
        if (!$o_achat || $o_achat->type_document !== $type) {
            return redirect()->route('achats.liste', $type)->with('error', __('achats.' . $type) . " n'existe pas");
        }
        $payabale_types = ModuleService::getPayabaleTypes();
        $is_controled = GlobalSetting::first()->controle && auth()->user()->can('achat.controler');
        $globals = GlobalService::get_all_globals();
        return view('achats.afficher', compact('o_achat', 'type', 'payabale_types', 'is_controled', 'globals'));
    }

    /**
     * @param Request $request
     * @param $type
     * @param $id
     * @return RedirectResponse
     */
    public function mettre_a_jour(AchatUpdateRequest $request, $type, $id)
    {
        $this->guard_custom(['achat.metter_a_jour']);
        $o_achat = Achat::find($id);
        $date_permission = !$request->user()->can('achat.date');
        if (!$o_achat) {
            abort(404);
        }
        if ($o_achat->statut === 'confirmé') {
            session()->flash('warning', __('achats.' . $type) . " est déjà confirmé !");
            return redirect()->route('achats.liste', [$type]);
        }
        DB::beginTransaction();
        try {
            // ------------------- ### Magasin ### -------------------
            $magasin_id = $request->get('magasin_id') ?? $o_achat->magasin_id ?? Magasin::first()->id;
            if (!$request->user()->magasins()->where('magasin_id', $magasin_id)->exists()) {
                session()->flash('warning', "Magasin n'est pas accessible");
                return redirect()->back()->withInput($request->input());
            }
            // ------------------- ### Define data of document ### -------------------
            $data = [
                'reference' => $request->get('reference'),
                'fournisseur_id' => $request->get('fournisseur_id'),
                "objet" => $request->get('objet'),
                'date_emission' => $date_permission ? Carbon::today()->toDateString() : Carbon::createFromFormat('d/m/Y', $request->get('date_emission'))->toDateString(),
                'type_document' => $type,
                'note' => $request->get('i_note'),
                'magasin_id' => $magasin_id,
                'template_id' => $request->get('template_id')
            ];
            // ------------------- ### Check if document has file and store it ### -------------------
            if ($request->hasFile('fichier_document')) {
                $data['fichier_document'] = FileService::storageUploadFile($request->file('fichier_document'), 'achats' . DIRECTORY_SEPARATOR . $request->get('fournisseur_id'));
            } elseif ($request->get('i_supprimer_fichier')) {
                $data['fichier_document'] = null;
            }
            // ------------------- ### Check if date d'expiration is required ### -------------------
            if (in_array($type, ['dva', 'faa', 'fpa', 'bca'])) {
                $data['date_expiration'] = $date_permission ? Carbon::today()->addDays(15)->toDateString() :  Carbon::createFromFormat('d/m/Y', $request->get('date_expiration'))->toDateString();
            }
            // ------------------- ### Check if there is details or not ### -------------------
            if (!$request->get('details')) {
                $data['total_ht'] = +number_format($request->get('total_ht'), 3, '.', '');
                $data['total_ttc'] = +number_format($request->get('total_ttc'), 3, '.', '');
                $data['total_tva'] = $data['total_ttc'] - $data['total_ht'];
                $data['debit'] = $data['total_ttc'];
            }
            if (GlobalService::get_modifier_reference()) {
                $data['reference_interne'] = $request->get('reference_interne');
            }
            // ------------------- ### End of data defining ### -------------------
            $o_achat->update($data);
            $lignes = $request->get('lignes', []);
            $achat_ht = 0;
            $achat_ttc = 0;
            $achat_tva = 0;
            $achat_reduction = 0;
            $exist_lignes = [];
            foreach ($lignes as $key => $ligne) {
                if (array_key_exists('id', $ligne)) {
                    $o_ligne = AchatLigne::find($ligne['id']);
                } else {
                    $o_ligne = new AchatLigne();
                }
                if ($ligne['i_reduction_mode'] === 'fixe') {
                    $reduction = $ligne['i_reduction'];
                } else if ($ligne['i_reduction_mode'] === 'pourcentage') {
                    $reduction = $ligne['i_prix_ht'] * (($ligne['i_reduction'] ?? 0) / 100);
                }
                $o_ligne->article_id = $ligne['i_article_id'];
                $o_ligne->unite_id = $ligne['i_unite'];
                $o_ligne->achat_id = $o_ligne->achat_id != null ? $o_ligne->achat_id : $o_achat->id;
                $o_ligne->mode_reduction = $ligne['i_reduction_mode'];
                $o_ligne->nom_article = $ligne['i_article'];
                $o_ligne->description = $ligne['i_description'];
                $o_ligne->ht = $ligne['i_prix_ht'];
                $o_ligne->quantite = $ligne['i_quantite'];
                $o_ligne->taxe = $ligne['i_taxe'];
                $o_ligne->reduction = $ligne['i_reduction'] ?? 0;
                $o_ligne->total_ttc = $this->calculate_ttc($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0);
                $o_ligne->position = $key;
                $o_ligne->magasin_id = $ligne['i_magasin_id'] ?? $magasin_id;
                $o_ligne->save();
                $exist_lignes[] = $o_ligne->id;

                $achat_ht += ($o_ligne->ht) * $o_ligne->quantite;
                $achat_reduction += $reduction * $o_ligne->quantite;
                $achat_tva += $this->calculate_tva_amount($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0);
                $achat_ttc += $o_ligne->total_ttc;
            }


            $o_achat->update([
                'total_ht' => $achat_ht,
                'total_tva' => $achat_tva,
                'total_reduction' => $achat_reduction,
                'total_ttc' => $achat_ttc,
                'debit' => $achat_ttc - $o_achat->credit,
                'statut_paiement' => PaiementService::get_payable_statut($achat_ttc, $o_achat->credit, $o_achat->debit),
            ]);
            AchatLigne::whereNotIn('id', $exist_lignes)->where('achat_id', $o_achat->id)->delete();
            $o_achat->tags()->sync($request->get('balises', []));
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Modification')
                ->withProperties([
                    'subject_type' => Achat::class,
                    'subject_id' => $o_achat->id,
                    'subject_reference' => $o_achat->reference_interne,
                ])
                ->log('Modification de ' . __('achats.' . $type) . ' ' . ($o_achat->reference_interne ?? '-'));
            session()->flash('success', __('achats.' . $type) . " modifié avec succès");
            return redirect()->route('achats.afficher', ['id' => $o_achat->id, 'type' => $type]);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', __('achats.' . $type) . " non modifié");
            return redirect()->route('achats.liste', ['type' => $type]);
        }
    }


    public function history_modal(string $type, int $id)
    {
        $this->guard_custom(['achat.historique']);
        $o_achat = Achat::find($id);
        if (!$o_achat) {
            return response(__('achats.' . $type) . " n'exist pas !", 404);
        }
        $activities = Activity::whereJsonContains('properties->subject_id', $id)
            ->where('properties->subject_type', Achat::class)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('achats.partials.historique_modal', compact('activities'));
    }

    /**
     * @param $type
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response|void
     */
    public function supprimer($type, $id)
    {
        $this->guard_custom(['achat.supprimer']);
        if (\request()->ajax()) {
            $o_achat = Achat::find($id);
            if ($o_achat) {
                $o_achat->paiements()->delete();
                $o_achat->delete();
                activity()
                    ->causedBy(Auth::user())
                    ->event('Suppression')
                    ->withProperties([
                        'subject_type' => Achat::class,
                        'subject_id' => $o_achat->id,
                        'subject_reference' => $o_achat->reference_interne,
                    ])
                    ->log('Suppression de ' . __('achats.' . $type) . ' ' . $o_achat->reference_interne ?? '-');
                return response('Document supprimé  avec succès', 200);
            } else {
                return response('Erreur', 404);
            }
        }
    }

    /**
     * @param $type
     * @param $id
     * @return Response
     */
    public function telecharger($type, $id)
    {
        $this->guard_custom(['achat.telecharger']);
        if ($type !== 'bca') {
            abort(404);
        }
        $o_achat = Achat::find($id);
        $o_template = $o_achat->template ?? DocumentsParametre::get()->first()->template;

        $template = 'documents.achats.' . $o_template->blade;
        $images = [
            'image_en_tete' => $o_template->image_en_tete ? $this->base64_img($o_template->image_en_tete) : null,
            'image_en_bas' => $o_template->image_en_bas ? $this->base64_img($o_template->image_en_bas) : null,
            'image_arriere_plan' => $o_template->image_arriere_plan ? $this->base64_img($o_template->image_arriere_plan) : null,
            'cachet' => $o_template->cachet ? $this->base64_img($o_template->cachet) : null,
            'logo' => $o_template->logo ? $this->base64_img($o_template->logo) : null

        ];
//        $o_document_parametres = DocumentsParametre::get()->first();
//        $o_template = $o_document_parametres->template;


        $pdf = Pdf::loadView($template, compact('type', 'o_achat', 'o_template', 'images'))->setOptions(['defaultFont' => 'Rubik'])->set_option("isPhpEnabled", true);
        return $pdf->stream($o_achat->fournisseur->nom . ' ' . $o_achat->date_emission . ' ' . $o_achat->reference . '.pdf');


    }

    /**
     * @param $type
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Factory|View|Application|JsonResponse|Response|\Illuminate\View\View
     */
    public function validation_modal($type, $id)
    {
        $this->guard_custom(['achat.valider']);
        $o_achat = Achat::find($id);
        if (!$o_achat) {
            return response()->json(__('achats.' . $type) . " n'existe pas", 404);
        }
        if ($o_achat->statut !== 'brouillon') {
            return response(__('achats.' . $type) . '  est déjà validé', 403);
        }
        if (!$o_achat->reference_interne) {
            $reference = ReferenceService::generateReference($type, Carbon::createFromFormat('d/m/Y', $o_achat->date_emission));
        } else {
            $reference = $o_achat->reference_interne;
        }
        return view('achats.partials.validation_modal', compact('o_achat', 'type', 'reference'));
    }

    public function devalidation_modal($type, $id)
    {
        $this->guard_custom(['achat.devalider']);
        $o_achat = Achat::find($id);
        if (!$o_achat) {
            return response()->json(__('achats.' . $type) . " n'existe pas", 404);
        }
        if ($o_achat->statut !== 'validé') {
            return response(__('achats.' . $type) . '  doit être validé', 403);
        }
        return view('achats.partials.devalidation_modal', compact('o_achat', 'type'));
    }

    /**
     * @param $type
     * @param $id
     * @return RedirectResponse
     */
    public function valider($type, $id)
    {
        $this->guard_custom(['achat.valider']);

        $o_achat = Achat::find($id);
        if (!$o_achat) {
            session()->flash('error', __('achats.' . $type) . " n'existe pas");
            return redirect()->route('achats.afficher', [$type, $o_achat->id]);
        }
        if ($o_achat->statut !== 'brouillon') {
            session()->flash('warning', __('achats.' . $type) . '  est déjà validé !');
            return redirect()->route('achats.afficher', [$type, $o_achat->id]);
        }
        DB::beginTransaction();
        try {
            if (!$o_achat->reference_interne) {
                $reference = ReferenceService::generateReference($type, Carbon::make($o_achat->date_document));
                ReferenceService::incrementCompteur($type);
                $o_achat->update([
                    'reference_interne' => $reference,
                ]);
            }
            $o_achat->update([
                'statut' => 'validé'
            ]);
            DB::commit();
            if (\request()->user()->can(['stock.*'])) {
                $this->stock($id);
            }
            activity()
                ->causedBy(Auth::user())
                ->event('Validation')
                ->withProperties([
                    'subject_type' => Achat::class,
                    'subject_id' => $o_achat->id,
                    'subject_reference' => $o_achat->reference_interne,
                ])
                ->log('Validation de ' . __('achats.' . $type) . ' ' . $o_achat->reference_interne ?? '-');
            session()->flash('success', __('achats.' . $type) . '  validé !');
            return redirect()->route('achats.afficher', [$type, $o_achat->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', 'Erreur !');
            return redirect()->route('achats.afficher', [$type, $o_achat->id]);
        }
    }


    public function devalider(string $type, int $id): RedirectResponse
    {
        $this->guard_custom(['achat.devalider']);

        $o_achat = Achat::find($id);
        if (!$o_achat) {
            session()->flash('error', __('achats.' . $type) . " n'existe pas");
            return redirect()->route('achats.afficher', [$type, $o_achat->id]);
        }
        if ($o_achat->statut === 'brouillon') {
            session()->flash('warning', __('achats.' . $type) . "  n'est pas validé !");
            return redirect()->route('achats.afficher', [$type, $o_achat->id]);
        }
        DB::beginTransaction();
        //reverse stock
        if ($o_achat->statut === 'validé' && \request()->user()->can(['stock.*'])) {
            $this->stock_reverse($o_achat->id);
        }
        try {
            $o_achat->update([
                'statut' => 'brouillon'
            ]);
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Dévalidation')
                ->withProperties([
                    'subject_type' => Achat::class,
                    'subject_id' => $o_achat->id,
                    'subject_reference' => $o_achat->reference_interne,
                ])
                ->log('Dévalidation de ' . __('achats.' . $type) . ' ' . $o_achat->reference_interne ?? '-');
            session()->flash('success', __('achats.' . $type) . '  dévalidé !');
            return redirect()->route('achats.afficher', [$type, $o_achat->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::emergency($exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
            session()->flash('error', 'Erreur !');
            return redirect()->route('achats.afficher', [$type, $o_achat->id]);
        }
    }

    /**
     * @param $type
     * @param $id
     * @return StreamedResponse
     */
    public function piece_jointe($type, $id)
    {
        $o_achat = Achat::find($id);
        if (!$o_achat) {
            abort(404);
        }
        return Storage::download(FileService::getStoragePath($o_achat->fichier_document));
    }

    /**
     * @param $type
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Factory|View|Application|Response|\Illuminate\View\View
     */
    public function paiement_modal($type, $id)
    {
        $this->guard_custom(['paiement.achat']);
        $payable_modules = ModuleService::getPayabaleTypes();
        if (!in_array($type, $payable_modules)) {
            return response(__('achats.' . $type . " est pas payable !"), 404);
        }
        $o_achat = Achat::find($id);
        if (!$o_achat) {
            return response(__('achats.' . $type) . " n'existe pas !", 404);
        }
        if ($o_achat->debit == 0) {
            return response(__('achats.' . $type) . " est déja payé !", 403);
        }
        $comptes = Compte::ofUser()->get();
        $methodes = MethodesPaiement::where('actif', '=', '1')->get();
        $o_magasins = \request()->user()->magasins()->where('active','=','1')->get(['magasin_id as id','nom as text']);
        $magasins_count = Magasin::where('active', '=', '1')->count();
        return view('achats.partials.paiement_modal', compact('o_achat', 'type', 'comptes', 'methodes', 'o_magasins', 'magasins_count'));
    }

    /**
     * @param Request $request
     * @param $type
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|RedirectResponse|Response
     */
    public function payer(Request $request, $type, $id)
    {
        $this->guard_custom(['paiement.achat']);
        $payable_modules = ModuleService::getPayabaleTypes();
        if (!in_array($type, $payable_modules)) {
            return response(__('achats.' . $type . " est pas payable !"), 404);
        }
        $o_achat = Achat::find($id);
        if (!$o_achat) {
            return response(__('achats.' . $type . " n'existe pas !"), 404);
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
            'i_reference' => 'référence de chéque',
            'i_note' => 'note',
            'i_comptable' => 'comptable',
            'i_date_paiement' => 'date de paiement',
            'magasin_id' => 'magasin'
        ];
        $validation = Validator::make($request->all(), [
            'i_compte_id' => 'required|exists:comptes,id',
            'i_montant' => 'required|min:1|numeric|max:' . $o_achat->debit,
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
            PaiementService::add_paiement(Achat::class, $o_achat->id, $request->all(), $request->get('magasin_id'));
            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Paiement')
                ->withProperties([
                    'subject_type' => Achat::class,
                    'subject_id' => $o_achat->id,
                    'subject_reference' => $o_achat->reference_interne,
                ])
                ->log('Paiement du montant ' . $request->get('i_montant') . ' DH');
            return response('Paiement réussi', 200);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', 'Erreur !');
            return redirect()->route('achats.afficher', [$type, $o_achat->id]);
        }
    }


    public function clone_modal(string $type, int $id): Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['achat.cloner']);
        $o_achat = Achat::find($id);
        if (!$o_achat) {
            return response(__('achats.' . $type) . " n'exist pas !", 404);
        }
        return view('achats.partials.clone_modal', compact('o_achat'));
    }

    public function cloner(string $type, int $id, Request $request)
    {
        $this->guard_custom(['achat.cloner']);
        $date_permission = !$request->user()->can('achat.date');
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
        $o_achat = Achat::findOrFail($id);
        DB::beginTransaction();
        try {
            $cloned = Achat::create([
                'statut' => 'brouillon',
                'objet' => $o_achat->objet,
                'date_emission' => $date_permission ? Carbon::today()->toDateString() : Carbon::createFromFormat('d/m/Y', $request->input("date_emission"))->toDateString(),
                'date_expiration' => $date_permission? Carbon::today()->addDays(15)->toDateString() :  Carbon::createFromFormat('d/m/Y', $request->input("date_emission"))->addDays(15)->toDateString(),
                'fournisseur_id' => $o_achat->fournisseur_id,
                'total_ht' => $o_achat->total_ht,
                'total_tva' => $o_achat->total_tva,
                'total_reduction' => $o_achat->total_reduction,
                'total_ttc' => $o_achat->total_ttc,
                'type_document' => $o_achat->type_document,
                'fichier_document' => null,
                'debit' => $o_achat->total_ttc,
                'credit' => 0,
                "created_by" => auth()->id(),
                'note' => $o_achat->note,
                'statut_paiement' => 'non_paye',
                'piece_jointe' => null,
                'reference_interne' => null,
                'magasin_id' => $o_achat->magasin_id
            ]);

            foreach ($o_achat->lignes as $ligne) {
                AchatLigne::create([
                    'achat_id' => $cloned->id,
                    'article_id' => $ligne->article_id,
                    'unite_id' => $ligne->unite_id,
                    'nom_article' => $ligne->nom_article,
                    'description' => $ligne->description,
                    'ht' => $ligne->ht,
                    'quantite' => $ligne->quantite,
                    'taxe' => $ligne->taxe,
                    'reduction' => $ligne->reduction,
                    'total_ttc' => $ligne->total_ttc,
                    'mode_reduction' => $ligne->mode_reduction,
                    'position' => $ligne->position,
                    'magasin_id' => $ligne->magasin_id ?? $cloned->magasin_id,
                ]);
            }
            activity()
                ->causedBy(Auth::user())
                ->event('Clonage')
                ->withProperties([
                    'subject_type' => Achat::class,
                    'subject_id' => $o_achat->id,
                    'subject_reference' => $o_achat->reference,
                ])
                ->log('Clonage de ' . __('achats.' . $type) . ' ' . $o_achat->reference ?? '-');
            DB::commit();
            session()->flash('success', 'Document cloné !');
            return redirect()->route('achats.afficher', [$type, $cloned->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            session()->flash('error', $exception->getMessage());
            LogService::logException($exception);
        }
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
        $ht = round($ht - $reduction, 3);
        $tva = (1 + $tva / 100);
        $ttc = round($ht * $tva, 3) * $quantite;
        return round($ttc, 3);
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
        return +number_format(round(($ht - $reduction) * ($tva / 100), 10) * $quantite, 3, '.', '');
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
     * @param int $achat
     * @return void
     */
    function stock(int $achat)
    {
        $modules = ModuleService::getModules();
        $o_achat = Achat::find($achat);
        if (count($o_achat->lignes) > 0 && $o_achat->statut === 'validé') {
            if (in_array($o_achat->type_document, $modules->where('action_stock', 'entrer')->pluck('type')->toArray())) {
                foreach ($o_achat->lignes as $ligne) {
                    if ($ligne->article_id) {
                        StockService::stock_entre($ligne['article_id'], $ligne->quantite, Carbon::createFromFormat('d/m/Y', $o_achat->date_emission)->format('Y-m-d'), Achat::class, $o_achat->id, $ligne->magasin_id);
                    }
                }
            } elseif (in_array($o_achat->type_document, $modules->where('action_stock', 'sortir')->pluck('type')->toArray())) {
                foreach ($o_achat->lignes as $ligne) {
                    if ($ligne->article_id) {
                        StockService::stock_sortir($ligne['article_id'], $ligne->quantite, Carbon::createFromFormat('d/m/Y', $o_achat->date_emission)->format('Y-m-d'), Achat::class, $o_achat->id, $ligne->magasin_id);
                    }
                }
            }
        }
    }

    /**
     * @param int $achat
     * @return void
     */
    function stock_reverse(int $achat)
    {
        StockService::stock_revert(Achat::class, $achat);
    }

    /**
     * Marque une achat comme contrôlée.
     *
     * @param int $id L'identifiant de la achat à contrôler.
     * @return \Illuminate\Http\JsonResponse Retourne une réponse JSON indiquant le succès ou l'échec de l'opération.
     */
    public function controle($type, $id)
    {
        $this->guard_custom(['achat.controler']);
        try {
            $achat = Achat::findOrFail($id);
            $achat->update([
                'is_controled' => true,
                'controled_at' => now(),
                'controled_by' => auth()->id()
            ]);
            activity()
                ->causedBy(Auth::user())
                ->event('Contrôle')
                ->withProperties([
                    'subject_type' => Achat::class,
                    'subject_id' => $achat->id,
                    'subject_reference' => $achat->reference,
                ])
                ->log('Contrôle effectué sur l\'achat ' . $achat->reference);

            return response()->json([
                'success' => true,
                'message' => 'Achat contrôlé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du contrôle de l\'achat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function attacher_piece_jointe(Request $request, $type, $id)
    {
        $this->guard_custom(['achat.modifier']);
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
            $o_achat = Achat::find($id);
            if (!$o_achat) {
                abort(404);
            }
            // ajouter une piece jointe
            $piece_jointe = new PieceJointe();
            $piece_jointe->document()->associate($o_achat);
            $piece_jointe->title = $request->get('title');
            $piece_jointe->url = $request->get('url');
            $piece_jointe->save();

            DB::commit();
            activity()
                ->causedBy(Auth::user())
                ->event('Piece jointe')
                ->withProperties([
                    'subject_type' => Achat::class,
                    'subject_id' => $o_achat->id,
                    'subject_reference' => $o_achat->reference ?? '',
                ])
                ->log('Pièce jointe ' . ' ' . $piece_jointe->title . ' attachée au document');
            session()->flash('success', "Pièce jointe attachée avec succès");
            return response('Pièce jointe attaché', 200);
        } catch (Exception $e) {
            LogService::logException($e);
            DB::rollBack();
            return response('Erreur lors de l\'attachement de la pièce jointe', 500);
        }
    }


    public function supprimer_piece_jointe($type, $id)
    {
        $o_piece_jointe = PieceJointe::findOrFail($id);
        if (!$o_piece_jointe) {
            abort(404);
        }
        $o_piece_jointe->delete();
        $o_achat = Achat::find($o_piece_jointe->document_id);
        activity()
            ->causedBy(Auth::user())
            ->event('Piece jointe')
            ->withProperties([
                'subject_type' => Achat::class,
                'subject_id' => $o_achat->id,
                'subject_reference' => $o_achat->reference,
            ])
            ->log('Pièce jointe ' . ' ' . $o_piece_jointe->title . ' supprimée du document');
        session()->flash('success', "Pièce jointe supprimée avec succès");
        return redirect()->route('achats.afficher', [$type, $o_piece_jointe->document_id]);
    }


    public function conversion_modal(string $type, int $id): Application|View|Factory|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $this->guard_custom(['achat.convertir']);
        $o_achat = Achat::find($id);
        if (!$o_achat) {
            return response(__('achats.' . $type) . " n'exist pas !", 404);
        }
        if ($o_achat->statut === 'brouillon') {
            return response(__('achats.' . $type) . " n'est pas validé!", 402);
        }
        $active_modules = ModuleService::getActiveModules();
        $types = array_filter(Achat::TYPES, function ($type) use ($active_modules) {
            return in_array($type, $active_modules);
        });

        return view('achats.partials.conversion_modal', compact('types', 'o_achat'));
    }

    public function convertir(Request $request, string $type, int $id): RedirectResponse
    {
        $this->guard_custom(['achat.convertir']);
        $date_permission = !$request->user()->can('achat.date');

        $request->validate([
            'date_emission' => 'required|date_format:d/m/Y',
        ]);

        $o_achat = Achat::find($id);
        if (!$o_achat) {
            session()->flash('error', __('achats.' . $type) . " n' existe pas !");
            return redirect()->route('achats.liste', $type);
        }


//        $dateEmission = Carbon::createFromFormat('d/m/Y', $request->get('date_emission'))->toDateString();

        $types = array_diff(Achat::TYPES, [$type]);
        $active_modules = ModuleService::getActiveModules();

        if (!in_array($request->get('i_type'), $types) || !in_array($request->get('i_type'), $active_modules)) {
            session()->flash('error', "veuillez choisir un document validé vers lequel convertir");
            return redirect()->route('achats.afficher', [$type, $id]);
        }


        DB::beginTransaction();
        try {
            $data = [
                'created_by' => auth()->id(),
                'fournisseur_id' => $o_achat->fournisseur_id,
                'reference' => $o_achat->reference,

                "statut" => "brouillon",
                "objet" => $o_achat->objet,

                'date_emission' => $date_permission ? Carbon::today()->toDateString() : Carbon::createFromFormat('d/m/Y', $request->input("date_emission"))->toDateString(),
                'date_expiration' => $date_permission ? Carbon::today()->addDays(15)->toDateString() : Carbon::createFromFormat('d/m/Y', $request->input("date_emission"))->addDays(15)->toDateString(),
                'type_document' => $request->get('i_type'),
                'statut_paiement' => 'non_paye',
                'note' => $o_achat->note,
                'total_ht' => $o_achat->total_ht,
                'total_tva' => $o_achat->total_tva,
                'total_reduction' => $o_achat->total_reduction,
                'total_ttc' => $o_achat->total_ttc,
                'magasin_id' => $o_achat->magasin_id,
                'credit' => 0,
                'debit' => $o_achat->total_ttc
            ];

            $o_converted_achat = Achat::create($data);
            $o_achat->documents_en_relation()->attach([$o_converted_achat->id]);

            foreach ($o_achat->lignes as $ligne) {
                $o_ligne = new AchatLigne();
                $o_ligne->achat_id = $o_converted_achat->id;
                $o_ligne->article_id = $ligne->article_id;
                $o_ligne->unite_id = $ligne->unite_id;
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
                    'subject_type' => Achat::class,
                    'subject_id' => $o_achat->id,
                    'subject_reference' => $o_achat->reference,
                ])
                ->log('Conversion de ' . __('achats.' . $type) . ' ' . $o_achat->reference ?? '-' . ' à ' . __('achats.' . $request->get('i_type')));

            session()->flash('success', __('achats.' . $o_converted_achat->type_document) . ' ajouté avec succès');
            return redirect()->route('achats.afficher', [$o_converted_achat->type_document, $o_converted_achat->id]);
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            dd($exception);
            session()->flash('error', "Une erreur s'est produite dans le processus");
            return redirect()->route('achats.liste', $type);
        }
    }
}
