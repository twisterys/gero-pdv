<?php

namespace App\Http\Controllers\Api\classic;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\classic\ArticleCardResource;
use App\Http\Resources\Api\classic\ArticleResource;
use App\Models\Article;
use App\Models\Famille;
use App\Models\Marque;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Services\PosService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function recherche_par_reference(Request $request)
    {
        $mot = $request->get('reference');
        return ArticleResource::collection(Article::where('reference', $mot)->get());
    }

    public function recherche_liste(Request $request)
    {
        $mot = $request->get('search');
        return ArticleResource::collection(Article::where('designation', 'like', '%' . $mot . '%')->orWhere('reference', 'like', '%' . $mot . '%')->orWhere('code_barre', $mot)->get());
    }

    public function afficher($id)
    {
        return new ArticleResource(Article::find($id));
    }

    public function liste(Request $request)
    {
        $query = Article::query();
        if ($request->get('famille')) {
            $famille = $request->get('famille');
            $query->whereHas('famille', function ($qu) use ($famille) {
                $qu->where('id', $famille);
            });
        }
        if ($request->get('marque')) {
            $marque = $request->get('marque');
            $query->whereHas('marque', function (Builder $qu) use ($marque) {
                $qu->where('id', $marque);
            });
        }
        return ArticleCardResource::collection($query->orderByDesc('reference')->paginate(20));
    }

    public function familles()
    {
        return Famille::where('actif', '=', '1')->select(['id', 'nom'])->get()->toArray();
    }

    public function marques()
    {
        return Marque::select(['id', 'nom'])->get()->toArray();
    }


}
