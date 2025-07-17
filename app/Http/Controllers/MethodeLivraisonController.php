<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\MethodeLivraison;
use App\Services\FileService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MethodeLivraisonController extends Controller
{
    public function liste(){
        $this->guard_custom(['parametres.methodes_livraison']);
        $methode_livraisons = MethodeLivraison::all();
        return view('parametres.methodeLivraison.liste',compact('methode_livraisons'));
    }

    public function sauvegarder(Request $request) {
        $this->guard_custom(['parametres.methodes_livraison']);
        \Validator::validate($request->all(),[
            'nom'=>'required|unique:methode_livraisons|string|max:255',
            'i_image'=>'nullable|image|max:2400|mimes:png,jpeg,jpg'
        ],
        [],
        [
            'nom'=>'nom de méthode',
            'i_imgae'=>'image'
        ]);
        try {
            if ($request->hasFile('i_image')){
                $file = $request->file('i_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = 'public'.DIRECTORY_SEPARATOR. 'methodes-livraison'.DIRECTORY_SEPARATOR.$fileName;
                Storage::disk('external_storage')->put($path, file_get_contents($file));
                $image_name = $fileName;
            }
            MethodeLivraison::create([
                'nom' => $request->get('nom'),
                'image' => $image_name ?? null,
        ]);
            session()->flash('success','Méthode ajouté avec succèe');
            return redirect()->route('methodes-livraison.liste');
        }catch (\Exception $exception){
            LogService::logException($exception);
            session()->flash('error',"Une erreur s'est produite lors de la demande");
            return redirect()->route('methodes-livraison.liste');
        }

    }

    public function modifier($id){
        $this->guard_custom(['parametres.methodes_livraison']);
        $o_methode_livraison = MethodeLivraison::findOrFail($id);
        return view('parametres.methodeLivraison.partials.modifier',compact('o_methode_livraison'));
    }

    public function mettre_a_jour(Request $request , $id){
        $this->guard_custom(['parametres.methodes_livraison']);
        $o_methode_livraison = MethodeLivraison::findOrFail($id);
        \Validator::validate($request->all(),[
            'nom'=>'required|string|max:255|unique:methode_livraisons,nom,'.$id,
            'i_image'=>'nullable|image|max:2400|mimes:png,jpeg,jpg'
        ],
            [],
            [
                'nom'=>'nom de méthode',
                'i_imgae'=>'image'
            ]);
        try {
            if ($request->hasFile('i_image')){
                $file = $request->file('i_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = 'public'.DIRECTORY_SEPARATOR. 'methodes-livraison'.DIRECTORY_SEPARATOR.$fileName;
                Storage::disk('external_storage')->put($path, file_get_contents($file));
                $image_name = $fileName;
            }elseif ($request->has('supprimer_image') && $request->get('supprimer_image')){
                $image_name = null;
            }else {
                $image_name = $o_methode_livraison->image;
            }
            $o_methode_livraison->update([
                'nom' => $request->get('nom'),
                'image' => $image_name ,
            ]);
            session()->flash('success','Méthode modifié avec succèe');
            return redirect()->route('methodes-livraison.liste');
        }catch (\Exception $exception){
            LogService::logException($exception);
            session()->flash('error',"Une erreur s'est produite lors de la demande");
            return redirect()->route('methodes-livraison.liste');
        }
    }

    public function livraison_select(Request $request)
    {
        if ($request->ajax()) {
            $search = '%' . $request->get('term') . '%';
            $data = MethodeLivraison::where('nom', 'LIKE', $search)->get(['id', 'nom as text']);
            return response()->json($data, 200);
        }
        abort(404);
    }

}
