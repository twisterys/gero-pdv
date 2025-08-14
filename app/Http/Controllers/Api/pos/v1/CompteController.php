<?php

namespace App\Http\Controllers\Api\pos\v1;

use App\Http\Controllers\Controller;
use App\Models\Compte;

class CompteController extends Controller
{
   public function liste(){
       return Compte::where('statut',1)->get(['id as value','nom as label']);
   }
}
