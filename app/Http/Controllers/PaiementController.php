<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Compte;
use App\Models\Depense;
use App\Models\MethodesPaiement;
use App\Models\Operation;
use App\Models\Paiement;
use App\Models\TransfertCaisse;
use App\Models\User;
use App\Models\Vente;
use App\Services\LogService;
use App\Services\ModuleService;
use App\Services\PaiementService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use Yajra\DataTables\DataTables;
use function request;

class PaiementController extends Controller
{
    public function liste(Request $request)
    {
        $this->guard_custom(['paiement.liste']);
        if ($request->ajax()) {
            $query = Paiement::with('payable', 'compte', 'methodePaiement');
            // -------------------### Filters ###-------------------
            if ($request->get('client_id')) {
                $query->where('client_id', $request->get('client_id'));
            }
            if ($request->get('fournisseur_id')) {
                $query->where('fournisseur_id', $request->get('fournisseur_id'));
            }
            if ($request->get('montant')) {
                $search = '%' . $request->get('montant') . '%';
                $query->where('encaisser', 'LIKE', $search)->orWhere('decaisser', 'LIKE', $search);
            }
            if ($request->get('date')) {
                $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date'))[0]))->toDateString();
                $end =  Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date'))[1]))->toDateString();
                if ($start === $end) {
                    $query->whereDate('date_paiement', $end);
                }
                $query->where(function ($query) use ($start, $end) {
                    $query->whereBetween('date_paiement', [$start, $end]);
                });
            }
            if ($request->get('compte_id')) {
                $query->where('compte_id', $request->get('compte_id'));
            }
            if ($request->get('methode_paiement_key')) {
                $query->where('methode_paiement_key', $request->get('methode_paiement_key'));
            }
            if ($request->get('created_by')) {
                $query->where('created_by', $request->get('created_by'));
            }

            if($request->get('type_op')){
               $type_op =  $request->get('type_op') ;
               switch ($type_op)
               {
                   case 'vente' :
                       $query->where('payable_type', 'App\\Models\\Vente'); break ;
                   case 'achat' :
                       $query->where('payable_type', 'App\\Models\\Achat'); break ;
                   case 'depense' :
                       $query->where('payable_type', 'App\\Models\\Depense'); break ;
                    case 'banque' :
                       $query->where('payable_type', 'App\\Models\\Operation'); break ;
                   default  : ;
               }

            }
            // ------------------- ### End of filters ### -------------------
            $table = DataTables::of($query);
            $table->addColumn('actions', function ($row) {
                $delete = 'supprimer';
                $crudRoutePart = 'paiement';
                $edit_modal = ['url' => route('paiement.modifier', ['id' => $row->id]), 'modal_id' => 'paiement-edit-modal'];
                $show_modal = ['url' => route('paiement.afficher',['id'=>$row->id]),'modal_id'=>'paiement-edit-modal'];
                $id = $row?->id;
                return view(
                    'partials.__datatable-action',
                    compact(
                        'delete',
                        'crudRoutePart',
                        'id',
                        'edit_modal',
                        'show_modal'
                    )
                )->render();
            })->addColumn(
                'selectable_td',
                function ($row) {
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $row->id . '">';
                }
            )->editColumn('compte_id', function ($row) {
                return $row->compte->nom;
            })->editColumn('encaisser', function ($row) {
                return '<span class="text-success" >' . number_format($row->encaisser, 3, '.', ' ') . ' MAD</span>';
            })->editColumn('decaisser', function ($row) {
                return '<span class="text-danger" >' . number_format($row->decaisser, 3, '.', ' ') . ' MAD</span>';
            })->editColumn('payable_id', function ($row) {
                $route = null;
                if ($row->payable_type === Achat::class) {
                    $route = route('achats.afficher', ['type' => $row->payable->type_document, 'id' => $row->payable->id]);
                } elseif ($row->payable_type === Depense::class) {
                    $route = route('depenses.afficher', $row->payable->id);
                } elseif ($row->payable_type === Vente::class) {
                    $route = route('ventes.afficher', ['type' => $row->payable->type_document, 'id' => $row->payable->id]);
                }
                return '<a class="text-info text-decoration-underline" target="_blank" '.($route ? "href=\"".$route."\"" : "").' >' . $row->payable->reference . '</a>';
            })->editColumn('methode_paiement_key', function ($row) {
                return $row->methodePaiement->nom;
            })->editColumn('created_by',function ($row){
                return $row->user ? $row->user->name : '---';
            })->addColumn('objet', function ($row){
                $objet = null ;
                if($row->payable_type === Achat::class){
                    $objet = $row->payable->objet;
                }elseif ($row->payable_type === Vente::class) {
                    $objet = $row->payable->objet;
                } elseif ($row->payable_type === Depense::class) {
                    $objet = $row->payable->nom_depense;
                }
                return $objet;
            });
            $table->rawColumns(['selectable_td', 'actions', 'payable_id', 'encaisser', 'decaisser']);
            return $table->make();
        }
        $comptes = Compte::all();
        $methodes = MethodesPaiement::where('actif', 1)->get();
        $operations = Operation::all();
        $users = User::get(['id','name']);
        return view('paiements.liste', compact('comptes', 'methodes', 'operations','users'));
    }

