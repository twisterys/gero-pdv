<?php

namespace App\Http\Controllers\Api\commercial;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\commercial\MethodeLivraisonResource;
use App\Models\MethodeLivraison;

class MethodeLivraisonController extends Controller
{
    function index()
    {
        return MethodeLivraisonResource::collection(MethodeLivraison::all());
    }
}
