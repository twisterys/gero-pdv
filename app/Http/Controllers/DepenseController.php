<?php

namespace App\Http\Controllers;

use App\Models\CategorieDepense;
use App\Models\Compte;
use App\Models\Depense;
use App\Models\Magasin;
use App\Models\MethodesPaiement;
use App\Models\Taxe;
use App\Services\GlobalService;
use App\Services\LogService;
use App\Services\PaiementService;
use App\Services\ReferenceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;

class DepenseController extends Controller
{
    public function liste(Request $request)
    {
        $this->guard_custom(['depense.liste']);
        if ($request->ajax()) {
            $o_depenses = Depense::query()->with('categorie');

            if (!$request->get('ignore_exercice')){
                $o_depenses->whereYear('date_operation', session()->get('exercice'));
            }
            if ($request->get('statut_paiement')) {
                $o_depenses->where('statut_paiement', $request->get('statut_paiement'));
            }
            if ($request->get('nom_depense')) {
                $search = '%' . $request->get('nom_depense') . '%';
                $o_depenses->where('nom_depense', 'LIKE', $search);
            }
            if ($request->get('pour')) {
                $o_depenses->where('pour', $request->get('pour'));
            }
            if ($request->get('montant')) {
                $o_depenses->where('montant', '=', $request->get('montant'));
            }


            if ($request->get('categories_id')) {
                $o_depenses->where('categorie_depense_id', $request->get('categories_id'));
            }

            if ($request->get('affaire_id')) {
                $o_depenses->where('affaire_id', $request->get('affaire_id'));
            }

            if ($request->get('date_operation')) {
                $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_operation'))[0]))->toDateString();
                $end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_operation'))[1]))->toDateString();
                if ($start === $end) {
                    $o_depenses->whereDate('date_operation', $end);
                }
                $o_depenses->where(function ($query) use ($start, $end) {
                    $query->whereDate('date_operation', '>=', $start)->whereDate('date_operation', '<=', $end)->orWhereNull('date_operation');
                });

            }
            if ($request->get('order') && $request->get('columns') ){
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order){
                    $o_depenses->orderByRaw(''.$columns[$order['column']]['data'].' '.$order['dir']);
                }
            }
            $table = DataTables::of($o_depenses)->order(function (){});
            $table->addColumn('action', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $show = 'afficher';
                $edit = 'modifier';
                $delete = 'supprimer';
                $crudRoutePart = 'depenses';
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
            })->editColumn('date_operation',function ($row){
                return Carbon::make($row->date_operation)->format('d/m/Y');
            });
            $table->addColumn(
                'selectable_td',
                function ($O_depense) {
                    $id = $O_depense->id;
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
                }
                return '<div class="badge w-100 bg-soft-' . $color . '" >' . __('ventes.' . $row->statut_paiement) . '</div>';
            });

            $table->rawColumns(['actions', 'selectable_td', 'statut_paiement']);

            return $table->make();
        }
        $status_paiement = Depense::STATUTS_DE_PAIEMENT;
        return view('depenses.liste', compact('status_paiement'));
    }

    public function ajouter()
    {
        $this->guard_custom(['depense.sauvegarder']);

        $referenceDepanse = ReferenceService::generateReference('dpa');
        $taxes = Taxe::all();
        $categories = CategorieDepense::where('active', '1')->get();
        $comptes = Compte::ofUser()->get();
        $methodes = MethodesPaiement::where('actif',1)->get();
        $o_magasins = \request()->user()->magasins()->where('active','=','1')->get(['magasin_id as id','nom as text']);
        $magasins_count = Magasin::where('active', '=', '1')->count();
        return view('depenses.ajouter', compact('taxes', 'categories', 'referenceDepanse','comptes','methodes', 'o_magasins', 'magasins_count'));
    }

    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['depense.sauvegarder']);
        $date_permission = !$request->user()->can('depense.date');
        $request->validate([

            'i_reference'=>'required |max:255|unique:depenses,reference' ,
            'i_nom_depense'=>'required | max:255' ,
            'i_categorie'=>'required|exists:categorie_depense,id' ,
            'i_pour'=>'required|min:3|max:255' ,
            'i_date_operation'=>'required' ,
            'i_description'=>'nullable|string' ,
            'i_montant'=>['numeric','min:1','required'],
            'i_compte_id'=>'required_with:regle|exists:comptes,id',
            'i_method_key'=>'required_with:regle|exists:methodes_paiement,key',
            'i_note'=>'nullable|string|max:999',
            'cheque_lcn_date'=>'nullable|string|max:999',
            'i_tax'=>'required|exists:taxes,valeur',
            'i_date' => [Rule::requiredIf(in_array($request->i_method_key, ['cheque', 'lcn'])), 'date_format:d/m/Y', 'nullable'],
            'i_reference_paiement' => [Rule::requiredIf(in_array($request->i_method_key, ['cheque', 'lcn'])), 'max:255'],
            'magasin_id' => ['required', 'exists:magasins,id'],
        ]);
        $magasin_id = $request->get('magasin_id');
        if (!$request->user()->magasins()->where('magasin_id', $magasin_id)->exists()) {
            session()->flash('warning', "Magasin n'est pas accessible");
            return redirect()->back()->withInput($request->input());
        }
        DB::beginTransaction();
        try {
            $o_depense = new Depense();
            $o_depense->reference = $request->get('i_reference');
            $o_depense->nom_depense = $request->get('i_nom_depense');
            $o_depense->categorie_depense_id = $request->get('i_categorie');
            $o_depense->pour = $request->get('i_pour');
            $o_depense->date_operation = $request->get('i_date_operation');
            $o_depense->description = $request->get('i_description');
            $o_depense->montant = $request->get('i_montant');
            $o_depense->solde = $request->get('i_montant');
            $o_depense->statut_paiement = 'non_paye';
            $o_depense->taxe = $request->input('i_tax');
            $o_depense->magasin_id = $request->get('magasin_id');
            $o_depense->save();
            if ($request->input('regle')){
                PaiementService::payer_depense($o_depense->id,[
                    'i_date_paiement' => $date_permission ?Carbon::today()->format('d/m/Y'):  Carbon::make($request->input('i_date_operation'))->format('d/m/Y'),
                    'i_compte_id' => $request->input('i_compte_id'),
                    'i_method_key' =>  $request->input('i_method_key'),
                    'i_note' => $request->input('i_note') ?? null,
                    'i_date'=>$request->input('i_date'),
                    'i_montant'=>$o_depense->montant,
                    'i_reference'=>$request->input('i_reference_paiement'),
                ], $request->get('magasin_id'));
            }
            DB::commit();
            ReferenceService::incrementCompteur('dpa');
            if ($request->ajax()) {
                return response($o_depense, 200);
            }
            session()->flash('success', 'Dépense ajouté');
            return redirect()->route('depenses.liste');
        } catch (Exception $e) {
            DB::rollBack();
            LogService::logException($e);
            session()->flash('error', 'Une erreur est produit ');
            return redirect()->route('depenses.liste');
        }

    }

    public function afficher($id)
    {
        $this->guard_custom(['depense.afficher']);

        $o_depense = Depense::with('categorie')->find($id);
//        dd($o_depense);
        return view('depenses.afficher', compact('o_depense'));
    }

    public function modifier($id)
    {
        $this->guard_custom(['depense.mettre_a_jour']);
        $o_depense = Depense::find($id);
        $categories = CategorieDepense::where('active', '1')->get();

        if (!$o_depense) {
            return redirect()->back()->with('error', "Dépense n'existe pas");
        }
        $taxes = Taxe::all();
        $modifier_reference =  GlobalService::get_modifier_reference();
        $o_magasins = \request()->user()->magasins()->where('active','=','1')->get(['magasin_id as id','nom as text']);
        $magasins_count = Magasin::where('active', '=', '1')->count();
        return view('depenses.modifier', compact('o_depense', 'categories','modifier_reference','taxes', 'o_magasins', 'magasins_count'));
    }

    public function mettre_a_jour(Request $request, int $id)
    {
        $this->guard_custom(['depense.mettre_a_jour']);
        $date_permission = !$request->user()->can('depense.date');
        $o_depense = Depense::find($id);
        if (!$o_depense) {
            abort(404);
        }
        $modifier_reference =  GlobalService::get_modifier_reference();

        $request->validate([
            'i_reference' => $modifier_reference ? ['required ', 'max:255', Rule::unique('depenses', 'reference')->ignore($o_depense->id)] : 'nullable' ,
            'i_nom_depense' => 'required | max:255',
            'i_categorie' => 'nullable|exists:categorie_depense,id',
            'i_pour' => 'required|min:3|max:255',
            'i_date_operation' => 'required',
            'i_description' => 'nullable|string',
            'i_montant' => ['regex:/^[0-9]{1,9}((.|,)[0-9]{1,2})?$/', 'required'],
            'i_tax'=>'required|exists:taxes,valeur',
            'magasin_id' => ['required', 'exists:magasins,id'],
        ]);

        try {
            DB::beginTransaction();
            $o_depense->reference = $modifier_reference ? $request->get('i_reference') : $o_depense->reference;
            $o_depense->nom_depense = $request->get('i_nom_depense');
            $o_depense->categorie_depense_id = $request->get('i_categorie');
            $o_depense->pour = $request->get('i_pour');
            $o_depense->date_operation = $date_permission ? Carbon::today()->format('d/m/Y') :  $request->get('i_date_operation');
            $o_depense->description = $request->get('i_description');
            $o_depense->montant = $request->get('i_montant');
            $o_depense->taxe = $request->integer('i_tax');
            $o_depense->solde = $o_depense->montant - $o_depense->encaisser;
            $o_depense->statut_paiement = PaiementService::get_payable_statut($o_depense->montant,$o_depense->encaisser,$o_depense->solde);
            $o_depense->magasin_id = $request->get('magasin_id');


            $o_depense->save();
            DB::commit();
            return redirect()->route('depenses.liste')->with('success', 'dépense modifer avec success');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('depenses.liste')->with('error', 'problem de modification');
        }


    }

    public function supprimer(int $id)
    {
        $this->guard_custom(['depense.supprimer']);


        if (\request()->ajax()) {
            $o_depense = Depense::find($id);
            if ($o_depense) {
                $o_depense->paiements()->delete();
                $o_depense->delete();
                return response("Dépense supprimé avec success");
            } else {
                return response('erreur', 404);
            }
        }
    }

    public function paiement_modal(int $id)
    {
        $this->guard_custom(['paiement.depense']);

        $o_depense = Depense::find($id);
        if (!$o_depense) {
            return response("Dépense n'existe pas !", 404);
        }
        if ($o_depense->solde == 0) {
            return response("Cette dépense dest déja payé !", 403);
        }
        $comptes = Compte::ofUser()->get();
        $methodes = MethodesPaiement::where('actif','=','1')->get();
        $o_magasins = \request()->user()->magasins()->where('active','=','1')->get(['magasin_id as id','nom as text']);
        $magasins_count = Magasin::where('active', '=', '1')->count();

        return view('depenses.partials.paiement_modal', compact('o_depense', 'comptes', 'methodes', 'o_magasins', 'magasins_count'));
    }

    public function payer(Request $request, $id)
    {
        $this->guard_custom(['paiement.depense']);

        $o_depense = Depense::find($id);
        if (!$o_depense) {
            return response(__("Dépense n'existe pas !!"), 404);
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
            'i_montant' => 'required|min:1|numeric|max:' . $o_depense->solde,
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

        try {
            DB::beginTransaction();
            PaiementService::payer_depense( $o_depense->id, $request->all(), $request->get('magasin_id'));
            DB::commit();
            return response('Paiement réussi', 200);
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur !');
            return redirect()->route('depenses.afficher', $o_depense->id);

        }

    }

}
