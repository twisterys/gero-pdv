<?php

namespace App\Http\Controllers\Api\commercial;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\commercial\CommercialResource;
use App\Models\Commercial;
use Illuminate\Http\Request;

class CommercialController extends Controller
{
    function index()
    {
        return CommercialResource::collection(Commercial::all());
    }

    function show($id)
    {
        return new CommercialResource(Commercial::findOrFail($id));
    }
}
