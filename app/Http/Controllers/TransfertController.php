<?php

namespace App\Http\Controllers;

use App\Http\Requests\VenteStoreRequest;
use App\Models\Article;
use App\Models\Commercial;
use App\Models\DemandeTransfert;
use App\Models\Importation;
use App\Models\GlobalSetting;
use App\Models\Magasin;
use App\Models\Taxe;
use App\Models\Transfert;
use App\Models\TransfertLigne;
use App\Models\Unite;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Services\GlobalService;
use App\Services\LimiteService;
use App\Services\LogService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class TransfertController extends Controller
{
    public function controle($id)
    {
        $this->guard_custom(['transfert.controler']);
        try {
            $transfert = Transfert::findOrFail($id);
            $transfert->update([
                'is_controled' => true,
                'controled_at' => now(),
                'controled_by' => auth()->id()
            ]);

            activity()
                ->causedBy(Auth::user())
                ->event('Contrôle')
                ->withProperties([
                    'subject_type' => Transfert::class,
                    'subject_id' => $transfert->id,
                    'subject_reference' => $transfert->reference,
                ])
                ->log('Contrôle effectué sur transfert ' . $transfert->reference);

            return response()->json([
                'success' => true,
                'message' => 'Transfert contrôlé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du contrôle du transfert: ' . $e->getMessage()
            ], 500);
        }
    }

    public function liste(Request $request){
        $this->guard_custom(['transfert_stock.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        if ($request->ajax()){
            $query = Transfert::query();
            if ($request->filled('statut_controle')) {
                if ($request->statut_controle === 'controle') {
                    $query->where('is_controled', 1);
                } elseif ($request->statut_controle === 'non_controle') {
                    $query->where('is_controled', 0);
                }
            }
            $table = DataTables::of($query);
            $table->addColumn('selectable_td',function ($row){
                return '<input type="checkbox" class="row-select form-check-input" value="' . $row->id . '">';
            })->addColumn('actions',function ($row){
                return '<button class="btn btn-sm btn-primary show-btn" data-url="'.route('transferts.afficher',$row->id).'"><i class="fa fa-eye"></i></button>';
            })->editColumn('magasin_entree',function ($row){
                return $row->magasinEntree->nom;
            })
            ->editColumn('magasin_sortie',function ($row){
                return $row->magasinSortie->nom;
            })->editColumn('created_at',function ($row){
                return Carbon::make($row->created_at)->format('d/m/Y');
            })->editColumn('is_controled', function ($row) {
                if ($row->is_controled) {
                    return '<div class="badge bg-soft-success w-100">Contrôlé</div>';
                }
                return '<div class="badge bg-soft-secondary w-100">Non contrôlé</div>';
            })->rawColumns(['selectable_td','actions','is_controled']);
            return  $table->make();
        }
        return \view('transferts.liste');
    }

    public function afficher($id){
        $this->guard_custom(['transfert_stock.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        $o_transfert = Transfert::findOrFail($id);
        $is_controled=GlobalSetting::first()->controle;
        return \view('transferts.partials.afficher',compact('o_transfert','is_controled'));
    }
    public function afficher_demande($id){
        $this->guard_custom(['transfert_stock.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }

        $o_demande = DemandeTransfert::findOrFail($id);
        return \view('transferts.demande_afficher',compact('o_demande'));
    }


    public function ajouter()
    {
        $this->guard_custom(['transfert_stock.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        $o_magasins = auth()->user()->magasins()->get();
        $o_all_magasins = Magasin::all();
        $reference = 'TRF-'.Carbon::now()->format('YmdHis');
        return view('transferts.ajouter', compact('o_magasins', 'reference','o_all_magasins'));
    }

    public function article_select_modal()
    {
        $this->guard_custom(['transfert_stock.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        $magasin_id=1;
        $type= 'ventes';
        $articles = Article::all()->take(6);
        return view('transferts.partials.modal',compact('articles','magasin_id','type'));
    }

    public function afficher_demandes(Request $request)
    {
        $this->guard_custom(['transfert_stock.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
//        $demandes = DemandeTransfert::with('lignes')->get();
        if ($request->ajax()){
            $query = DemandeTransfert::with('lignes', 'magasin_sortie', 'magasin_entree', 'user')->get();
            $table = DataTables::of($query);
            $table->addColumn('selectable_td',function ($row){
                return '<input type="checkbox" class="row-select form-check-input" value="' . $row->id . '">';
            })->addColumn('actions',function ($row){
                return '<a class="btn btn-sm btn-primary " href="'.route('transferts.afficher.demande',$row->id).'"><i class="fa fa-eye"></i></button>';
            })->editColumn('magasin_entree',function ($row){
                return $row->magasin_entree->nom;
            })
                ->editColumn('magasin_sortie',function ($row){
                    return $row->magasin_sortie->nom;
                })->editColumn('created_at',function ($row){
                    return Carbon::make($row->created_at)->format('d/m/Y');
                })->rawColumns(['selectable_td','actions']);
            return  $table->make();
        }
        return view('transferts.afficher-demandes');
    }
    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['transfert_stock.*']);
        if (!LimiteService::is_enabled('stock')){
            abort(404);
        }
        $data = [
            'reference' => $request->get('reference'),
            'magasin_entree' => $request->get('au_magasin'),
            'magasin_sortie' => $request->get('magasin-select'),
        ];

        $validator = Validator::make($request->all(), [
            'reference' => 'required|max:20|unique:transferts,reference',
            'magasin-select' => 'required|exists:magasins,id',
            'au_magasin' => 'required|exists:magasins,id',
            'lignes' => 'required|array',
            'lignes.*.i_article_id'=>'required|exists:articles,id',
            'lignes.*.i_article_reference'=>'required|exists:articles,reference',
            'lignes.*.i_article'=>'required|string|max:255',
            'lignes.*.i_quantite'=>'required|numeric|min:1'
        ],[],[
            'magasin-select'=>"du magasin ",
            'au_magasin'=>"au magasin",
            'lignes.*.i_article_id'=>'article',
            'lignes.*.i_article_reference'=>"référence d'article",
            'lignes.*.i_article'=>'article',
            'lignes.*.i_quantite'=>"quantité d'article",
        ]);
        $validator->sometimes('magasin-select', 'different:au_magasin', function ($input) {
            return isset($input['magasin_entree']) && isset($input['magasin_sortie']);
        });
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        DB::beginTransaction();
        try {
            $o_transfert = Transfert::create($data);
            $lignes = $request->get('lignes', []);
            if (count($lignes) > 0) {
                foreach ($lignes as $key => $ligne) {
                    $o_ligne = new TransfertLigne();
                    $o_ligne->article_id = $ligne['i_article_id'];
                    $o_ligne->transfert_id = $o_transfert->id;
                    $o_ligne->qte = $ligne['i_quantite'];
                    $o_ligne->save();
                    StockService::stock_entre($ligne['i_article_id'], $ligne['i_quantite'],  Carbon::now()->format('Y-m-d'), Transfert::class, $o_transfert->id,$request->get('au_magasin'));
                    StockService::stock_sortir($ligne['i_article_id'], $ligne['i_quantite'], Carbon::now()->format('Y-m-d'), Transfert::class, $o_transfert->id,$request->get('magasin-select'));
                }

            }


            DB::commit();
            return redirect()->route('transferts.liste')->with('success', 'Transfert ajouté avec succès');
        } catch (Exception $exception) {
            LogService::logException($exception);
            DB::rollBack();
            return redirect()->route('transferts.liste')->with('error', "Une erreur s'est produite lors du transfert ");
        }
    }




}
