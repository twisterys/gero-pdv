<?php

namespace App\Http\Controllers\Api\sync;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\User;
use App\Models\Article;
use App\Jobs\ProcessCsvJob;
use App\Jobs\ProcessCsvJobAchat;


class GeroSyncController extends Controller
{
    public function handleSyncCommandes(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');

        if ($file) {
            $fileName = 'commandes_' . time() . '.csv';
            $path = $file->storeAs('uploads', $fileName);

            Log::info('Fichier CSV stocké : ' . $path);

            $userId=$request->user()->id;
            $tenantId = tenant()->id;
            ProcessCsvJob::dispatch($path,$userId,$tenantId);

            return response()->json(['message' => 'Fichier reçu et le traitement est en cours...'], 200);
        }

        return response()->json(['error' => 'Aucun fichier reçu'], 400);
    }
}
