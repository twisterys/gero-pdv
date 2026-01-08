<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Achat;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Event;
use App\Models\FormeJuridique;
use App\Models\Paiement;
use App\Models\Vente;
use App\Services\GlobalService;
use App\Services\LogService;
use App\Services\ModuleService;
use App\Services\ReferenceService;
use App\Traits\LogHelper;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class ClientController extends Controller
{
    public function liste(Request $request)
    {
        $this->guard_custom(['client.liste']);
        if ($request->ajax()) {
            $o_clients = Client::with('forme_juridique');
            if ($request->get('nom')) {
                $search = '%' . $request->get('nom') . '%';
                $o_clients->where('nom', 'LIKE', $search);
            }
            if ($request->get('reference')) {
                $o_clients->where('reference', $request->get('reference'));
            }
            if ($request->get('ice')) {
                $o_clients->where('ice', $request->get('ice'));
            }
            $o_clients = $o_clients->get();
            $table = DataTables::of($o_clients);
            $table->addColumn('actions', '&nbsp;');
//            $table->editColumn('forme_juridique', function ($fournisseur) {
//                $types = Client::getFormJuridiqueTypes();
//                return $types[$fournisseur->forme_juridique] ?? '';
//            });
            $table->editColumn('actions', function ($row) {
                $show = 'afficher';
                $edit = 'modifier';
                $delete = 'supprimer';
                $crudRoutePart = 'clients';
                $id = $row?->id;

                return view('partials.__datatable-action', compact('show', 'edit', 'delete', 'crudRoutePart', 'id'));
            });
            $table->addColumn('selectable_td', function ($contact) {
                $id = $contact->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->rawColumns(['actions', 'selectable_td']);
            return $table->make();
        }
        $form_juridique_types = FormeJuridique::where('active','1')->get();
        return view("clients.liste", compact('form_juridique_types'));
    }

    /**
     * Return views of client creationg modal and page
     *
     * @return Factory|\Illuminate\Contracts\View\View|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function ajouter(): Factory|\Illuminate\Contracts\View\View|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $this->guard_custom(['client.sauvegarder']);
        $client_reference = ReferenceService::generateReference('clt');
        $form_juridique_types = FormeJuridique::where('active','1')->get();
        $modifier_reference =  GlobalService::get_modifier_reference();
        if (\request()->ajax()){
            return \view('clients.partials.ajout-rapide-modal',compact('form_juridique_types','client_reference','modifier_reference'));
        }
        return view('clients.ajouter', compact('form_juridique_types', 'client_reference','modifier_reference'));
    }

    public function sauvegarder(ClientStoreRequest $request)
    {
        $this->guard_custom(['client.sauvegarder']);
        try {
            DB::beginTransaction();
            $data = [
                'forme_juridique_id'=>$request->get('forme_juridique'),
                'reference'=>$request->get('reference'),
                'ice'=>$request->get('ice')??null,
                'nom'=>$request->get('nom'),
                'email'=>$request->get('email')??null,
                'telephone'=>$request->get('telephone')??null,
                'note'=>$request->get('note')??null,
                'limite_de_credit'=>$request->get('limite_de_credit')??null,
                'limite_ventes_impayees'=>$request->get('limite_ventes_impayees')??0,
                'adresse'=>$request->get('adresse')??null,
                'ville'=> $request->get('ville')??null,
                'remise_par_defaut' => $request->get('remise_par_defaut')??0,
            ];
            $o_client = Client::create($data);
            ReferenceService::incrementCompteur('clt');
            DB::commit();
            if ($request->ajax()){
                return response(['id'=>$o_client->id,'text'=>$o_client->nom],200);
            }
            return redirect()->route('clients.liste')->with('success', "Client ajouté avec succès");
        } catch (Exception $e) {
            DB::rollBack();
            LogService::logException($e);
            if ($request->ajax()){
                return response("Une erreur s'est produite lors de l'ajout du client",500);
            }
            return redirect()->route('clients.liste')->with('error', "Une erreur s'est produite lors de l'ajout du client");
        }
    }

    public function modifier($id)
    {
        $this->guard_custom(['client.mettre_a_jour']);

        $o_client = Client::find($id);
        if (!$o_client) {
            return redirect()->back()->with('error', "Client n'existe pas");
        }
        $form_juridique_types = FormeJuridique::all();
        $modifier_reference =  GlobalService::get_modifier_reference();
        return view('clients.modifier', compact('o_client', 'form_juridique_types','modifier_reference'));
    }

    public function afficher(Request $request, $id)
    {
        $this->guard_custom(['client.afficher']);
        $o_client = Client::find($id);
        if (!$o_client) {
            abort(404);
        }
        $exercice = session()->get('exercice');
        $types_event = Event::TYPES;
        $payable_types = ModuleService::getPayabaleTypes();

        $query_commandes = Vente::where('type_document','bc')->where('statut','validé')->where('client_id',$id);
        $query_ca = Vente::whereIn('type_document',$payable_types)->where('statut','validé')->where('client_id',$id);
        $query_encaissement = Paiement::where('client_id',$id);
        $query_credit = Vente::whereIn('type_document',$payable_types)->where('statut','validé')->where('client_id',$id);

        if ($request->get('date_emission')) {
            $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_emission'))[0]))->toDateString();
            $end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_emission'))[1]))->toDateString();

            $query_commandes->whereBetween('date_emission', [$start, $end]);
            $query_ca->whereBetween('date_emission', [$start, $end]);
            $query_encaissement->whereBetween('date_paiement', [$start, $end]);
            $query_credit->whereBetween('date_emission', [$start, $end]);
        } else {
            $query_commandes->whereYear('date_emission', $exercice);
            $query_ca->whereYear('date_emission', $exercice);
            $query_encaissement->whereYear('date_paiement', $exercice);
            $query_credit->whereYear('date_emission', $exercice);
        }

        $commandes = $query_commandes->sum('total_ttc');
        $ca = $query_ca->sum('total_ttc');
        $encaissement = $query_encaissement->sum('encaisser');
        $credit = $query_credit->sum('solde');

        $types = array_diff(ModuleService::getActiveModules(),Achat::TYPES);
        $payables = ModuleService::getPayabaleTypes();

        $ventesImpayeQuery = $o_client->ventesImpaye();
        if ($request->get('date_emission')) {
            $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_emission'))[0]))->toDateString();
            $end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_emission'))[1]))->toDateString();
            $ventesImpayeQuery->whereBetween('date_emission', [$start, $end]);
        }
        $ventesImpaye = $ventesImpayeQuery->get();

        if ($request->ajax()) {
            return view('clients.partials.afficher_content', compact('o_client','commandes','ca','encaissement','credit','types','payables','types_event', 'ventesImpaye'))->render();
        }

        return view('clients.afficher', compact('o_client','commandes','ca','encaissement','credit','types','payables','types_event', 'ventesImpaye'));
    }

    public function afficher_ajax(Request $request, $id)
    {
        $o_client = Client::find($id);
        if ($request->ajax()){
            if (!$o_client) {
                return response()->json('',404);
            }
            return response()->json($o_client,200);
        }
        if (!$o_client) {
            return redirect()->back()->with('error', "Client n'existe pas");
        }
    }

    public function mettre_a_jour(ClientUpdateRequest $request, $id)
    {
        $this->guard_custom(['client.mettre_a_jour']);
        DB::beginTransaction();
        try {
            $modifier_reference =  GlobalService::get_modifier_reference();
            $o_client = Client::find($id);
            if (!$o_client) {
                return redirect()->back()->with('error', "Client n'existe pas");
            }
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
                    $o_contact->is_principal = (int) $request->get('contacts_principal') === $key ? : '0';
                    $o_contact->save();
                    $contacts[]= $o_contact->id;
                }
                $o_client->contacts()->sync($contacts);
            }
            $data = [
                'forme_juridique_id'=>$request->get('forme_juridique'),
                'reference' => $modifier_reference ? $request->get('reference') : $o_client->reference ,
                'nom'=>$request->get('nom'),
                'ice'=>$request->get('ice'),
                'email'=>$request->get('email'),
                'telephone'=>$request->get('telephone'),
                'note'=>$request->get('note'),
                'limite_de_credit'=>$request->get('limite_de_credit'),
                'limite_ventes_impayees'=>$request->get('limite_ventes_impayees')??0,
                'adresse'=>$request->get('adresse'),
                'ville'=> $request->get('ville')??null,
            ];
            $o_client->update($data);
            DB::commit();
            return redirect()->route('clients.liste')->with('success', 'Client modifié avec succès');
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            return redirect()->route('clients.liste')->with('error', "Probleme dans modification");
        }
    }

    public function supprimer($id)
    {
        $this->guard_custom(['client.supprimer']);
        if (\request()->ajax()) {
            $o_client = Client::find($id);
            if ($o_client) {
                $o_client->delete();
                return response('Client supprimé avec succès', 200);
            } else {
                return response('Erreur', 404);
            }
        }
    }


    /**
     * Obtient la liste des noms et des identifiants des clients en fonction de la requête de recherche fournie par Ajax.
     * @param Request $request
     * @return JsonResponse
     */
    public function client_select(Request $request)
    {
        if ($request->ajax()) {
            $search = '%' . $request->get('term') . '%';
            $data = Client::where('nom', 'LIKE', $search)->get(['id', 'nom as text','remise_par_defaut']);
            return response()->json($data, 200);
        }
        abort(404);
    }
}
