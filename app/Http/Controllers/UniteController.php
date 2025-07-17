<?php

namespace App\Http\Controllers;

use App\Models\Unite;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class UniteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function liste()
    {
        $this->guard_custom(['parametres.unite']);
        $i_unite = Unite::all();
        return view("parametres.unites.liste", compact('i_unite'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['parametres.unite']);

        if ($request->ajax()) {
            DB::beginTransaction();
            if ($request->get('i_default') == '1') {
                $default_unite = Unite::where('defaut', '1')->update([
                    'defaut' => '0'
                ]);
            }
            $i_unite = Unite::create([
                'nom' => $request->get('i_nom'),
                'defaut' => $request->get('i_default') ?? '0'
            ]);
            DB::commit();
            return $i_unite;
        }
        DB::beginTransaction();
        if ($request->get('i_default') == '1') {
            $default_unite = Unite::where('defaut', '1')->update([
                'defaut' => '0'
            ]);
        }
        $i_unite = Unite::create([
            'nom' => $request->get('i_nom'),
            'defaut' => $request->get('i_default') ?? '0'
        ]);
        DB::commit();
        if ($i_unite) {
            session()->flash('success', 'Unité ajouté');
            return redirect()->route('unites.liste');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifier($id)
    {
        $this->guard_custom(['parametres.unite']);

        $i_unite = Unite::find($id);
        if ($i_unite) {
            $name = $i_unite->nom;
            $default = $i_unite->defaut;
            return view('parametres.unites.modifier', compact('name', 'default', 'id'));
        }
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function mettre_a_jour(Request $request, $id)
    {
        $this->guard_custom(['parametres.unite']);

        $i_unite = Unite::find($id);
        if ($i_unite) {
            try {
                DB::beginTransaction();
                if ($request->get('i_default') == '1') {
                    $default_unite = Unite::where('defaut', '1')->update([
                        'defaut' => '0'
                    ]);

                };

                $i_unite->update([
                    'nom' => $request->get('i_nom'),
                    'defaut' => $request->get('i_default') ?? '0'
                ]);

                DB::commit();
                session()->flash('success', 'Unité mise à jour');
                return redirect()->route('unites.liste');
            } catch (\Exception $exception) {
                LogService::logException($exception);
                session()->flash('error', 'Une erreur est produit');
                return redirect()->route('unites.liste');
            }
        }
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function supprimer($id)
    {
        $this->guard_custom(['parametres.unite']);

        if (\request()->ajax()) {
            $i_unite = Unite::find($id);
            if ($i_unite) {
                $i_unite->delete();
                return response('Unité supprimé', 200);
            } else {
                return response('Erreur', 404);
            }
        }
    }

    public function unite_select(Request $request)
    {

        $search = '%' . $request->get('term') . '%';
        $data = Unite::where('nom', 'LIKE', $search)->get(['id', 'nom as text']);

        return response()->json($data, 200);
    }

    public function modifier_active(Request $request)
    {
        $this->guard_custom(['parametres.unite']);

        $id = $request->get('id');
        $active = $request->get('active');

        DB::table('unites')->where('id', $id)->update(['active' => $active]);
        return response()->json(['success' => 'success']);
    }
}
