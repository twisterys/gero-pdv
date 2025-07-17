<?php

namespace App\Http\Controllers\Api\parfums;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\parfums\ArticleResource;
use App\Models\Article;
use App\Models\VenteLigne;
use App\Services\PosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function recherche_par_reference(Request $request){
        $mot = $request->get('reference');
        return ArticleResource::collection(Article::where('reference',$mot)->get());
    }
    public function recherche_liste(Request $request)
    {
        $mot = $request->get('search');
        $articles = Article::whereRaw('MATCH (designation, reference) AGAINST (? IN BOOLEAN MODE)', [$mot])
            ->orWhere('designation', 'LIKE', "%{$mot}%")
            ->orWhere('reference', 'LIKE', "%{$mot}%")
            ->orWhere('code_barre', 'LIKE', "%{$mot}%")
            ->get();
        return ArticleResource::collection($articles);
    }







    public function afficher($id){
        return new ArticleResource(Article::find($id));
    }


}
