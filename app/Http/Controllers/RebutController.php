<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Magasin;
use App\Models\Rebut;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RebutController extends Controller
{

    public function liste(){
        $this->guard_custom(['rebut.*']);
        if (\request()->ajax()){
            $query = Rebut::with('magasin');
            $table = DataTables::of($query);
            $table->addColumn('actions',function ($row){
                $crudRoutePart = 'rebuts';
                $show = 'afficher';
                $id = $row->id;
                return view(
                    'partials.__datatable-action',
                    compact(
                        'crudRoutePart',
                        'show',
                        'id',
                    )
                );

            });
            $table->editColumn('date_operation', function ($row) {
                return \Carbon\Carbon::make($row->date_operation)->format('d/m/Y');
            });
            $table->rawColumns(['actions']);
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
        return view('rebuts.afficher',compact('o_rebut'));
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

}
