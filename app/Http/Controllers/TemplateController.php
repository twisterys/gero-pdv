<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function afficher_parametres(Template $template){
        return view('parametres.documents.parials.template-parametre',compact('template'));
    }
}
