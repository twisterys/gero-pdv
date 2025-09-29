<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use App\Models\Cheque;
use App\Models\Compte;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ChequeController extends Controller
{
    public function encaisser_liste(Request $request)
    {
        $this->guard_custom(['cheque.liste']);
        if ($request->ajax()) {
            $query = Cheque::where('type', 'encaissement')->whereYear('date_emission', session()->get('exercice'));

            if ($request->get('date_debut') && $request->get('date_fin')) {
                $query->whereBetween('date_emission', [$request->date_debut, $request->date_fin]);
            }

            if ($request->get('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            if ($request->get('banque_id')) {
                $query->where('banque_id', $request->banque_id);
            }

            if ($request->get('compte_id')) {
                $query->where('compte_id', $request->compte_id);
            }

            if ($request->get('statut')) {
                $query->where('statut', $request->statut);
            }

            if ($request->get('numero')) {
                $query->where('number', $request->numero);
            }

            $table = DataTables::of($query);
            $table->addColumn('actions', function ($row) {
                $actions = '<a data-url="' . route('cheques.modifier', $row->id) . '" data-target="updateChequeModal" class="btn btn-sm btn-warning cheque-edit mx-1"> <i class="fa fa-edit"></i></a>';
                if ($row->statut === 'en_attente') {
                    $actions .= '<a data-url="' . route('cheques.encaisser', $row->id) . '" data-target="encaisserChequeModal" class="btn btn-sm btn-success mx-1 cheque-encaisser"> <i class="fa fa-check"></i></a>';
                    $actions .= '<a data-url="' . route('cheques.annuler', $row->id) . '" data-target="annulerChequeModal" class="btn btn-sm btn-danger mx-1 cheque-annuler"> <i class="fa fa-times"></i></a>';

                }
                return $actions;
            })->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            }
            )->addColumn('client', function (Cheque $row) {
                return $row->client->nom;
            })->addColumn('banque_emettrice', function (Cheque $row) {
                return $row->banque->nom;
            })->addColumn('compte_bancaire', function (Cheque $row) {
                return $row?->compte?->nom;
            })->editColumn('statut', function ($row) {
                $color = $row->statut === 'en_attente' ? 'warning' : ($row->statut === 'traite' ? 'success' : 'danger');
                return '<span class="badge bg-soft-' . $color . '">' . __('cheques.' . $row->statut) . '</span>';
            })->rawColumns(['actions', 'selectable_td', 'statut']);
            return $table->make(true);
        }
        $comptes = Compte::ofUser()->where('type', 'banque')->get();
        $banques = Banque::get();
        $statuts = Cheque::STATUTS;
        return view('cheques.encaisser', compact('comptes', 'banques', 'statuts'));
    }


    public function decaisser_liste(Request $request)
    {
        $this->guard_custom(['cheque.liste']);
        if ($request->ajax()) {
            $query = Cheque::where('type', 'decaissement')->whereYear('date_emission', session()->get('exercice'));

            if ($request->get('date_debut') && $request->get('date_fin')) {
                $query->whereBetween('date_emission', [$request->date_debut, $request->date_fin]);
            }

            if ($request->get('fournisseur_id')) {
                $query->where('fournisseur_id', $request->fournisseur_id);
            }


            if ($request->get('compte_id')) {
                $query->where('compte_id', $request->compte_id);
            }

            if ($request->get('statut')) {
                $query->where('statut', $request->statut);
            }

            if ($request->get('numero')) {
                $query->where('number', $request->numero);
            }

            $table = DataTables::of($query);
            $table->addColumn('actions', function ($row) {
                $actions = '<a data-url="' . route('cheques.modifier', $row->id) . '" data-target="updateChequeModal" class="btn btn-sm btn-warning cheque-edit mx-1"> <i class="fa fa-edit"></i></a>';
                if ($row->statut === 'en_attente') {
                    $actions .= '<a data-url="' . route('cheques.decaisser', $row->id) . '" data-target="decaisserChequeModal" class="btn btn-sm btn-success mx-1 cheque-decaisser"> <i class="fa fa-check"></i></a>';
                    $actions .= '<a data-url="' . route('cheques.annuler', $row->id) . '" data-target="annulerChequeModal" class="btn btn-sm btn-danger mx-1 cheque-annuler"> <i class="fa fa-times"></i></a>';
                }
                return $actions;                return $actions;
            })->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            }
            )->addColumn('fournisseur', function (Cheque $row) {
                return $row->fournisseur->nom;
            })->addColumn('compte_bancaire', function (Cheque $row) {
                return $row?->compte?->nom;
            })->editColumn('statut', function ($row) {
                $color = $row->statut === 'en_attente' ? 'warning' : ($row->statut === 'traite' ? 'success' : 'danger');
                return '<span class="badge bg-soft-' . $color . '">' . __('cheques.' . $row->statut) . '</span>';
            })->rawColumns(['actions', 'selectable_td','statut']);
            return $table->make(true);
        }
        $comptes = Compte::ofUser()->where('type', 'banque')->get();
        $banques = Banque::get();
        $statuts = Cheque::STATUTS;
        return view('cheques.decaisser', compact('comptes', 'banques','statuts'));
    }


    public function sauvegarder(Request $request, $type)
    {
        $this->guard_custom(['cheque.sauvegarder']);
        if (!in_array($type, ['encaissement', 'decaissement'])) {
            abort(404);
        }
        $rules = [
            'banque' => 'nullable|exists:banques,id',
            'date_emission' => 'required|date_format:d/m/Y',
            'date_echeance' => 'required|date_format:d/m/Y',
            'i_compte_id' => 'nullable|exists:comptes,id',
            'montant_encaisse' => 'required|numeric|min:1|max:9999999',
            'numero_transaction' => 'required|unique:cheques,number',
            'note' => 'nullable|string'
        ];
        if ($type === 'encaissement') {
            $rules['client_id'] = 'required|exists:clients,id';
        } else {
            $rules['fournisseur_id'] = 'required|exists:fournisseurs,id';
        }

        Validator::make($request->all(), $rules, [], [
            'banque' => 'Banque',
            'type' => 'Type',
            'date_emission' => 'Date d\'émission',
            'date_echeance' => 'Date d\'échéance',
            'i_compte_id' => 'Compte bancaire',
            'montant_encaisse' => 'Montant encaissé',
            'numero_transaction' => 'Numéro de transaction',
            'client_id' => 'Client',
            'fournisseur_id' => 'Fournisseur',
            'note' => 'Note'
        ])->validate();

        Cheque::create([
            'type' => $type,
            'number' => $request->numero_transaction,
            'montant' => $request->montant_encaisse,
            'date_emission' => Carbon::createFromFormat('d/m/Y', $request->date_emission),
            'date_echeance' => Carbon::createFromFormat('d/m/Y', $request->date_echeance),
            'statut' => 'en_attente',
            'banque_id' => $request->banque ?? null,
            'client_id' => $request->client_id ?? null,
            'compte_id' => $request->i_compte_id,
            'fournisseur_id' => $request->fournisseur_id ?? null,
            'note' => $request->note
        ]);

        return response('le cheque a été  sauvegarder en succès', 200);
    }


    public function modifier($id)
    {
        $this->guard_custom(['cheque.mettre_a_jour']);
        $cheque = Cheque::findOrFail($id);
        $comptes = Compte::ofUser()->where('type', 'banque')->get();
        $banques = Banque::get();
        return view('cheques.partials.modifier_modal', compact('cheque', 'comptes', 'banques'));
    }

    public function mettre_a_jour(Request $request, $id)
    {
        $this->guard_custom(['cheque.mettre_a_jour']);
        $o_cheque = Cheque::findOrFail($id);
        $rules = [
            'update_banque' => 'nullable|exists:banques,id',
            'update_date_emission' => 'required|date_format:d/m/Y',
            'update_date_echeance' => 'required|date_format:d/m/Y',
            'update_i_compte_id' => 'nullable|exists:comptes,id',
            'update_montant_encaisse' => 'required|numeric|min:1|max:9999999',
            'update_note' => 'nullable|string'
        ];
        if ($o_cheque->type === 'encaissement') {
            $rules['update_client_id'] = 'required|exists:clients,id';
        } else {
            $rules['update_fournisseur_id'] = 'required|exists:fournisseurs,id';
        }

        Validator::make($request->all(), $rules, [], [
            'update_banque' => 'Banque',
            'update_type' => 'Type',
            'update_date_emission' => 'Date d\'émission',
            'update_date_echeance' => 'Date d\'échéance',
            'update_i_compte_id' => 'Compte bancaire',
            'update_montant_encaisse' => 'Montant encaissé',
            'update_client_id' => 'Client',
            'update_fournisseur_id' => 'Fournisseur',
            'update_note' => 'Note'
        ])->validate();

        $o_cheque->update([
            'montant' => $request->update_montant_encaisse,
            'date_emission' => Carbon::createFromFormat('d/m/Y', $request->update_date_emission),
            'date_echeance' => Carbon::createFromFormat('d/m/Y', $request->update_date_echeance),
            'banque_id' => $request->update_banque,
            'client_id' => $request->update_client_id ?? null,
            'compte_id' => $request->update_i_compte_id,
            'fournisseur_id' => $request->update_fournisseur_id ?? null,
            'note' => $request->update_note
        ]);

        return response('le cheque a été  modifié en succès', 200);
    }


    public function encaisser(Request $request, $id)
    {
        $this->guard_custom(['cheque.encaisser']);
        if ($request->ajax()) {
            $cheque = Cheque::findOrFail($id);
            if ($cheque->type === 'decaissement' || $cheque->statut !== 'en_attente') {
                return response('le cheque ne peut pas être encaissé', 400);
            }
            $cheque->update(['statut' => 'traite']);
            return response('le cheque a été encaissé en succès', 200);
        }
    }


    public function decaisser(Request $request, $id)
    {
        $this->guard_custom(['cheque.decaisser']);
        if ($request->ajax()) {
            $cheque = Cheque::findOrFail($id);
            if ($cheque->type === 'encaissement' || $cheque->statut !== 'en_attente') {
                return response('le cheque ne peut pas être décaissé', 400);
            }
            $cheque->update(['statut' => 'traite']);
            return response('le cheque a été décaissé en succès', 200);
        }
    }


    public function annuler(Request $request, $id)
    {
        $this->guard_custom(['cheque.annuler']);
        if ($request->ajax()) {
            $cheque = Cheque::findOrFail($id);
            if ($cheque->statut !== 'en_attente') {
                return response('le cheque ne peut pas être annulé', 400);
            }
            $cheque->update(['statut' => 'annule']);
            return response('le cheque a été annulé en succès', 200);
        }
    }
}