    public function modifier($id)
    {
        $this->guard_custom(['paiement.sauvegarder']);
        if (request()->ajax()) {
            $o_paiement = Paiement::findOrFail($id);
            $comptes = Compte::all();
            $methodes = MethodesPaiement::where('actif', 1)->get();
            return view('paiements.patials.modifier_modal', compact('o_paiement', 'comptes', 'methodes'));
        }
    }

    public function afficher ($id){
        $this->guard_custom(['paiement.liste']);
        $o_paiement = Paiement::findOrFail($id);
        return view('paiements.patials.afficher_modal',compact('o_paiement'));
    }

    public function mettre_a_jour(Request $request, $id)
    {
        $this->guard_custom(['paiement.mettre_a_jour']);
        $o_paiement = Paiement::findOrFail($id);
        $attributes = [
            'i_compte_id' => "compte",
            'i_method_key' => 'méthode de paiement ',
            'i_date' => 'date prévu',
            'i_date_paiement' => 'date de paiement',
            'i_reference' => 'référence de chéque',
            'i_note' => 'note',
        ];
        $rules = [
            'i_compte_id' => 'required|exists:comptes,id',
            'i_method_key' => ['required', 'exists:methodes_paiement,key'],
            'i_date_paiement' => ['required', 'date_format:d/m/Y'],
        ];
        if (in_array($request->i_method_key, ['cheque', 'lcn'])){
            $rules['i_date']='required|date_format:d/m/Y';
            $rules['i_reference']='required|max:255';
        }else {
            $request->i_date =null;
            $request->i_reference =null;
        }
        $validation = Validator::make($request->all(), $rules, [], $attributes);
        if ($validation->fails()) {
            $messaget = '';
            foreach ($validation->messages()->getMessages() as $message) {
                $messaget .= $message[0] . '<br>';
            }
            return response($messaget, 400);
        }
        $o_paiement->update([
            'compte_id' => $request->i_compte_id,
            'methode_paiement_key' => $request->i_method_key,
            'date' => $request->i_date ? Carbon::createFromFormat('d/m/Y', $request->i_date)->format('Y-m-d') : null,
            'date_paiement' => $request->i_date_paiement ? Carbon::createFromFormat('d/m/Y', $request->i_date_paiement)->format('Y-m-d') : null,
            'note' => $request->i_note,
            'cheque_lcn_date' => $request->i_date ? Carbon::createFromFormat('d/m/Y', $request->i_date)->format('Y-m-d') : null,
            'cheque_lcn_reference' => $request->i_reference
        ]);
        activity()
            ->causedBy(Auth::user())
            ->event('Modification')
            ->withProperties([
                'subject_type' => $o_paiement->payable::class,
                'subject_id' => $o_paiement->payable->id,
                'subject_reference' => ($o_paiement->payable::class === 'App\Models\Achat') ? $o_paiement->payable->reference_interne : $o_paiement->payable->reference,
            ])
            ->log('Modification du paiement de '. abs($o_paiement->encaisser - $o_paiement->decaisser) .' DH' );
        return response('Paiement mise à jour', 200);
    }

