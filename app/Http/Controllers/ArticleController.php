<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleStoreRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Models\Client;
use App\Models\GlobalSetting;
use App\Models\Marque;
use App\Models\ProduitSettings;
use App\Models\Vente;
use App\Services\GlobalService;
use App\Services\LimiteService;
use Exception;
use Carbon\Carbon;
use App\Models\Taxe;
use App\Models\Stock;
use App\Models\Unite;
use App\Models\Article;
use App\Models\Magasin;
use App\Services\LogService;
use Illuminate\Http\Request;
use App\Traits\ArticleHelper;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Monolog\Handler\IFTTTHandler;
use App\Services\ReferenceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Barryvdh\DomPDF\Facade\Pdf;

class ArticleController extends Controller
{
    use ArticleHelper;
    /**
     * Display a listing of the resource.
     */
    public function liste(Request $request)
    {
        $this->guard_custom(['article.liste']);
        if ($request->ajax()) {
            $query = Article::with('famille', 'unite', 'stock');
            if( $request->get('code_barre')){
                $query->where('code_barre', $request->get('code_barre'));
            }
            if ($request->get('famille_id')) {
                $query->where('famille_id', $request->get('famille_id'));
            }
            if ($request->get('date')) {
                $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date'))[0]))->toDateString();
                $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date'))[1]))->toDateString();
                if ($end_date === $start_date) {
                    $query->whereDate('created_at', $end_date);
                } else {
                    $query->whereBetween('created_at', [$start_date, $end_date]);
                }
            }
            if ($request->get('reference')) {
                $query->where('reference', $request->get('reference'));
            }
            if ($request->get('designation')) {
                $designation_search = '%' . $request->get('designation') . '%';
                $query->where('designation', 'LIKE', $designation_search);
            }
            if ($request->get('prix_vente')) {
                $query->where('prix_vente', +$request->get('prix_vente'));
            }
            if ($request->get('prix_achat')) {
                $query->where('prix_achat', +$request->get('prix_achat'));
            }
            if ($request->get('prix_revient')) {
                $query->where('prix_revient', +$request->get('prix_revient'));
            }
            $table = DataTables::of($query);
            $table->addColumn(
                'selectable_td',
                function ($row) {
                    $id = $row['id'];
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            )->addColumn('quantite', function ($row) {
                return $row->quantite;
            });
            $table->addColumn('actions', function ($row) {
                $crudRoutePart = 'articles';
                $show = 'afficher';
                $delete = 'supprimer';
                $edit = 'modifier';
                $id = $row->id;
                return view('partials.__datatable-action', compact('id', 'crudRoutePart', 'edit', 'delete', 'show'));
            });
            $table->editColumn('unite_id', function ($row) {
                return $row->unite->nom;
            });
            $table->editColumn('famille_id', function ($row) {
                if (!$row->famille) {
                    return '---';
                }
                return '<div class="badge" style="background-color:' . $row->famille['couleur'] . '">' . $row->famille["nom"] . '</div>';
            });
            $table->editColumn('prix_vente', function ($row) {
                return number_format($row->prix_vente, 3, ',', '') . ' MAD';
            });
            $table->editColumn('prix_achat', function ($row) {
                return number_format($row->prix_achat, 3, ',', '') . ' MAD';
            });
            $table->editColumn('prix_revient', function ($row) {
                return number_format($row->prix_revient, 3, ',', '') . ' MAD';
            });
            $table->editColumn('created_at', function ($row) {
                return Carbon::make($row->created_at)->toDateString();
            });
            $table->rawColumns(['selectable_td', 'famille_id', 'actions']);
            return $table->make();
        }
        $stock_limite = LimiteService::is_enabled('stock');
        $is_code_barre = GlobalSetting::first()->code_barre != 0;
        return view('articles.liste',compact('stock_limite','is_code_barre'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function ajouter()
    {
        $this->guard_custom(['article.sauvegarder']);
        $article_reference = $this->generer_reference(Carbon::now());
        $taxes = Taxe::all()->toArray();
        $unites = Unite::all()->toArray();
        $marques = Marque::all();
        $produit_settings = ProduitSettings::all();
        $marque = $produit_settings->where('key','marque')->first()->value ?? false;
        $image = $produit_settings->where('key','image')->first()->value ?? false;
        $numero_serie = $produit_settings->where('key','numero_serie')->first()->value ?? false;
        $globals = GlobalService::get_all_globals();
        $is_code_barre = GlobalSetting::first()->code_barre != 0;

        if (\request()->ajax()) {
            return view('articles.partials.ajout-rapide-modal', compact('unites', 'article_reference', 'taxes','is_code_barre'));
        }
        return view('articles.ajouter', compact('taxes', 'unites', 'article_reference','marques','marque','image','numero_serie','globals'));
    }

    public function afficher($id)
    {
        $this->guard_custom(['article.afficher']);
        $o_article = Article::findOrFail($id);
        $globals = GlobalService::get_all_globals();



        $magasins = Magasin::leftJoin('transaction_stocks', function ($join) use ($id) {
            $join->on('magasins.id', '=', 'transaction_stocks.magasin_id')
                ->where('transaction_stocks.article_id', '=', $id);
        })
            ->select([
                'magasins.id', 'magasins.reference', 'magasins.nom', \DB::raw('COALESCE(SUM(transaction_stocks.qte_entree) - SUM(transaction_stocks.qte_sortir), 0) as quantite'),
                \DB::raw('COALESCE(SUM(CASE WHEN transaction_stocks.declencheur = \'Vente\' THEN transaction_stocks.qte_sortir ELSE 0 END), 0) AS qte_vente'),
                \DB::raw('COALESCE(SUM(CASE WHEN transaction_stocks.declencheur = \'Vente\' THEN transaction_stocks.qte_entree ELSE 0 END), 0) AS qte_retour'),
                \DB::raw('COALESCE(SUM(CASE WHEN transaction_stocks.declencheur = \'Achat\' THEN transaction_stocks.qte_sortir ELSE 0 END), 0) AS qte_retour_achat'),
                \DB::raw('COALESCE(SUM(CASE WHEN transaction_stocks.declencheur = \'Achat\' THEN transaction_stocks.qte_entree ELSE 0 END), 0) AS qte_achat')
            ])
            ->groupBy('magasins.id')
            ->get();


        $codeBarreHtml = null;

// Générer le code-barre seulement si l'article a un code-barre
        if (!empty($o_article->code_barre)) {
            $codeBarreHtml = $this->genererCodeBarre($o_article->code_barre);
        }


        $globals = GlobalService::get_all_globals();
        return view('articles.afficher', compact('o_article', 'magasins','codeBarreHtml','globals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function sauvegarder(ArticleStoreRequest $request)
    {
        $this->guard_custom(['article.sauvegarder']);
        try {
            DB::beginTransaction();
            $o_article = new Article();
            $o_article->reference = $request->get('i_reference');
            $o_article->designation = $request->get('i_designation');
            $o_article->unite_id = $request->get('i_unite');
            $o_article->famille_id = $request->get('i_famille');
            $o_article->description = $request->get('description');
            $o_article->taxe = $request->get('i_taxe');
            $o_article->prix_vente = $request->get('i_vente_prix');
            $o_article->prix_achat = $request->get('i_achat_prix');
            $o_article->prix_revient = $request->get('i_revient_prix');
            $o_article->quantite_alerte = $request->get('i_quantite_alerte');
            $o_article->stockable = $request->get('i_stockable') ?? '0';
            $o_article->marque_id = $request->get('i_marque_id') ?? null;
            $o_article->numero_serie = $request->get('i_numero_serie') ?? null;
            $o_article->code_barre = $request->get('i_code_barre');

            if ($request->file('i_image')) {
                $file = $request->file('i_image');
                $fileName = $this->store_article_image($file);
                $o_article->image = $fileName;
            }
            $o_article->save();
            ReferenceService::incrementCompteur('art');
            if ($o_article->stockable && LimiteService::is_enabled('stock') ){
                Stock::create([
                    'article_id' => $o_article->id,
                    'quantite' => 0
                ]);
            }
            DB::commit();
            if ($request->ajax()) {
                return response($o_article, 200);
            }
            session()->flash('success', 'Article ajouté');
            return redirect()->route('articles.liste');
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', 'Une erreur est produit');
            return redirect()->route('articles.liste');
        }
    }



    public function genererCodeBarre($code)
    {
        try {
            $generator = new BarcodeGeneratorHTML();
            return $generator->getBarcode($code, $generator::TYPE_CODE_128);
        } catch (Exception $e) {
            Log::error("Erreur lors de la génération du code-barre : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Display the specified resource.
     */
    public function imprimerCodeBarre($code)
    {
        $this->guard_custom(['article.afficher']);
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcode = base64_encode($generator->getBarcode($code, $generator::TYPE_CODE_128));

            $pdf = Pdf::loadView('articles.pdf-code-barre', compact('barcode', 'code'));

            return $pdf->download('code-barre-' . $code . '.pdf');
        } catch (Exception $e) {
            Log::error("Erreur lors de la génération du code-barre : " . $e->getMessage());
            return null;
        }
    }




    public function voir(Article $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifier(int $id)
    {
        $this->guard_custom(['article.mettre_a_jour']);
        $o_article = Article::with('famille', 'unite', 'taxe')->find($id);
        if (!$o_article) {
            abort(404);
        }
        $taxes = Taxe::all();
        $unites = Unite::all()->toArray();
        $marques = Marque::all();
        $produit_settings = ProduitSettings::all();
        $marque = $produit_settings->where('key','marque')->first()->value ?? false;
        $image = $produit_settings->where('key','image')->first()->value ?? false;

        //        if ($o_article['image']) {
        //            $o_article['image'] = $this->get_image_url($o_article['image']);
        //        }
        $numero_serie = $produit_settings->where('key','numero_serie')->first()->value ?? false;

        $modifier_reference =  GlobalService::get_modifier_reference();
        $globals = GlobalService::get_all_globals();
        return view('articles.modifier', compact('taxes', 'unites', 'o_article','modifier_reference','marques','marque','image','numero_serie','globals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function mettre_a_jour(ArticleUpdateRequest $request, int $id)
    {
        $this->guard_custom(['article.mettre_a_jour']);
        $o_article = Article::with('famille', 'unite', 'taxe')->find($id);
        $modifier_reference =  GlobalService::get_modifier_reference();

        if (!$o_article) {
            abort(404);
        }
        try {
            DB::beginTransaction();
            $o_article->designation = $request->get('i_designation');
            $o_article->reference =  $modifier_reference ? $request->get('i_reference') : $o_article->reference;
            $o_article->unite_id = $request->get('i_unite');
            $o_article->famille_id = $request->get('i_famille');
            $o_article->description = $request->get('description');
            $o_article->taxe = $request->get('i_taxe');
            $o_article->prix_vente = $request->get('i_vente_prix');
            $o_article->prix_achat = $request->get('i_achat_prix');
            $o_article->prix_revient = $request->get('i_revient_prix');
            $o_article->quantite_alerte = $request->get('i_quantite_alerte');
            $o_article->stockable = $request->get('i_stockable') ?? '0';
            $o_article->marque_id = $request->get('i_marque_id') ?? null;
            $o_article->numero_serie = $request->get('i_numero_serie') ?? null;
            $o_article->code_barre = $request->get('i_code_barre');
            if ($request->file('i_image')) {
                $file = $request->file('i_image');
                $fileName = $this->store_article_image($file);
                $o_article->image = $fileName;
            } elseif ($request->get('i_supprimer_image') === '1') {
                $o_article->image = null;
            }
            $o_article->save();
            DB::commit();
            session()->flash('success', 'Article mise à jour');
            return redirect()->route('articles.liste');
        } catch (Exception $exception) {
            DB::rollBack();
            LogService::logException($exception);
            session()->flash('error', 'Une erreur est produit');
            return redirect()->route('articles.liste');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function supprimer($id)
    {
        $this->guard_custom(['article.supprimer']);
        if (\request()->ajax()) {
            $i_famille = Article::find($id);
            if ($i_famille) {
                $i_famille->delete();
                return response('Article supprimé', 200);
            } else {
                return response('Erreur', 404);
            }
        }
    }

    public function modal_recherche(Request $request, $type)
    {
        $this->guard_custom(['article.liste']);
        if ($request->ajax()) {
            $nom = $request->get('search');
            $magasin_id = $request->get('magasin_id');
            $articles = Article::where('reference', 'like', "%$nom%")->orWhere('designation', 'like', "%$nom%")->orWhere('code_barre',$nom)->orderbyRAW('LENGTH(reference)')->take(6)->get();
            return view('articles.partials.modal_content', compact('articles', 'type', 'magasin_id'));
        }
    }
    public function article_select_modal(Request $request,$type, $magasin_id )
    {
        $this->guard_custom(['article.liste']);
        if ($request->ajax()) {
            if ($type === "inventaire"){
                $articles = Article::with('stock')->where("stockable", "=", "1")->take(6)->get();
            }else{
                $articles = Article::all()->take(6);
            }
            if ($magasin_id === "null") {
                return response('l\'tilisateur n\'a aucun magasin affecté ', 404);
            }
            $magasins = \request()->user()->magasins()->get();
            return view('articles.partials.modal', compact('articles', 'type', 'magasins', 'magasin_id'));
        }
    }
    function generer_reference($date = null)
    {
        return ReferenceService::generateReference('art', $date);
    }

    public function load_article_image($file)
    {
        $this->guard_custom(['article.liste']);
        return $this->load($file);
    }

    public function article_select(Request $request)
    {
        $this->guard_custom(['article.liste']);
        if ($request->ajax()) {
            $search = '%' . $request->get('term') . '%';
            $data = Article::where('designation', 'LIKE', $search)->get(['id', 'designation as text']);
            return response()->json($data, 200);
        }
        abort(404);
    }

    public function afficher_ajax(Request $request, $id)
    {
        $this->guard_custom(['article.afficher']);
        $o_article = Article::find($id);
        if ($request->ajax()){
            if (!$o_article) {
                return response()->json('',404);
            }
            return response()->json($o_article,200);
        }
        if (!$o_article) {
            return redirect()->back()->with('error', "Article n'existe pas");
        }
    }


    public function historique_prix_modal(Request $request){
        $this->guard_custom(['article.afficher']);
        \Validator::make($request->all(),[
            'client_id'=>'required|exists:clients,id',
            'article_id'=>'required|exists:articles,id'
        ],[],[
            'client_id'=>'client',
            'article_id'=>'article'
        ])->validate();
        $o_article = Article::findOrFail($request->input('article_id'));
        $ventes =  Vente::join('vente_lignes','vente_id','=','ventes.id')->where('article_id',$request->article_id)->where('client_id',$request->client_id)->get(['date_document','ht','ventes.reference']);
        return view('articles.partials.historique_prix',compact('o_article','ventes'));
    }
}
