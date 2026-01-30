<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\TransfertCaisse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransfertCaisseController extends Controller
{
    public function sauvegarder(Request $request)
    {
        $rules = [
            'compte_source' => 'required|exists:comptes,id',
            'compte_destination' => 'required|exists:comptes,id',
            'date_emission' => 'required|date_format:d/m/Y',
            'date_reception' => 'required|after_or_equal:date_emission|date_format:d/m/Y',
            'i_montant' => 'required|numeric|min:0.001',
            'i_method_key' => 'required|exists:methodes_paiement,key',
        ];
        $attr = [
            'compte_source' => "compte source",
            'compte_destination' => "compte destination",
            'date_emission' => 'date d\'émission',
            'date_reception' => 'date de réception',
            'i_montant' => 'montant',
            'i_method_key' => 'méthode de paiement',
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
        try {
            $transfert = TransfertCaisse::create([
                'reference' => 'TR-' . now()->timestamp,
                'compte_source_id' => $request->input('compte_source'),
                'compte_destination_id' => $request->input('compte_destination'),
                'date_emission' => Carbon::createFromFormat('d/m/Y', $request->date_emission)->format('Y-m-d'),
                'date_reception' => Carbon::createFromFormat('d/m/Y', $request->date_reception)->format('Y-m-d'),
                'montant' => $request->input('i_montant'),
                'methode_paiement_key' => $request->input('i_method_key'),
                'description' => $request->input('description')
            ]);
            //Creer la line de decaissement depuis le compte source
            $data_decaisser = [
                'payable_id' => $transfert->id,
                'payable_type' => TransfertCaisse::class,
                'compte_id' => $request->get('compte_source'),
                'methode_paiement_key' => $request->get('i_method_key'),
                'date_paiement' => Carbon::createFromFormat('d/m/Y', $request->get('date_emission'))->toDateString(),
                'created_by' =>auth()->user()->id,
            ];
            $data_decaisser['decaisser'] = $request->get('i_montant');
            $data_decaisser['encaisser'] = 0;
            Paiement::create($data_decaisser);



            //Creer la line de decaissement depuis le compte destination
            $data_encaisser = [
                'payable_id' => $transfert->id,
                'payable_type' => TransfertCaisse::class,
                'compte_id' => $request->get('compte_destination'),
                'methode_paiement_key' => $request->get('i_method_key'),
                'date_paiement' => Carbon::createFromFormat('d/m/Y', $request->get('date_reception'))->toDateString(),
                'created_by' =>auth()->user()->id,
            ];
            $data_encaisser['encaisser'] = $request->get('i_montant');
            $data_encaisser['decaisser'] = 0;
            Paiement::create($data_encaisser);

            return redirect()->route('paiement.liste')->with('success', 'Transfert ajouté avec succès');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
