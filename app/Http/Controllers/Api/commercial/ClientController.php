<?php

namespace App\Http\Controllers\Api\commercial;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientStoreRequest;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Services\LogService;
use App\Services\ReferenceService;
use DB;
use Exception;
use Illuminate\Validation\Rule;
use Log;

class ClientController extends Controller
{
    public function recherche_liste(Request $request)
    {
        $mot = '%' . $request->get('search') . '%';
        return Client::where('nom', 'like', $mot)->select('id', 'nom')->get();
    }

    public function sauvegarder(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|min:3|max:100',
            'telephone' => ["nullable", "string", "regex:/^(((00|\+)212)|(0))(6|7|8|5)[0-9]{8}$/"],
            'city'=>'nullable|string'
        ]);
        try {
            DB::beginTransaction();
            $data = [
                'reference' => ReferenceService::generateReference('clt'),
                'nom' => $request->get('nom'),
                'telephone' => $request->get('telephone') ?? null,
                'ville'=> $request->get('city')??null
            ];
            $o_client = Client::create($data);
            ReferenceService::incrementCompteur('clt');
            DB::commit();
            return response(['client' => [
                'value' => $o_client->id,
                'label' => $o_client->nom
            ], 'message' => 'Client ajouté avec succès !'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            LogService::logException($e);
            return response("Une erreur s'est produite lors de l'ajout du client", 500);
        }
    }
}
