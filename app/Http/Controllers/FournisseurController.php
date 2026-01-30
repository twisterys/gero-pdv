<?php

namespace App\Http\Controllers;

use App\Http\Requests\FournisseurStoreRequest;
use App\Http\Requests\FournisseurUpdateRequest;
use App\Models\Achat;
use App\Models\Contact;
use App\Models\FormeJuridique;
use App\Models\Fournisseur;
use App\Models\Paiement;
use App\Models\Vente;
use App\Services\GlobalService;
use App\Services\LogService;
use App\Services\ModuleService;
use App\Services\ReferenceService;
use DB;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Yajra\DataTables\DataTables;

class FournisseurController extends Controller
{
    public function liste(Request $request)
    {
        $this->guard_custom(['fournisseur.liste']);
        if ($request->ajax()) {
            $o_fournisseurs = Fournisseur::with('forme_juridique');
            if ($request->get('nom')) {
                $search = '%' . $request->get('nom') . '%';
                $o_fournisseurs->where('nom', 'LIKE', $search);
            }
            if ($request->get('reference')) {
                $o_fournisseurs->where('reference', $request->get('reference'));
            }
            if ($request->get('ice')) {
                $o_fournisseurs->where('ice', $request->get('ice'));
            }
            $o_fournisseurs = $o_fournisseurs->get();
            $table = DataTables::of($o_fournisseurs);
            $table->addColumn('actions', '&nbsp;');
//            $table->editColumn('forme_juridique', function ($fournisseur) {
//                $types = Fournisseur::getFormJuridiqueTypes();
//                return $types[$fournisseur->forme_juridique] ?? '';
//            });
            $table->editColumn('actions', function ($row) {
                $show = 'afficher';
                $edit = 'modifier';
                $delete = 'supprimer';
                $crudRoutePart = 'fournisseurs';
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
            $table->addColumn(
                'selectable_td',
                function ($O_fournisseur) {
                    $id = $O_fournisseur->id;
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            );
            $table->rawColumns(['actions', 'selectable_td']);
            return $table->make();
        }
        $form_juridique_types = FormeJuridique::where('active','1')->get();
        return view("fournisseurs.liste", compact('form_juridique_types'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function ajouter()
    {
        $this->guard_custom(['fournisseur.sauvegarder']);
        $fournisseur_reference = ReferenceService::generateReference('fr');
        $form_juridique_types = FormeJuridique::where('active','1')->get();
        $modifier_reference =  GlobalService::get_modifier_reference();
        if (\request()->ajax()) {
            return view('fournisseurs.partials.ajout-rapide-modal', compact('form_juridique_types', 'fournisseur_reference','modifier_reference'));
        }
        return view('fournisseurs.ajouter', compact('form_juridique_types', 'fournisseur_reference','modifier_reference'));
    }

    /**
     * Store a newly created resource in storage.
     * @param FournisseurStoreRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|RedirectResponse|Response
     */
    public function sauvegarder(FournisseurStoreRequest $request)
    {
        $this->guard_custom(['fournisseur.sauvegarder']);
        DB::beginTransaction();
        try {
            $data = [
                'reference'=>$request->get('reference'),
                'forme_juridique_id'=>$request->get('forme_juridique'),
                'ice'=>$request->get('ice')??null,
                'nom'=>$request->get('nom'),
                'email'=>$request->get('email')??null,
                'telephone'=>$request->get('telephone')??null,
                'note'=>$request->get('note')??null,
                'limite_de_credit'=>$request->get('limite_de_credit')??null,
                'adresse'=>$request->get('adresse')??null,
                'rib'=>$request->get('rib')??null
            ];
            $O_fournisseur = Fournisseur::create($data);

            ReferenceService::incrementCompteur('fr');
            DB::commit();
            if ($request->ajax()) {
                return response(['id' => $O_fournisseur->id, 'text' => $O_fournisseur->nom], 200);
            }
            return redirect()->route('fournisseurs.liste')->with('success', "Fournisseur ajouté avec succès");
        } catch (Exception $e) {
            DB::rollBack();
            LogService::logException($e);
            if ($request->ajax()) {
                return response("Une erreur s'est produite lors de l'ajout fournisseur", 500);
            }
            return redirect()->route('fournisseurs.liste')->with('error', "Une erreur s'est produite lors de l'ajout fournisseur");
        }
    }

    public function afficher(Request $request, $id)
    {
        $this->guard_custom(['fournisseur.afficher']);
        $o_fournisseur = Fournisseur::find($id);
        if (!$o_fournisseur) {
            abort(404);
        }
        $exercice = session()->get('exercice');
        $payable_types = ModuleService::getPayabaleTypes();
        $commandes = Achat::where('type_document', 'bca')->where('statut', 'validé')->whereRaw('Year(date_emission) = '.$exercice)->where('fournisseur_id', $id)->sum('total_ttc');
        $ca = Achat::whereIn('type_document', $payable_types)
            ->where('statut', 'validé')
            ->whereRaw('Year(date_emission) = '.$exercice)
            ->where('fournisseur_id', $id)
            ->sum('total_ttc');
        $decaissement = Paiement::where('fournisseur_id', $id)->whereRaw('Year(date_paiement) = '.$exercice)->sum('decaisser');
        $credit = Achat::whereIn('type_document', $payable_types)->whereRaw('Year(date_emission) = '.$exercice)->where('statut', 'validé')->where('fournisseur_id', $id)->sum('debit');
        $types = array_diff(ModuleService::getActiveModules(),Vente::TYPES);
        $payables = ModuleService::getPayabaleTypes();
        return view('fournisseurs.afficher', compact('o_fournisseur', 'commandes', 'ca', 'decaissement', 'credit','types','payables'));


    }

    public function afficher_ajax(Request $request, $id)
    {
        $o_fournisseur = Fournisseur::find($id);
        if ($request->ajax()) {
            if (!$o_fournisseur) {
                return response()->json('', 404);
            }
            return response()->json($o_fournisseur, 200);
        }
        if (!$o_fournisseur) {
            return redirect()->back()->with('error', "Fournisseur n'existe pas");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifier($id)
    {
        $this->guard_custom(['fournisseur.mettre_a_jour']);
        $O_fournisseur = Fournisseur::find($id);
        if (!$O_fournisseur) {
            return redirect()->back()->with('error', "Fournisseur n'existe pas");
        }
        $modifier_reference =  GlobalService::get_modifier_reference();
        $form_juridique_types = FormeJuridique::all();
        return view('fournisseurs.modifier', compact('O_fournisseur', 'form_juridique_types','modifier_reference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function mettre_a_jour(FournisseurUpdateRequest $request, $id)
    {
        $this->guard_custom(['fournisseur.mettre_a_jour']);
        $O_fournisseur = Fournisseur::find($id);
        if (!$O_fournisseur) {
            return redirect()->route('fournisseurs.liste')->with('error', "Fournisseur n'existe pas");
        }
        $modifier_reference =  GlobalService::get_modifier_reference();
        DB::beginTransaction();
        try {
            if (count($request->get('contacts_nom',[]))) {
                $contacts = [];
                foreach ($request->get('contacts_nom',[]) as $key=>$item){
                    if (isset($request->get('contacts_id',[])[$key])){
                        $o_contact = Contact::find($request->get('contacts_id',[])[$key]);
                    }else {
                        $o_contact = new Contact();
                    }
                    $o_contact->nom = $request->get('contacts_nom',[])[$key];
                    $o_contact->prenom = $request->get('contacts_prenom',[])[$key];
                    $o_contact->email = $request->get('contacts_email',[])[$key];
                    $o_contact->telephone = $request->get('contacts_telephone',[])[$key];
                    $o_contact->is_principal = $request->get('contacts_principal',[])[$key] ? '1' : '0';
                    $o_contact->save();
                    $contacts[]= $o_contact->id;
                }
                $O_fournisseur->contacts()->sync($contacts);
            }
            $data = [
                'reference'=> $modifier_reference ? $request->get('reference') : $O_fournisseur->reference,
                'forme_juridique_id'=>$request->get('forme_juridique'),
                'nom'=>$request->get('nom'),
                'ice'=>$request->get('ice'),
                'email'=>$request->get('email'),
                'telephone'=>$request->get('telephone'),
                'note'=>$request->get('note'),
                'limite_de_credit'=>$request->get('limite_de_credit'),
                'adresse'=>$request->get('adresse'),
                'rib'=>$request->get('rib')??null
            ];
            $O_fournisseur->update($data);
            DB::commit();
            return redirect()->route('fournisseurs.liste')->with('success', 'Fournisseur modifé avec succès');
        }catch (Exception $exception){
            DB::rollBack();
            LogService::logException($exception);
            return redirect()->route('fournisseurs.liste')->with('error', "Une erreur s'est produite lors de l'ajout fournisseur");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function supprimer($id)
    {
        $this->guard_custom(['fournisseur.supprimer']);
        if (\request()->ajax()) {
            $O_fournisseur = Fournisseur::find($id);
            if ($O_fournisseur) {
                $O_fournisseur->delete();
                return response('Fournisseur supprimé avec succès', 200);
            } else {
                return response('Erreur', 404);
            }
        }
    }

    public function fournisseur_select(Request $request)
    {
        if ($request->ajax()) {
            $search = '%' . $request->get('term') . '%';
            $data = Fournisseur::where('nom', 'LIKE', $search)->get(['id', 'nom as text']);
            return response()->json($data, 200);
        }
        abort(404);
    }
}
