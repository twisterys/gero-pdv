<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\GlobalSetting;
use App\Models\Magasin;
use App\Models\Rebut;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RebutController extends Controller
{
    public function controle($id)
    {
        $this->guard_custom(['rebut.controler']);
        try {
            $rebut = Rebut::findOrFail($id);
            $rebut->update([
                'is_controled' => true,
                'controled_at' => now(),
                'controled_by' => auth()->id()
            ]);

            activity()
                ->causedBy(Auth::user())
                ->event('Contrôle')
                ->withProperties([
                    'subject_type' => Rebut::class,
                    'subject_id' => $rebut->id,
                    'subject_reference' => $rebut->reference,
                ])
                ->log('Contrôle effectué sur rebut ' . $rebut->reference);

            return response()->json([
                'success' => true,
                'message' => 'Rebut contrôlé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du contrôle du rebut: ' . $e->getMessage()
            ], 500);
        }
    }

    public function liste(Request $request){
        $this->guard_custom(['rebut.*']);
        if ($request->ajax()){
            $query = Rebut::with('magasin');
            if ($request->filled('statut_controle')) {
                if ($request->statut_controle === 'controle') {
                    $query->where('is_controled', 1);
                } elseif ($request->statut_controle === 'non_controle') {
                    $query->where('is_controled', 0);
                }
            }
            $table = DataTables::of($query);
            $table->addColumn('actions',function ($row){
                $crudRoutePart = 'rebuts';
                $show = 'afficher';
                $rollback ='rollback';
                $id = $row->id;
                if ($row->statut !== "Rebut annulé") {
                    return view(
                        'partials.__datatable-action',
                        compact(
                            'crudRoutePart',
                            'rollback',
                            'show',
                            'id',
                        )
                    );
                } else {
                    return view(
                        'partials.__datatable-action',
                        compact(
                            'crudRoutePart',
                            'show',
                            'id',
                        )
                    );
                }

            });
            $table->editColumn('date_operation', function ($row) {
                return \Carbon\Carbon::make($row->date_operation)->format('d/m/Y');
            });
            $table->editColumn('is_controled', function ($row) {
                if ($row->is_controled) {
                    return '<div class="badge bg-soft-success w-100">Contrôlé</div>';
                }
                return '<div class="badge bg-soft-secondary w-100">Non contrôlé</div>';
            });
            $table->rawColumns(['actions', 'is_controled']);
            $table->addColumn('selectable_td',function (){
                return'';
            });
            return  $table->make();
        }
        return view('rebuts.liste');
    }

    public function ajouter()
    {
        $this->guard_custom(['rebut.sauvegarder']);
        $o_magasins = Magasin::all();
        return view('rebuts.ajouter', compact('o_magasins'));
    }

    public function afficher($id){
        $this->guard_custom(['rebut.afficher']);
        $o_rebut = Rebut::findOrFail($id);
        $is_controled=GlobalSetting::first()->controle;
        return view('rebuts.afficher',compact('o_rebut','is_controled'));
    }

    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['rebut.sauvegarder']);

        $validator = Validator::make($request->all(), [
            'i_reference'=> 'required|max:30|unique:rebuts,reference',
            'magasin_id'=> 'required|exists:magasins,id',
            'lignes' => 'required|array',
            'lignes.*.i_article' => 'required|string|max:255',
            'lignes.*.i_article_id' => 'required|exists:articles,id',
            'lignes.*.quantite_rebut' => 'required|numeric',
        ], [
            'i_reference.required' => 'Le champ référence est obligatoire.',
            'i_reference.max' => 'La référence ne doit pas dépasser :max caractères.',
            'i_reference.unique' => 'Cette référence existe déjà.',
            'magasin_id.exists' => 'Ce magasin n\'existe pas.',
            'lignes.*.i_article' => 'Le champ article est obligatoire',
            'lignes' => 'Lignes d\'inventaire',
            'lignes.*.i_article_id' => "Le champ article est obligatoire",
            'lignes.*.quantite_rebut' => "Quantité de rebut obligatoire",
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $magasin = Magasin::findOrFail($request->get('magasin_id'));

        $rebut = new Rebut();
        $rebut->statut = "Rebut réussi";
        $rebut->date_operation = Carbon::now();
        $rebut->magasin_id = $magasin->id;
        $rebut->reference = $request->get('i_reference');
        $rebut->save();

        DB::beginTransaction();
        try {
            $lignes = $request->get('lignes', []);

            foreach ($lignes as $index => $row){
                $article = Article::where('id', $row['i_article_id'])->first();
                if ($row['quantite_rebut'] !== null) {
                    StockService::stock_sortir($article->id, $row['quantite_rebut'], Carbon::now()->format('Y-m-d'), Rebut::class, $rebut->id,$magasin->id);
                }
            }
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur inattendue s\'est produite : ' . $e->getMessage());
        }
        return redirect()->route('rebuts.afficher',$rebut->id)->with('success', "Rebut effectué avec succès.");
    }


    public function rollback($id){
        $this->guard_custom(['rebut.rollback']);
        if (\request()->ajax()) {
            DB::beginTransaction();
            try {
                $o_rebut = Rebut::findOrFail($id);
                if ($o_rebut) {
                    StockService::stock_revert(Rebut::class, $o_rebut->id);
                    $o_rebut->statut = "Rebut annulé";
                    $o_rebut->save();
                    DB::commit();
                    return response('Rebut annulé avec succès.', 200);
                } else {
                    return response('Erreur', 404);
                }
            }catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Une erreur inattendue s\'est produite : ' . $e->getMessage());
            }

        }
        abort(404);
    }

}
