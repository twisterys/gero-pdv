<?php

namespace App\Http\Controllers\Api\commercial;

use App\Http\Controllers\Controller;
use App\Models\PosSession;
use App\Services\LogService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function terminer()
    {
        try {
            PosSession::where('ouverte', 1)->where('user_id', auth()->user()->id)->update(['ouverte' => 0, 'date_fin' => now()]);
            return response('Session est terminée.', 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response('Erreur lors de la fermeture de la session.', 500);
        }
    }
}
