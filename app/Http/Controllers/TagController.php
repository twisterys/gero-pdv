<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function liste()
    {
        $tags = Tag::all();
        return view('parametres.balises.liste',compact('tags'));
    }

    public function sauvegarder(Request $request)
    {
        if ($request->ajax()){
            $rules = [
                'i_nom' => ['required'],
                'i_couleur' => ['required'],
            ];
            $attrs = [
                'i_nom'=>'nom',
                'i_couleur'=>'couleur'
            ];
            \Validator::make($request->all(),$rules,[],$attrs)->validate();
            Tag::create([
                'nom'=>$request->i_nom,
                'couleur'=>$request->i_couleur,
            ]);
            return response('Balise ajouté !',200);
        }
        abort(404);

    }


    public function supprimer(Tag $id)
    {
        if (\request()->ajax()){
            $id->delete();
            return response('Balise supprimé',200);
        }
        abort(404);
    }

    public function balise_select(Request $request){
        if ($request->ajax()) {
            $search = '%' . $request->get('term') . '%';
            $data = Tag::where('nom', 'LIKE', $search)->get(['id', 'nom as text']);
            return response()->json($data, 200);
        }
        abort(404);
    }

    public function modifier($id){
        $tag = Tag::findOrFail($id);
        return view('parametres.balises.partials.modifier',compact('tag'));
    }

    public function mettre_a_jour(Request $request,$id){
        $tag = Tag::findOrFail($id);
        $rules = [
            'i_nom' => ['required'],
            'i_couleur' => ['required'],
        ];
        $attrs = [
            'i_nom'=>'nom',
            'i_couleur'=>'couleur'
        ];
        \Validator::make($request->all(),$rules,[],$attrs)->validate();
        $tag->update([
            'nom' => $request->get('i_nom'),
            'couleur' => $request->get('i_couleur')
        ]);
        return response('Balise mettre à jour !',200);

    }
}