    public function supprimer($id){
        $this->guard_custom(['paiement.supprimer']);
        $o_paiement = Paiement::find($id);
        if (!$o_paiement) {
            abort(404);
        }
        $payable = $o_paiement->payable;
        DB::beginTransaction();
        $encaissement_types = ModuleService::getEncaissementTypes();
        $decaissement_types = ModuleService::getDecaissementTypes();
        try {
            if ($o_paiement->payable_type === Vente::class) {
                if (in_array($payable->type_document, $encaissement_types)) {
                    $payable->encaisser -= $o_paiement->encaisser;
                    $payable->solde += $o_paiement->encaisser;
                } elseif (in_array($payable->type_document, $decaissement_types)) {
                    $payable->encaisser -= $o_paiement->decaisser;
                    $payable->solde += $o_paiement->decaisser;
                }
                $payable->statut_paiement = PaiementService::get_payable_statut($payable->total_ttc, $payable->encaisser, $payable->solde);
                $payable->save();
                $o_paiement->delete();
                DB::commit();
                activity()
                    ->causedBy(Auth::user())
                    ->event('Suppression')
                    ->withProperties([
                        'subject_type' => $o_paiement->payable::class,
                        'subject_id' => $o_paiement->payable->id,
                        'subject_reference' => $o_paiement->payable->reference,
                    ])
                    ->log('Suppression du paiement de '. $o_paiement->encaisser .' DH' );
                session()->flash('success', "Paiement supprimé avec succès");
                if (request()->ajax()) {
                    return response('Paiement supprimé avec succès');
                }
                return redirect()->route('ventes.afficher', [$payable->type_document, $payable->id]);
            } elseif ($o_paiement->payable_type === Achat::class) {
                if (in_array($payable->type_document, $encaissement_types)) {
                    $payable->credit -= $o_paiement->encaisser;
                    $payable->debit += $o_paiement->encaisser;
                } elseif (in_array($payable->type_document, $decaissement_types)) {
                    $payable->credit -= $o_paiement->decaisser;
                    $payable->debit += $o_paiement->decaisser;
                }
                $payable->statut_paiement = PaiementService::get_payable_statut($payable->total_ttc, $payable->credit, $payable->debit);
                $payable->save();
                $o_paiement->delete();
                DB::commit();
                activity()
                    ->causedBy(Auth::user())
                    ->event('Suppression')
                    ->withProperties([
                        'subject_type' => $o_paiement->payable::class,
                        'subject_id' => $o_paiement->payable->id,
                        'subject_reference' => $o_paiement->payable->reference_interne,
                    ])
                    ->log('Suppression du paiement de '. abs($o_paiement->encaisser - $o_paiement->decaisser) .' DH' );
                session()->flash('success', "Paiement supprimé avec succès");
                if (request()->ajax()) {
                    return response('Paiement supprimé avec succès');
                };
                return redirect()->route('achats.afficher', [$payable->type_document, $payable->id]);
            } elseif ($o_paiement->payable_type === Depense::class) {
                $payable->solde += $o_paiement->decaisser;
                $payable->encaisser -= $o_paiement->decaisser;
                $payable->statut_paiement = PaiementService::get_payable_statut($payable->montant, $payable->encaisser, $payable->solde);
                $payable->save();
                $o_paiement->delete();
                DB::commit();
                session()->flash('success', "Paiement supprimé avec succès");
                if (request()->ajax()) {
                    return response('Paiement supprimé avec succès');
                }
                return redirect()->route('depenses.afficher', [$payable->id]);
            } elseif ($o_paiement->payable_type === Operation::class) {
                $o_paiement->delete();
                DB::commit();
                session()->flash('success', "Paiement supprimé avec succès");
                if (request()->ajax()) {
                    return response('Paiement supprimé avec succès');
                }
                return redirect()->route('paiement.liste');
            }
            elseif ($o_paiement->payable_type === TransfertCaisse::class) {
                $o_paiement->delete();
                DB::commit();
                session()->flash('success', "Paiement supprimé avec succès");
                if (request()->ajax()) {
                    return response('Paiement supprimé avec succès');
                }
                return redirect()->route('paiement.liste');
            }
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', "");
            if (request()->ajax()) {
                return response('Erreur ', 500);
            }
            if ($o_paiement->payable_type === Vente::class) {
                return redirect()->route('ventes.afficher', [$payable->type_document, $payable->id]);
            } elseif ($o_paiement->payable_type === Achat::class) {
                return redirect()->route('achats.afficher', [$payable->type_document, $payable->id]);
            } elseif ($o_paiement->payable_type === Depense::class) {
                return redirect()->route('depenses.afficher', [$payable->type_document, $payable->id]);
            }
        }
        abort(404);
    }

    public function sauvegarder_operation(Request $request)
    {
        $this->guard_custom(['paiement.operation_bancaire']);
        $rules = [
            'i_date_paiement' => 'required|date_format:d/m/Y',
            'i_operation_id' => 'required|exists:operations,id',
            'i_montant' => 'required|numeric|min:0.001|max:9999999999',
            'i_compte_id' => 'required|exists:comptes,id',
            'i_method_key' => 'required|exists:methodes_paiement,key',
            'i_date' => 'nullable|date_format:d/m/Y'
        ];
        $attr = [
            'i_date_paiement' => "date de paiement",
            'i_operation_id' => "opération",
            'i_montant' => 'montant de paiement',
            'i_compte_id' => 'compte',
            'i_method_key' => 'déthode de paiement',
            'i_date' => 'date prévu',
        ];
        $validation = \Validator::make($request->all(), $rules, [], $attr);
        if ($validation->fails()) {
            $messaget = '';
            foreach ($validation->messages()->getMessages() as $message) {
                $messaget .= $message[0] . '<br>';
            }
            session()->flash('error', $messaget);
            return redirect()->route('paiement.liste');
        }
        $o_operation = Operation::find($request->get('i_operation_id'));
        $data = [
            'payable_id' => $o_operation->id,
            'payable_type' => Operation::class,
            'compte_id' => $request->get('i_compte_id'),
            'methode_paiement_key' => $request->get('i_method_key'),
            'date_paiement' => Carbon::createFromFormat('d/m/Y', $request->get('i_date_paiement'))->toDateString(),
            'created_by' =>auth()->user()->id,
        ];
        if (in_array($request->get('i_method_key'), ['cheque', 'lcn'])) {
            $data['cheque_lcn_reference'] = $data['i_reference'];
            $data['cheque_lcn_date'] = Carbon::createFromFormat('d/m/Y', $request->get('i_date'))->toDateString();
        }
        if ($o_operation->action == 'decaisser') {
            $data['decaisser'] = $request->get('i_montant');
            $data['encaisser'] = 0;
        } else {
            $data['encaisser'] = $request->get('i_montant');
            $data['decaisser'] = 0;
        }
        Paiement::create($data);
        if ($request->ajax()){
            return response('Opération ajouté avec succès');
        }
        session()->flash('success', '');
        return redirect()->route('paiement.liste');
    }
}
