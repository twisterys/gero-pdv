<?php

namespace App\Http\Controllers\Api\classic;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\parfums\DepenseResource;
use App\Models\Compte;
use App\Models\Depense;
use App\Models\PosSession;
use App\Services\PaiementService;
use App\Services\ReferenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepenseController extends Controller
{
    public function sauvegarder(Request $request)
    {
        Validator::make($request->all(), [
            'nom' => 'required | max:255',
            'category_id' => 'required|exists:categorie_depense,id',
            'benificiaire' => 'nullable|min:3|max:255',
            'montant' => ['regex:/^[0-9]{1,9}((.|,)[0-9]{1,2})?$/', 'required'],
        ],
            [],
            [
                'nom' => 'nom',
                'category_id' => 'catégorie',
                'benificiaire' => 'bénéficiaire'
            ])->validate();

        $reference = ReferenceService::generateReference('dpa');
        $o_pos_session = PosSession::find($request->get('session_id'));

        $o_depense = Depense::create([
            'reference' => $reference,
            'nom_depense' => $request->input('nom'),
            'montant' => $request->input('montant'),
            'categorie_depense_id' => $request->input('category_id'),
            'pour' => $request->input('benificiaire'),
            'date_operation' => Carbon::today()->toDateString(),
            'solde' => $request->input('montant'),
            'pos_session_id' => $request->input('session_id'),
            'magasin_id' => $o_pos_session->magasin_id,
        ]);
        PaiementService::payer_depense($o_depense->id, [
            'i_date_paiement' => Carbon::now()->format('d/m/Y'),
            'i_compte_id' => Compte::ofUser()->where('principal', 1)->first()?->id ?? (Compte::ofUser()->first()?->id ?? Compte::first()->id) ,
            'i_method_key' => 'especes',
            'i_note' => null,
            'i_date' => Carbon::now()->format('d/m/Y'),
            'i_montant' => $o_depense->montant,
            'i_reference' => '',
        ]);

        ReferenceService::incrementCompteur('dpa');
        return response('Dépense ajouté avec succès !');
    }

    public function liste(Request $request)
    {
        $depenses = Depense::where('pos_session_id', $request->get('session_id'))->get();
        return DepenseResource::collection($depenses);
    }
}
