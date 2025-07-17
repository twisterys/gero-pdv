<?php

namespace App\Http\Controllers;
use App\Exports\ProductExport;
use App\Exports\StocksExport;
use App\Imports\ArticlesImport;
use App\Imports\VentesImport;
use App\Models\Importation;
use App\Models\Inventaire;
use App\Models\Magasin;
use App\Models\Stock;
use App\Models\Vente;
use App\Services\FileService;
use App\Services\LimiteService;
use App\Services\ReferenceService;
use App\Services\StockService;
use App\Traits\ImportationHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Article;
use Maatwebsite\Excel\HeadingRowImport;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    use ImportationHelper;

    public function ajouter()
    {
        $this->guard_custom(['inventaire.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        $o_magasins = Magasin::all();
        return view('inventaires.ajouter',compact('o_magasins'));
    }

    public function ajouter_manuellement()
    {
        $this->guard_custom(['inventaire.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        $o_magasins = Magasin::all();
        return view('inventaires.ajouter_manuellement',compact('o_magasins'));
    }

    public function liste(){
        $this->guard_custom(['inventaire.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        if (\request()->ajax()){
            $query = Inventaire::with('magasin');
            $table = DataTables::of($query);
            $table->addColumn('actions',function ($row){
                $crudRoutePart = 'inventaire';
                $show = 'afficher';
                $rollback = 'rollback';
                $id = $row->id;
                if ($row->statut !== "Inventaire annulé") {
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
//                return view('partials.__datatable-action',compact('crudRoutePart','show','id','rollback'));
            });
            $table->addColumn('selectable_td',function (){
                return'';
            });
            return  $table->make();
        }
        return view('inventaires.liste');
    }

    public function inventaire_exporter_stocks(Request $request)
    {
        $this->guard_custom(['inventaire.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        $request->validate([
            'magasin'=>'required|exists:magasins,id',
        ]);
        $magasin = Magasin::findOrFail($request->get('magasin'));
        $magasin_id=$magasin->id;
        return Excel::download(new ProductExport($magasin_id), 'export_stocks_inventory'.$magasin->reference.'.xlsx');
    }

    public function inventaire_importer_stocks(Request $request)
    {
        $this->guard_custom(['inventaire.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'i_reference'=> 'required|max:30|unique:inventaires,reference',
            'magasin'=> 'required|exists:magasins,id',
        ], [
            'i_reference.required' => 'Le champ référence est obligatoire.',
            'i_reference.max' => 'La référence ne doit pas dépasser :max caractères.',
            'i_reference.unique' => 'Cette référence existe déjà.',
            'magasin.exists' => 'Cette référence n\'existe pas.'
        ]);
        $magasin = Magasin::findOrFail($request->get('magasin'));

        $inventaire = new Inventaire();
        $inventaire->type = $request->input('type');
        $inventaire->statut =  'Inventaire échoué';
        $inventaire->date =  Carbon::now();
        $inventaire->magasin_id = $magasin->id ;
        $inventaire->reference = $request->get('i_reference');
        $inventaire->type_inventaire = "automatique";
        if ($request->file('file')) {
            $file = $request->file('file');
            $fileName = $this->store_import_file($file);
            $inventaire->fichier_path= $fileName;
        }
        $inventaire->save();


        $headings = (new HeadingRowImport)->toArray($request->file('file'));
        $headerRow = $headings[0][0];
        $requiredHeaders = [
            "reference_article",
            "designation",
            "quantite_actuelle",
            "nouvelle_quantite",
        ];

        // Check if all required header values are present
        $missingHeaders = array_diff($requiredHeaders, $headerRow);
        $headerLabels = [
            "reference_article" => "Référence Article",
            "designation" => "Designation",
            "quantite_actuelle" => "Quantité Actuelle",
            "nouvelle_quantite" => "Nouvelle Quantité",

        ];
        // Check for missing headers
        $missingHeadersString = implode(", ", array_map(function ($header) use ($headerLabels) {
            return $headerLabels[$header];
        }, $missingHeaders));

        if (!empty($missingHeadersString)) {
            return redirect()->back()->with('error', "Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString.");
        }
        // Check for any additional unwanted headers
        $additionalHeaders = array_diff($headerRow, $requiredHeaders);
        if (!empty($additionalHeaders)) {
            $additionalHeadersString = implode(", ", $additionalHeaders);
            return redirect()->back()->with('error', "Les en-têtes suivants ne sont pas nécessaires dans le fichier Excel : $additionalHeadersString.");
        }
        $data = Excel::toArray(new ArticlesImport(), $request->file('file'));
        if (empty($data) || (count($data) === 1 && empty($data[0]))) {
            return redirect()->back()->with('error', 'Le fichier Excel est vide.');
        }
        foreach ($data[0] as $index => $row) {
            $validator = Validator::make($row, [
                'reference_article' => 'nullable|max:20|exists:articles,reference',
                'designation' => 'required_if:reference_article,null',
                'quantite_actuelle' => 'required|numeric',
                'nouvelle_quantite' => 'nullable|numeric',
            ]);
            if ($validator->fails()) {
                $rowNumber = $index + 2;
                return redirect()->back()->with('error', "Erreur de validation dans la ligne {$rowNumber}: " . $validator->errors()->first());
            }
        }
        DB::beginTransaction();
        try {
            foreach ($data[0] as $index => $row){
                if($row['reference_article']){
                    $article = Article::where('reference', $row['reference_article'])->first();
                }elseif($row['designation']){
                    $article = Article::where('designation', $row['designation'])->first();
                }
                if ($row['nouvelle_quantite'] !== null) {
                    $magasin_stock = $article->magasin_stock($magasin->id);
                    if ($row['nouvelle_quantite'] > $magasin_stock) {
                        $quantite = $row['nouvelle_quantite'] - $magasin_stock;
                        StockService::stock_entre($article->id, $quantite, Carbon::now()->format('Y-m-d'), Inventaire::class, $inventaire->id,$magasin->id);
                    } elseif ($row['nouvelle_quantite'] < $magasin_stock) {
                        $quantite = $magasin_stock - $row['nouvelle_quantite'];
                        StockService::stock_sortir($article->id, $quantite, Carbon::now()->format('Y-m-d'), Inventaire::class, $inventaire->id,$magasin->id);
                    }
                }
            }
            $inventaire->statut= 'Inventaire réussie';
            $inventaire->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur inattendue s\'est produite : ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Inventaire effectué avec succès.');

    }


    public function inventaire_importer_stocks_manuellement(Request $request)
    {
//        dd($request->all());
        $this->guard_custom(['inventaire.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }


        $validator = Validator::make($request->all(), [
            'i_reference'=> 'required|max:30|unique:inventaires,reference',
            'magasin_id'=> 'required|exists:magasins,id',
            'lignes' => 'required|array',
            'lignes.*.i_article' => 'required|string|max:255',
            'lignes.*.i_article_id' => 'required|exists:articles,id',
            'lignes.*.quantite_stock' => 'required|numeric',
            'lignes.*.quantite_new' => 'required|numeric',
        ], [
            'i_reference.required' => 'Le champ référence est obligatoire.',
            'i_reference.max' => 'La référence ne doit pas dépasser :max caractères.',
            'i_reference.unique' => 'Cette référence existe déjà.',
            'magasin_id.exists' => 'Ce magasin n\'existe pas.',
            'lignes.*.i_article' => 'Le champ article est obligatoire',
            'lignes' => 'Lignes d\'inventaire',
            'lignes.*.i_article_id' => "Le champ article est obligatoire",
            'lignes.*.quantite_stock' => "Quantité actuelle",
            'lignes.*.quantite_new' => "Nouvelle quantité est requis"
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $magasin = Magasin::findOrFail($request->get('magasin_id'));

        $inventaire = new Inventaire();
        $inventaire->type = $request->input('type');
        $inventaire->statut =  'Inventaire échoué';
        $inventaire->date =  Carbon::now();
        $inventaire->magasin_id = $magasin->id ;
        $inventaire->reference = $request->get('i_reference');
        $inventaire->fichier_path = "";
        $inventaire->type_inventaire = "manuel";
        $inventaire->save();

        DB::beginTransaction();
        try {
            $lignes = $request->get('lignes', []);

            foreach ($lignes as $index => $row){
                $article = Article::where('id', $row['i_article_id'])->first();
                if ($row['quantite_new'] !== null) {
                    $magasin_stock = $article->magasin_stock($magasin->id);
                    if ($row['quantite_new'] > $magasin_stock) {
                        $quantite = $row['quantite_new'] - $magasin_stock;
                        StockService::stock_entre($article->id, $quantite, Carbon::now()->format('Y-m-d'), Inventaire::class, $inventaire->id,$magasin->id);
                    } elseif ($row['quantite_new'] < $magasin_stock) {
                        $quantite = $magasin_stock - $row['quantite_new'];
                        StockService::stock_sortir($article->id, $quantite, Carbon::now()->format('Y-m-d'), Inventaire::class, $inventaire->id,$magasin->id);
                    }
                }
            }
            $inventaire->statut= 'Inventaire réussie';
            $inventaire->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur inattendue s\'est produite : ' . $e->getMessage());
        }
        return redirect()->route('inventaire.afficher',$inventaire->id)->with('success', "Inventaire effectué avec succès.");
    }

    public function afficher($id){
        $this->guard_custom(['inventaire.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        $o_inventaire = Inventaire::findOrFail($id);
        // add file check if null
        $file = null;
        if ($o_inventaire->fichier_path) {
            $file = route('inventaire.load', ['file' => $o_inventaire->fichier_path]);
        }

        return view('inventaires.afficher',compact('o_inventaire','file'));
    }

    public function rollback($id){
        $this->guard_custom(['inventaire.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        if (\request()->ajax()) {
            DB::beginTransaction();
            try {
                $o_inventaire = Inventaire::findOrFail($id);
                if ($o_inventaire) {
                    StockService::stock_revert(Inventaire::class, $o_inventaire->id);
                    $o_inventaire->statut = "Inventaire annulé";
                    $o_inventaire->save();
                    DB::commit();
                    return response('Inventaire annulé avec succès.', 200);
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

    public function loadFile($file)
    {
        $this->guard_custom(['inventaire.*']);
        return $this->load($file);
    }

}
