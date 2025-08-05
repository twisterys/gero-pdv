<?php

namespace App\Http\Controllers\Api\classic;

use App\Http\Controllers\Controller;
use App\Models\MethodesPaiement;

class MethodePaiementController extends Controller
{
    public function liste()
    {
        return MethodesPaiement::where('actif',1)->get(['key as value','nom as label']);
    }
}
