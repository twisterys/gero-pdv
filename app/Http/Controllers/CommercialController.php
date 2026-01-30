<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommercialRequest;
use App\Models\Client;
use App\Models\Commercial;
use App\Models\Paiement;
use App\Models\Vente;
use App\Services\GlobalService;
use App\Services\LimiteService;
use App\Services\LogService;
use App\Services\ModuleService;
use App\Services\ReferenceService;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Storage;
use Yajra\DataTables\DataTables;

class CommercialController extends Controller
{
    public function liste(Request $request)
    {
        $this->guard_custom(['commercial.liste']);
        if (!LimiteService::is_enabled('commerciaux')){
            abort(404);
        }
        try {

            if ($request->ajax()) {

                $o_commercial = Commercial::query();

                if ($request->get('nom')) {
                    $search = '%' . $request->get('nom') . '%';
                    $o_commercial->where('nom', 'LIKE', $search);
                }
                if ($request->get('reference')) {
                    $o_commercial->where('reference', $request->get('reference'));
                }
                if ($request->get('ice')) {
                    $o_commercial->where('ice', $request->get('ice'));
                }
                $o_commercial = $o_commercial->get();
                $table = DataTables::of($o_commercial);


                $table->addColumn('actions', '&nbsp;');


                $table->editColumn('actions', function ($row) {
                    $crudRoutePart = 'commercials';

                    $show = 'afficher';
                    $edit = 'modifier';
                    $delete = 'supprimer';
                    $id = $row?->id;
                    return view(
                        'partials.__datatable-action',
                        compact(
                            'show',
                            'edit',
                            'delete',
                            'crudRoutePart',
                            'id'
                        )
                    );
                });
                $table->editColumn('type_commercial', function ($fournisseur) {
                    return Commercial::type_de_commercial()[$fournisseur->type_commercial] ?? "";
                });
                $table->addColumn(
                    'selectable_td',
                    function ($commercial) {
                        $id = $commercial->id;
                        return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                    }
                );
                $table->rawColumns(['actions', 'selectable_td']);
                return $table->make();
            }
            $form_juridique_types = Client::getFormJuridiqueTypes();
            return view('commercials.liste', compact('form_juridique_types'));
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function ajouter()
    {
        $this->guard_custom(['commercial.sauvegarder']);
        if (!LimiteService::is_enabled('commerciaux')){
            abort(404);
        }
        $commercial_reference = ReferenceService::generateReference('cms');
        $type_de_commercial = Commercial::type_de_commercial();
        $modifier_reference =  GlobalService::get_modifier_reference();
        return view('commercials.ajouter', compact('type_de_commercial', 'commercial_reference', 'modifier_reference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function sauvegarder(CommercialRequest $request)
    {
        $this->guard_custom(['commercial.sauvegarder']);
        // Validation and saving logic
        if (!LimiteService::is_enabled('commerciaux')){
            abort(404);
        }
        try {

            if ($request->file('i_image')) {
                $file = $request->file('i_image');
                $fileName = $this->store_image($file);
                $commercial_image = $fileName;
            }
            Commercial::create([
                'nom' => $request->input('nom'),
                'email' => $request->input('email'),
                'telephone' => $request->input('telephone'),
                'note' => $request->input('note'),
                'reference' => $request->input('reference'),
                'commission_par_defaut' => $request->input('commission_par_defaut'),
                'objectif' => $request->input('objectif'),
                'secteur' => $request->input('secteur'),
                'type_commercial' => $request->input('type_commercial'),
                'image' => $commercial_image ?? null,
            ]);
            ReferenceService::incrementCompteur('cms');
            return redirect()->route('commercials.liste')->with('success', "Commercial ajouté avec succès");
        } catch (\Exception $e) {
            LogService::logException($e);
            return redirect()->route('commercials.liste')->with('error', "Une erreur s'est produite lors de l'ajout du Commercial");
        }
    }

    /**
     * Display the specified resource.
     */

    public function afficher(Request $request, $id)
    {
        $this->guard_custom(['commercial.afficher']);
        if (!LimiteService::is_enabled('commerciaux')){
            abort(404);
        }
        $o_commercial = Commercial::find($id);
        if (!$o_commercial) {
            abort(404);
        }

        $types_inclus = ['fa'];
        $exercice_date = session()->get('exercice');
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');
        if ($request->ajax()){
            if ($request->get('i_types')){
                $types_inclus = $request->get('i_types');
            }
            $query = Vente::where('commercial_id',$id)->whereIn('type_document',$types_inclus)->where('statut','validé')->select(['id','type_document','reference','date_emission','total_ttc','commission_par_defaut']);
            if ($request->has('i_date')) {
                $selectedDateRange = $request->get('i_date');
                $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->toDateString();
                $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->toDateString();
                $range = [$start_date, $end_date];
                $query->whereBetween('ventes.date_emission', $range);
            }
            $table = DataTables::of($query);
            $table->addColumn(
                'selectable_td',
                function ($commercial) {
                    $id = $commercial->id;
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            );
            $table->editColumn('reference',function ($row){
               return '<a class="text-decoration-underline text-info" href="'.route('ventes.afficher',['type'=>$row->type_document,'id'=>$row->id]).'" target="_blank" >'.$row->reference.'</a>';
            });
            $table->editColumn('commission_par_defaut',function ($row){
                return number_format($row->total_ttc*$row->commission_par_defaut/100,3,'.',' ').' MAD';
            });
            $table->editColumn('total_ttc',function ($row){
                return number_format($row->total_ttc ?? 0,3,'.',' ').' MAD';
            });

            $table->rawColumns(['selectable_td','reference']);
            return  $table->make();
        }
        $payable_types = ModuleService::getPayabaleTypes();
        $commandes = Vente::where('type_document', 'bc')->where('statut', 'validé')->where('commercial_id', $id)->sum(DB::raw('(total_ttc * commission_par_defaut)/100'));
        $ca = Vente::whereIn('type_document', $payable_types)->where('statut', 'validé')->where('commercial_id', $id)->sum(DB::raw('(total_ttc * commission_par_defaut)/100'));
        $encaissement = Vente::join('paiements', function (JoinClause $q) {
            $q->on('paiements.payable_id', 'ventes.id')->where('paiements.payable_type', Vente::class);
        })->whereIn('type_document', $payable_types)->where('statut', 'validé')->where('commercial_id', $id)->select('*')->groupBy('ventes.id')->sum(DB::raw('(paiements.encaisser * commission_par_defaut)/100'));
        $commissions = Vente::whereIn('type_document', $payable_types)->where('statut', 'validé')->where('commercial_id', $id)->sum(DB::raw('(total_ttc * commission_par_defaut)/100'));

        $types = Vente::TYPES;
        return view('commercials.afficher', compact('o_commercial','date_picker_end','date_picker_start', 'commandes', 'ca', 'encaissement', 'commissions','types_inclus' ,'types'));
    }
    public function afficher_ajax(Request $request, $id)
    {
        $o_commercial = Commercial::find($id);
        if ($request->ajax()) {
            if (!$o_commercial) {
                return response()->json('', 404);
            }
            return response()->json($o_commercial, 200);
        }
        if (!$o_commercial) {
            return redirect()->back()->with('error', "Commercial n'existe pas");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifier($id)
    {
        $this->guard_custom(['commercial.mettre_a_jour']);
        if (!LimiteService::is_enabled('commerciaux')){
            abort(404);
        }
        $o_commercial = Commercial::find($id);
        if (!$o_commercial) {
            return redirect()->back()->with('error', "Commercial n'existe pas");
        }
        $form_juridique_types = __("cruds.contact.form_juridique_type");
        $type_de_commercial = Commercial::type_de_commercial();
        $modifier_reference =  GlobalService::get_modifier_reference();


        return view('commercials.modifier', compact('o_commercial', 'type_de_commercial', 'modifier_reference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function mettre_a_jour(CommercialRequest $request, $id)
    {
        $this->guard_custom(['commercial.mettre_a_jour']);
        if (!LimiteService::is_enabled('commerciaux')){
            abort(404);
        }
        $modifier_reference =  GlobalService::get_modifier_reference();
        $o_commercial = Commercial::find($id);
        if (!$o_commercial) {
            return redirect()->route('commercials.liste')->with('error', "Commercial n'existe pas");
        }
        $image = $o_commercial->image;
        if ($request->file('i_image')) {
            $file = $request->file('i_image');
            $fileName = $this->store_image($file);
            $image = $fileName;
        } elseif ($request->get('i_supprimer_image') === '1') {
            $image = null;
        }
        $o_commercial->update([
            'nom' => $request->input('nom'),
            'email' => $request->input('email'),
            'telephone' => $request->input('telephone'),
            'note' => $request->input('note'),
            'reference' => $modifier_reference ? $request->get('reference') : $o_commercial->reference,
            'commission_par_defaut' => $request->input('commission_par_defaut'),
            'objectif' => $request->input('objectif'),
            'secteur' => $request->input('secteur'),
            'type_commercial' => $request->input('type_commercial'),
            'image' => $image,
        ]);
        return redirect()->route('commercials.liste')->with('success', 'Commercial modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function supprimer($id)
    {
        $this->guard_custom(['commercial.supprimer']);
        if (!LimiteService::is_enabled('commerciaux')){
            abort(404);
        }
        if (\request()->ajax()) {
            $o_commercial = Commercial::find($id);
            if ($o_commercial) {
                $o_commercial->delete();
                return response('Commercial supprimé  avec succès', 200);
            } else {
                return response('Erreur', 404);
            }
        }
    }

    /**
     * Obtient la liste des noms et des identifiants des commerciaux en fonction de la requête de recherche fournie par Ajax.
     * @param Request $request
     * @return JsonResponse
     */
    public function commercial_select(Request $request)
    {
        if ($request->ajax()) {
            $search = '%' . $request->get('term') . '%';
            $data = Commercial::where('nom', 'LIKE', $search)->get(['id', 'nom as text', 'commission_par_defaut']);
            return response()->json($data, 200);
        }
        abort(404);
    }
    public function load_article_image($file)
    {
        $path = 'public/commerciaux/' . $file;
        if (Storage::disk('external_storage')->exists($path)) {
            return response()->file(Storage::disk('external_storage')->path($path), ['Content-Type' => 'image/*']);
        }
        return response('', 404);
    }

    function store_image($file)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = 'public' . DIRECTORY_SEPARATOR . 'commerciaux' . DIRECTORY_SEPARATOR . $fileName;
        Storage::disk('external_storage')->put($path, file_get_contents($file));
        return $fileName;
    }
}
