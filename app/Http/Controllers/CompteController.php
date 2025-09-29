<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Banque;
use App\Models\Compte;
use App\Models\Depense;
use App\Models\MethodesPaiement;
use App\Models\Operation;
use App\Models\Paiement;
use App\Models\Vente;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CompteController extends Controller
{
    public function liste2(Request $request)
    {
        $this->guard_custom(['compte.liste']);
        try {
            if ($request->ajax()) {
                $o_comptes = Compte::all();
                $table = DataTables::of($o_comptes);

                $table->addColumn('actions', '&nbsp;');

                $table->editColumn('statut', function ($row) {
                    return $row->statut ? 'professionnel' : 'personnel';
                });
                $table->editColumn('banque', function ($row) {
                    return $row->banque ? $row->banque->nom : '-';
                });
                $table->editColumn('rib', function ($row) {
                    return $row->rib ?? '-';
                });
                $table->editColumn('adresse', function ($row) {
                    return $row->adresse ?? '-';
                });
                $table->editColumn('principal', function ($row) {
                    return $row->principal ? '<div class="badge bg-soft-success" >Oui</div>' : '<div class="badge bg-soft-danger" >Non</div>';
                });
                $table->editColumn('actions', function ($row) {
                    $crudRoutePart = 'comptes';
                    $show = 'afficher';
                    $edit = 'modifier';
                    $delete = 'supprimer';
                    $id = $row->id;

                    return view('partials.__datatable-action', compact(

                        'edit',
                        'delete',
                        'crudRoutePart',
                        'id',
                        'show'
                    ));
                });
                $table->addColumn(
                    'selectable_td',
                    function ($compte) {
                        $id = $compte->id;
                        return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                    }
                );
                $table->rawColumns(['actions', 'selectable_td','principal']);
                return $table->make();
            }
            return view("comptes.liste");
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.']);
        }
    }

    public function liste(Request $request)
    {
        $this->guard_custom(['compte.liste']);
        $caisses = Compte::ofUser()->where('type','caisse')->get();
        $banques = Compte::ofUser()->where('type','banque')->get();
        return view('comptes.liste',compact('caisses','banques'));
    }

    public function ajouter()
    {
        $this->guard_custom(['compte.sauvegarder']);
        $banques = Banque::all();
        return view('comptes.ajouter',compact('banques'));
    }

    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['compte.sauvegarder']);
        $validationRules = [
            'nom' => 'required|min:3',
            'type' => 'required',
            'banque' => 'nullable|exists:banques,id',
            'rib' =>  'nullable|min:24|max:27' ,
            'adresse' =>'nullable|min:3',
            'principal' => 'boolean'
        ];
        $validated = $request->validate($validationRules);
        if (!isset($validated['principal']) || empty($validated['principal'])) {
            $validated['principal'] = 0;
        }else if ($validated['principal'] && Compte::where('principal', 1)->exists()) {
            Compte::where('principal',1)->first()->update(['principal' => 0]);
        }

        try {
            Compte::create([
                'nom' => $request->input('nom'),
                'type' => $request->input('type'),
                'banque_id' => $request->input('banque'),
                'rib' => $request->input('banque')? $request->input('rib') : null,
                'adresse' => $request->input('banque')? $request->input('adresse') : null,
                'principal' => $request->input('principal') ?? 0,
                'statut' => 0
            ]);
            return redirect()->route('comptes.liste')->with('success', 'Compte ajouté avec succès');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function modifier($id)
    {
        $this->guard_custom(['compte.mettre_a_jour']);

        $o_compte = Compte::find($id);
        $banques = Banque::all();

        if ($o_compte){
            return view('comptes.modifier',compact('o_compte','banques'));
        }
        abort(404);
    }

    public function mettre_a_jour(Request $request,$id)
    {
        $this->guard_custom(['compte.mettre_a_jour']);
        $validationRules = [
            'nom' => 'required|min:3',
            'type' => 'required',
            'banque' => 'nullable|exists:banques,id',
            'rib' =>  'nullable|min:24|max:27' ,
            'adresse' =>'nullable|min:3',
            'principal' => 'nullable|boolean'
        ];

        $validated = $request->validate($validationRules);
        if (!isset($validated['principal']) || empty($validated['principal'])) {
            $validated['principal'] = 0;
        }else if ($validated['principal'] && Compte::where('principal', 1)->exists()) {
            Compte::where('principal',1)->first()->update(['principal' => 0]);
        }

        $o_compte = Compte::findOrFail($id);

        $o_compte->update([
            'nom' => $request->input('nom'),
            'type' => $request->input('type'),
            'banque_id' => $request->input('banque'),
            'rib' => $request->input('banque')? $request->input('rib') : null,
            'adresse' => $request->input('banque')? $request->input('adresse') : null,
            'principal' => $request->input('principal') ?? 0,
            'statut' => 0
        ]);
        session()->flash('success','Compte mise à jour');
        return redirect()->route('comptes.liste');

        abort(404);
    }

    public function afficher(Request $request,$id){
        $this->guard_custom(['compte.afficher']);
        $exercice_date = session()->get('exercice');

        if (\request()->ajax()){

            $range = [Carbon::now()->setYear($exercice_date)->firstOfYear()->toDateString(), Carbon::now()->setYear($exercice_date)->lastOfYear()->toDateString()];
            if ($request->get('date')){
                $selectedDateRange = $request->get('date');
                $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->toDateString();
                $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->toDateString();
                $range = [$start_date, $end_date];
            }

            $query = Paiement::whereBetween('date_paiement',$range)->where('compte_id',$id)->with("payable");
            $table = DataTables::of($query);
            $table->addColumn(
                'selectable_td',
                function ($compte) {
                    $id = $compte->id;
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            )->editColumn('payable_id', function ($row) {
                $route = null;
                if ($row->payable_type === Achat::class) {
                    if(!$row->payable){
                        return "-----";
                    }
                    $route = route('achats.afficher', ['type' => $row->payable->type_document, 'id' => $row->payable->id]);
                } elseif ($row->payable_type === Depense::class) {
                    if(!$row->payable){
                        return "-----";
                    }
                    $route = route('depenses.afficher', $row->payable->id);
                } elseif ($row->payable_type === Vente::class) {
                    if(!$row->payable){
                        return "-----";
                    }
                    $route = route('ventes.afficher', ['type' => $row->payable->type_document, 'id' => $row->payable->id]);
                }
                if (!$route) {
                    return $row->payable->reference;
                }
                return '<a class="text-info text-decoration-underline" target="_blank" href="' . $route . '" >' . $row->payable->reference . '</a>';
            })->addColumn('objet', function ($row){
                $objet =null ;
                if ($row->payable_type === Achat::class) {
                    $objet = $row->payable->objet;
                }elseif ($row->payable_type === Depense::class) {
                    $objet = $row->payable->nom_depense;
                }elseif ($row->payable_type === Vente::class) {
                    $objet = $row->payable->objet;
                }
                return $objet;
            })->editColumn('methode_paiement_key',function (Paiement $row){
                return $row->methodePaiement->nom;
            })->rawColumns(['selectable_td','payable_id']);
            $exercie_total = Paiement::where('compte_id',$id)->whereBetween('date_paiement',$range)->selectRaw('SUM(encaisser) as encaisser,SUM(decaisser) as decaisser')->first();
            $total_encaisser = $exercie_total->encaisser;
            $total_decaisser= $exercie_total->decaisser;
            $differance = $total_encaisser - $total_decaisser;
            $total_ouverture = Paiement::where('compte_id',$id)->whereRaw('date_paiement < "'.$range[0].'"')->selectRaw('SUM(encaisser) - SUM(decaisser) as total')->first()->total ?? 0;
            $total_actuel = $total_ouverture + $differance;
            return [
                ...(array)$table->make()->getData(),
                'numbers' => view('comptes.partials.numbers',compact('total_encaisser','total_decaisser','differance','total_actuel','total_ouverture'))->render()
            ];

        }

        $o_compte = Compte::findOrFail($id);

        // ---------------------------### calcule des soldes ###---------------------------
        $exercie_total = Paiement::where('compte_id',$id)->whereRaw('YEAR(date_paiement) = '.session()->get('exercice'))->selectRaw('SUM(encaisser) as encaisser,SUM(decaisser) as decaisser')->first();
        $total_encaisser = $exercie_total->encaisser;
        $total_decaisser= $exercie_total->decaisser;
        $differance = $total_encaisser - $total_decaisser;
        $total_ouverture = Paiement::where('compte_id',$id)->whereRaw('Date(date_paiement) < '.session()->get('exercice'))->selectRaw('SUM(encaisser) - SUM(decaisser) as total')->first()->total ?? 0;
        $total_actuel = $total_ouverture + $differance;
        $operations = Operation::get(['nom','id']);
        $methodes = MethodesPaiement::where('actif','1')->get(['key','nom']);

        return view('comptes.afficher',compact('o_compte','operations','methodes'));
    }

    public function supprimer($id)
    {
        $this->guard_custom(['compte.supprimer']);

        if (\request()->ajax()){
            $o_compte = Compte::find($id);
            if ($o_compte){
                if ($o_compte->has('paiements')){
                    return response('vous ne pouvez pas supprimer ce compte car des paiements y sont déjà liés',402);
                }
                $o_compte->delete();
                return  response('Compte supprimé',200);
            }else {
                return response('Erreur',404);
            }
        }
    }
}
