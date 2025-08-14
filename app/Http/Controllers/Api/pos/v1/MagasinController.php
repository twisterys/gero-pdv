<?php

namespace App\Http\Controllers\Api\pos\v1;

use App\Http\Controllers\Controller;
use App\Models\Magasin;
use App\Models\PosSession;

class MagasinController extends Controller
{
    public function liste()
    {
        $user = auth()->user();
        $o_pos_session =  PosSession::where('ouverte', 1)->where('user_id', $user->id)->first();
        return Magasin::where('id','!=' ,$o_pos_session->magasin_id)->where('type_local',1)->get(['id', 'nom']);
    }
}
