<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Article;
use App\Models\Depense;
use App\Models\Importation;
use App\Models\Inventaire;
use App\Models\Magasin;
use App\Models\Paiement;
use App\Models\PosSession;
use App\Models\PosSettings;
use App\Models\Rapport;
use App\Models\Tag;
use App\Models\TransactionStock;
use App\Models\Transfert;
use App\Models\Vente;
use App\Services\PosService;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yajra\DataTables\Facades\DataTables;
/*
 * rapports pour Gero PDV
 */
class RapportController extends Controller
{
    public function liste()
    {
        $this->guard_custom(['rapport.*']);
        $achat_vente = Rapport::where('type','achat-vente')->get();
        $stock = Rapport::where('type','stock')->get();
        $statistiques = Rapport::where('type','statistiques')->get();
        $comptabilité = Rapport::where('type','comptabilité')->get();
        $pos = Rapport::where('type','pos')->get();
        return view('rapports.liste',compact('achat_vente','statistiques','stock','comptabilité','pos'));
    }

    public function mouvement_stock(Request $request)
    {
        $this->guard_custom(['rapport.*']);

        $exercice_date = session()->get('exercice');
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');
        if ($request->ajax()) {
            $o_transactions = TransactionStock::with('article', 'stockable');
            if ($request->has('i_search')) {
                $selected_reference = $request->get('i_search');
                $o_transactions->whereHas('article', function ($query) use ($selected_reference) {
                    $query->where('reference', 'like', '%' . $selected_reference . '%');
                });
            }
            if ($request->has('i_date')) {
                $selectedDateRange = $request->get('i_date');
                $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->toDateString();
                $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->toDateString();
                $range = [$start_date, $end_date];
                $o_transactions->whereBetween('created_at', $range);
            }
            if ($request->get('i_ref')) {
                $inventaire_reference = $request->get('i_ref');
                $inventaire = Inventaire::where('reference', $inventaire_reference)->first();
                $o_transactions->where('stockable_id', $inventaire->id)
                    ->where('stockable_type', Inventaire::class);
            }
            if ($request->get('i_imp')) {
                $importation_reference = $request->get('i_imp');
                $importation = Importation::where('reference', $importation_reference)->first();
                $o_transactions->where('stockable_id', $importation->id)
                    ->where('stockable_type', Importation::class);
            }
            if ($request->get('order') && $request->get('columns') ){
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order){
                    $o_transactions->orderByRaw(''.$columns[$order['column']]['data'].' '.$order['dir']);
                }
            }
            $table = DataTables::of($o_transactions)->order(function (){})
                ->addColumn('stockable_type', function ($row) {
                    $route = null;
                    if ($row->stockable_type === Achat::class) {
                        $route = route('achats.afficher', ['type' => $row->stockable->type_document, 'id' => $row->stockable->id]);
                    } elseif ($row->stockable_type === Vente::class) {
                        $route = route('ventes.afficher', ['type' => $row->stockable->type_document, 'id' => $row->stockable->id]);
                    } elseif ($row->stockable_type === Inventaire::class) {
                        return '<a href="javascript:void(0);" class="i_ref text-info text-decoration-underline" data-reference="' . $row->stockable->reference . '" data-type="inventaire">' . $row->stockable->reference . '</a>';
                    } elseif ($row->stockable_type === Importation::class) {
                        return '<a href="javascript:void(0);" class="i_imp text-info text-decoration-underline" data-reference="' . $row->stockable->reference . '" data-type="importation">' . $row->stockable->reference . '</a>';
                    }
                    return '<a class="text-info text-decoration-underline" href="' . $route . '">' . $row->stockable->reference . '</a>';
                })
                ->rawColumns(['stockable_type'])
                ->addColumn('mouvement', function ($row) {
                    return $row->stockable_type::DECLENCHEUR;
                })
                ->addColumn('magasin', function ($row) {
                    return $row->magasin ? $row->magasin->reference : '--';
                });

            return $table->make();
        }
        $rapport_details = Rapport::where('route','mouvement-stock')->first();
        return view("rapports.mouvement_stock", compact('date_picker_start', 'date_picker_end','rapport_details'));
    }


    public function achat_vente(Request $request)
    {
        $this->guard_custom(['rapport.*']);

        // Get the type from the request route
        $exercice_date = session()->get('exercice');

        // Set default date range for the current year
        $range = [Carbon::now()->setYear($exercice_date)->firstOfYear(), Carbon::now()->setYear($exercice_date)->lastOfYear()];
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');

        // If a date range is provided in the request, update the range and start/end dates
        if ($request->has('i_date')) {
            $selectedDateRange = $request->get('i_date');
            $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->toDateString();
            $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->toDateString();
            $date_picker_start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->format('d/m/Y');
            $date_picker_end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->format('d/m/Y');
            $range = [$start_date, $end_date];
        }
        $types_achat = ['faa'];
        $types_vente = ['fa'];
        $retours_achat = ['bra'];
        $retours_vente = ['br'];
        $balises_vente = [];
        $balises_achat = [];

        if ($request->get('types_achat')) {
            $types_achat = $request->get('types_achat');
        }
        if ($request->get('retours_achat')) {
            $retours_achat = $request->get('retours_achat');
        }
        if ($request->get('types_vente')) {
            $types_vente = $request->get('types_vente');
        }
        if ($request->get('retours_vente')) {
            $retours_vente = $request->get('retours_vente');
        }
        if ($request->get('balises_vente')) {
            $balises_vente = $request->get('balises_vente');
        }
        if ($request->get('balises_achat')) {
            $balises_achat = $request->get('balises_achat');
        }

        if ($balises_achat) {
            $totalAva = DB::table('taggables')->join('achats', function (JoinClause $joinClause) {
                $joinClause->where('taggable_type', '=', Achat::class)->on('taggables.taggable_id', '=', 'achats.id');
            })->whereIn('type_document', $retours_achat)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('tag_id', $balises_achat)->sum('total_ttc');


            $totalHT = DB::table('taggables')->join('achats', function (JoinClause $joinClause) {
                $joinClause->where('taggable_type', '=', Achat::class)->on('taggables.taggable_id', '=', 'achats.id');
            })->whereIn('type_document', $types_achat)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('tag_id', $balises_achat)->sum('total_ht');


            $totalCredit = DB::table('taggables')->join('achats', function (JoinClause $joinClause) {
                $joinClause->where('taggable_type', '=', Achat::class)->on('taggables.taggable_id', '=', 'achats.id');
            })->whereIn('type_document', $types_achat)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('tag_id', $balises_achat)->sum('credit');


            $totalTTC = DB::table('taggables')->join('achats', function (JoinClause $joinClause) {
                $joinClause->where('taggable_type', '=', Achat::class)->on('taggables.taggable_id', '=', 'achats.id');
            })->whereIn('type_document', $types_achat)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('tag_id', $balises_achat)->sum('total_ttc');
        } else {

            $totalAva = Achat::query()
                ->whereIn('type_document', $retours_achat)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->with('tags')
                ->sum('total_ttc');


            $totalHT = Achat::query()
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('type_document', $types_achat)
                ->sum('total_ht');


            $totalCredit = Achat::query()
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('type_document', $types_achat)
                ->sum('credit');


            $totalTTC = Achat::query()
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('type_document', $types_achat)
                ->sum('total_ttc');
        }

        if ($balises_vente) {
            $totalAv = DB::table('taggables')->join('ventes', function (JoinClause $joinClause) {
                $joinClause->where('taggable_type', '=', Vente::class)->on('taggables.taggable_id', '=', 'ventes.id');
            })->whereIn('type_document', $retours_vente)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('tag_id', $balises_vente)->sum('total_ttc');

            $ventestotalHT = DB::table('taggables')->join('ventes', function (JoinClause $joinClause) {
                $joinClause->where('taggable_type', '=', Vente::class)->on('taggables.taggable_id', '=', 'ventes.id');
            })->whereIn('type_document', $types_vente)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('tag_id', $balises_vente)->sum('total_ht');

            $ventetotalEncaisser = DB::table('taggables')->join('ventes', function (JoinClause $joinClause) {
                $joinClause->where('taggable_type', '=', Vente::class)->on('taggables.taggable_id', '=', 'ventes.id');
            })->whereIn('type_document', $types_vente)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('tag_id', $balises_vente)->sum('encaisser');

            $ventestotalTTC = DB::table('taggables')->join('ventes', function (JoinClause $joinClause) {
                $joinClause->where('taggable_type', '=', Vente::class)->on('taggables.taggable_id', '=', 'ventes.id');
            })->whereIn('type_document', $types_vente)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('tag_id', $balises_vente)->sum('total_ttc');
        } else {
            $totalAv = Vente::query()
                ->whereIn('type_document', $retours_vente)
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })
                ->sum('total_ttc');

            $ventestotalHT = Vente::query()
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('type_document', $types_vente)
                ->sum('total_ht');

            $ventetotalEncaisser = Vente::query()
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('type_document', $types_vente)
                ->sum('encaisser');

            $ventestotalTTC = Vente::query()
                ->whereYear('date_emission', $exercice_date)
                ->when($range, function ($query) use ($range) {
                    $query->whereBetween('date_emission', $range);
                })->whereIn('type_document', $types_vente)
                ->sum('total_ttc');
        }
        $depense_total_ttc = Depense::whereYear('date_operation', $exercice_date)
            ->when($range, function ($query) use ($range) {
                $query->whereBetween('date_operation', $range);
            })->sum('montant');

        $recap = $ventestotalTTC - $totalAv - $totalTTC + $totalAva - $depense_total_ttc;
        if ($request->ajax()) {
            return [
                'vente' => view('rapports.partials.achat_vente.vente', compact('ventestotalTTC', 'ventestotalHT', 'ventetotalEncaisser', 'totalAv'))->render(),
                'achat' => view('rapports.partials.achat_vente.achat', compact('totalTTC', 'totalHT', 'totalCredit', 'totalAva'))->render(),
                'recap' => view('rapports.partials.achat_vente.recap', compact('recap'))->render(),
                'depense' => view('rapports.partials.achat_vente.depense', compact('depense_total_ttc'))->render()
            ];
        }
        // Pass the filtered data and totals to the view
        $ventes_types = Vente::TYPES;
        $achats_types = Achat::TYPES;
        $balises = Tag::all();
        $rapport_details = Rapport::where('route','achat_vente')->first();
        return view("rapports.achat_vente", compact('recap', 'balises', 'depense_total_ttc', 'ventes_types', 'achats_types', 'retours_achat', 'retours_vente', 'types_vente', 'types_achat', 'totalCredit', 'ventetotalEncaisser', 'date_picker_start', 'date_picker_end', 'totalTTC', 'ventestotalTTC', 'totalHT', 'ventestotalHT', 'totalAva', 'totalAv','rapport_details'));
    }

    public function vente_produit(Request $request)
    {
        $this->guard_custom(['rapport.*']);
        if ($request->ajax()) {
            $query = DB::table('ventes')
                ->join('vente_lignes', 'ventes.id', '=', 'vente_lignes.vente_id')
                ->join('articles', 'vente_lignes.article_id', '=', 'articles.id')
                ->where('statut', 'validé')
                ->select('ventes.id as id', 'ventes.reference as reference_vente', 'vente_lignes.total_ttc as total', 'articles.designation as designation',
                    'type_document', 'articles.reference as sku',
                    'vente_lignes.quantite as quantite', 'ventes.date_document as date_document');

            if ($request->get('i_types')) {
                $types_inclues = $request->get('i_types');
            }
            $query->whereIn('type_document', $types_inclues);
            if ($request->get('i_search')) {
                $selected_reference = $request->get('i_search');
                $query->where('articles.reference', '=', $selected_reference)->orWhere('articles.designation', 'like', '%' . $selected_reference . '%');
            }
            if ($request->has('i_date')) {
                $selectedDateRange = $request->get('i_date');
                $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->toDateString();
                $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->toDateString();
                $range = [$start_date, $end_date];
                $query->whereBetween('ventes.date_document', $range);
            }
            if ($request->get('order') && $request->get('columns') ){
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order){
                    $query->orderByRaw(''.$columns[$order['column']]['data'].' '.$order['dir']);
                }
            }
            $query->orderBy('ventes.updated_at', 'desc');
            $table = DataTables::of($query)->order(function (){});
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->editColumn('type_document', function ($row) {
                return __('ventes.' . $row->type_document);
            });
            $table->rawColumns(['selectable_td']);
            return $table->make();
        }
        $exercice_date = session()->get('exercice');
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');

        $types_inclues = ['bc', 'br'];
        $types = Vente::TYPES;
        $rapport_details = Rapport::where('route','vente-produit')->first();
        return view("rapports.vente_produit", compact('date_picker_start', 'date_picker_end', 'types', 'types_inclues','rapport_details'));
    }

    public function achat_produit(Request $request)
    {
        $this->guard_custom(['rapport.*']);
        if ($request->ajax()) {

            $query = DB::table('achats')
                ->join('achat_lignes', 'achats.id', '=', 'achat_lignes.achat_id')
                ->join('articles', 'achat_lignes.article_id', '=', 'articles.id')
                ->where('achats.statut', 'validé')
                ->select('achats.id as id', 'achats.reference as reference_vente', 'achat_lignes.total_ttc as total',
                    'type_document', 'articles.reference as sku', 'articles.designation',
                    'achat_lignes.quantite as quantite', 'achats.date_emission as date_document');

            if ($request->get('i_types')) {
                $types_inclues = $request->get('i_types');
            }
            $query->whereIn('type_document', $types_inclues);

            if ($request->get('i_search')) {
                $selected_reference = $request->get('i_search');
                $query->where('articles.reference', '=', $selected_reference)->orWhere('articles.designation', 'like', '%' . $selected_reference . '%');
            }

            if ($request->has('i_date')) {
                $selectedDateRange = $request->get('i_date');
                $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->toDateString();
                $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->toDateString();
                $range = [$start_date, $end_date];
                $query->whereBetween('achats.date_emission', $range);
            }
            if ($request->get('order') && $request->get('columns') ){
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order){
                    $query->orderByRaw(''.$columns[$order['column']]['data'].' '.$order['dir']);
                }
            }
            $query->orderBy('achats.updated_at', 'desc');
            $table = DataTables::of($query)->order(function (){});
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->editColumn('type_document', function ($row) {
                return __('achats.' . $row->type_document);
            });
            $table->rawColumns(['selectable_td']);
            return $table->make();


        }
        $exercice_date = session()->get('exercice');
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');

        $types_inclues = ['bra', 'faa'];
        $types = Achat::TYPES;
        $rapport_details = Rapport::where('route','achat-produit')->first();
        return view("rapports.achat_produit", compact('date_picker_start', 'date_picker_end', 'types', 'types_inclues','rapport_details'));
    }

    public function ca_client(Request $request)
    {
        $this->guard_custom(['rapport.*']);

        $exercice_date = session()->get('exercice');
        $range = [Carbon::now()->setYear($exercice_date)->firstOfYear(), Carbon::now()->setYear($exercice_date)->lastOfYear()];
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');

        $types_inclue = ['fa'];
        if ($request->ajax()) {
            if ($request->get('i_types')) {
                $types_inclue = $request->get('i_types');
            }
            $query = DB::table('clients')->select(
                ['clients.nom',
                    'clients.id as id',
                    DB::raw('COALESCE(SUM(ventes.solde),0) AS total_solde'),
                    DB::raw('COALESCE(SUM(ventes.encaisser),0) AS total_encaisser'),
                    DB::raw('COALESCE(SUM(ventes.total_ttc),0) AS total_ttc')]
            )->leftJoin('ventes', function ($join) use ($types_inclue, $request) {
                $join->on('clients.id', '=', 'ventes.client_id')
                    ->where('ventes.statut', 'validé')
                    ->whereIn('ventes.type_document', $types_inclue);
                if ($request->has('i_date')) {
                    $selectedDateRange = $request->get('i_date');
                    $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->toDateString();
                    $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->toDateString();
                    $range = [$start_date, $end_date];
                    $join->whereBetween('ventes.date_emission', $range);
                }
            })->groupBy('clients.id', 'clients.nom');

            if ($request->get('i_search')) {
                $search = '%' . $request->get('i_search') . '%';
                $query->where(function (Builder $query) use ($search) {
                    $query->where('clients.nom', 'LIKE', $search)->orWhere('clients.reference', 'LIKE', $search);
                });
            }
            if ($request->get('order') && $request->get('columns')) {
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order) {
                    $query->orderByRaw('' . $columns[$order['column']]['data'] . ' ' . $order['dir']);
                }
            }
            $table = DataTables::of($query)->order(function () {
            });
            $table->addColumn('total_ttc', function ($row) {
                return $row->total_ttc . ' MAD';
            });
            $table->editColumn('total_solde', function ($row) {
                return $row->total_solde . ' MAD';
            });
            $table->editColumn('total_encaisser', function ($row) {
                return $row->total_encaisser . ' MAD';
            });
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->rawColumns(['selectable_td']);
            return $table->make();
        }
        $types = Vente::TYPES;
        $rapport_details = Rapport::where('route','ca-client')->first();
        return view("rapports.ca_client", compact('date_picker_start', 'date_picker_end', 'types', 'types_inclue','rapport_details'));
    }

    public function tendance_produit(Request $request)
    {
        $this->guard_custom(['rapport.*']);

        $exercice_date = session()->get('exercice');
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');

        $types_inclus = ["fa"];
        if ($request->ajax()) {
            $query = DB::table('articles')->leftJoin('vente_lignes', function (JoinClause $join) use ($types_inclus, $request) {
                if ($request->get('i_types')) {
                    $types_inclus = $request->get('i_types');
                }
                $join->on('articles.id', 'vente_lignes.article_id')
                    ->leftJoin('ventes', 'vente_id', '=', 'ventes.id')
                    ->whereIn('type_document', $types_inclus)
                    ->where('statut', 'validé');
                if ($request->has('i_date')) {
                    $selectedDateRange = $request->get('i_date');
                    $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->toDateString();
                    $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->toDateString();
                    $range = [$start_date, $end_date];
                    $join->whereBetween('ventes.date_document', $range);
                }
            })->selectRaw("articles.reference as reference ,
             articles.id as id,articles.designation as article,
            COALESCE(SUM(vente_lignes.total_ttc),0) as total_des_ventes ,
             COALESCE(SUM(vente_lignes.quantite),0) as nombre_des_ventes")
                ->groupBy('articles.id');

            if ($request->get('i_search')) {
                $search = '%' . $request->get('i_search') . '%';
                $query->where(function (Builder $query) use ($search) {
                    $query->where('articles.reference', 'LIKE', $search)
                        ->orWhere('articles.designation', 'LIKE', $search);
                });
            }


            $table = DataTables::of($query);
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->addColumn('total_des_ventes', function ($row) {
                return $row->total_des_ventes . ' MAD';
            });
            $table->rawColumns(['selectable_td']);

            return $table->make();
        }
        $types = Vente::TYPES;
        $rapport_details = Rapport::where('route','tendance-produit')->first();
        return view("rapports.tendance_produit", compact('date_picker_start', 'date_picker_end', 'types', 'types_inclus','rapport_details'));
    }

    public function stock_produit(Request $request)
    {
        $this->guard_custom(['rapport.*']);
        if ($request->ajax()) {
            $query = DB::table('articles as a')
                ->leftJoin('stocks as s', 'a.id', '=', 's.article_id')
                ->select(
                    'a.reference',
                    'a.designation',
                    'a.id as id',
                    'a.prix_vente',
                    'a.prix_achat',
                    DB::raw('s.quantite AS quantite')
                );
            if ($request->get('order') && $request->get('columns') ){
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order){
                    $query->orderByRaw(''.$columns[$order['column']]['data'].' '.$order['dir']);
                }
            }
            $table = DataTables::of($query)->order(function (){});
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->addColumn('valeur_achats', function ($row) {
                return $row->quantite * $row->prix_achat;
            });
            $table->addColumn('valeur_ventes', function ($row) {
                return $row->quantite * $row->prix_vente;
            });
            $table->addColumn('bénéfice_potentiel', function ($row) {
                return ($row->quantite * $row->prix_vente) - ($row->quantite * $row->prix_achat);
            });
            $table->rawColumns(['selectable_td']);
            return $table->make();
        }
        $stock_ventes = DB::table('articles')->join('stocks', 'stocks.article_id', '=', 'articles.id')
            ->selectRaw('SUM(stocks.quantite * prix_vente ) as somme_vente')->pluck('somme_vente')[0];
        $stock_achats = DB::table('articles')->join('stocks', 'stocks.article_id', '=', 'articles.id')
            ->selectRaw('SUM(stocks.quantite * prix_achat ) as somme_achat')->pluck('somme_achat')[0];
        $benifice = $stock_ventes - $stock_achats;
        $profit = $stock_ventes == 0 ? 0 : number_format((($benifice / $stock_ventes) * 100), 3);
        $rapport_details = Rapport::where('route','stock-produit')->first();
        return view("rapports.stock_produit", compact('stock_achats', 'stock_ventes', 'benifice', 'profit','rapport_details'));
    }
    public function stock_produit_legal(Request $request){

        $this->guard_custom(['rapport.*']);
        if ($request->ajax()) {
            $stock_vente_subquery = '(SELECT COALESCE(SUM(vl.quantite), 0)
            FROM vente_lignes as vl
            JOIN ventes as v ON vl.vente_id = v.id
            WHERE vl.article_id = a.id
            AND v.type_document = "fa"
            AND v.statut = "validé") +
            (SELECT COALESCE(SUM(al.quantite), 0)
            FROM achat_lignes as al
            JOIN achats as ac ON al.achat_id = ac.id
            WHERE al.article_id = a.id
            AND ac.type_document = "ava"
            AND ac.statut = "validé")';

            $stock_achat_subquery = '(SELECT COALESCE(SUM(al.quantite), 0)
            FROM achat_lignes as al
            JOIN achats as ac ON al.achat_id = ac.id
            WHERE al.article_id = a.id
            AND ac.type_document = "faa"
            AND ac.statut = "validé") +
            (SELECT COALESCE(SUM(vl.quantite), 0)
            FROM vente_lignes as vl
            JOIN ventes as v ON vl.vente_id = v.id
            WHERE vl.article_id = a.id
            AND v.type_document = "av"
            AND v.statut = "validé")';

            $query = DB::table('articles as a')
                ->leftJoin('stocks as s', 'a.id', '=', 's.article_id')
                ->select(
                    'a.id',
                    'a.reference',
                    'a.designation',
                    DB::raw('COALESCE(s.quantite, 0) AS quantite'),
                    DB::raw($stock_vente_subquery . ' AS stock_vente'),
                    DB::raw($stock_achat_subquery . ' AS stock_achat'),
                    DB::raw('(' . $stock_achat_subquery . ') - (' . $stock_vente_subquery . ') AS stock_legal')
                )
                ->groupBy('a.id', 'a.reference', 'a.designation', 's.quantite');

            if ($request->get('order') && $request->get('columns') ){
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order){
                    $query->orderByRaw(''.$columns[$order['column']]['data'].' '.$order['dir']);
                }
            }
            $table = DataTables::of($query)->order(function (){});
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });

            $table->rawColumns(['selectable_td']);
            return $table->make();
        }
        $rapport_details = Rapport::where('route','stock-produit-legal')->first();
        return view("rapports.stock_produit_legal",compact('rapport_details'));
    }
    public function stock_produit_par_magasin(Request $request)
    {
        $this->guard_custom(['rapport.*']);
        if ($request->ajax()) {
            $stock = DB::table('articles as a')
                ->leftJoin('transaction_stocks as ts', 'a.id', '=', 'ts.article_id')
                ->select(
                    'a.id',
                    DB::raw('COALESCE(SUM(ts.qte_entree) - SUM(ts.qte_sortir), 0) AS stock'),
                    'a.prix_vente',
                    'a.prix_achat'
                )
                ->where('ts.magasin_id', $request->input('magasin_id'))
                ->groupBy('a.id', 'a.prix_vente', 'a.prix_achat');

            $result = DB::table(DB::raw("({$stock->toSql()}) as s"))
                ->mergeBindings($stock)
                ->select(
                    DB::raw('SUM(s.stock * s.prix_vente) AS vente'),
                    DB::raw('SUM(s.stock * s.prix_achat) AS achat')
                )
                ->first();
            $stock_ventes = $result->vente;
            $stock_achats = $result->achat;
            $benifice = $stock_ventes - $stock_achats;
            $profit = $stock_ventes == 0 ? 0 : number_format((($benifice / $stock_ventes) * 100), 3);
            //----------------- DATATABLE----------------
            $query = DB::table('articles as a')
                ->leftJoin('transaction_stocks as s', 'a.id', '=', 's.article_id')
                ->select(
                    'a.reference',
                    'a.designation',
                    'a.id as id',
                    'a.prix_vente',
                    'a.prix_achat',
                    DB::raw('coalesce(sum(s.qte_entree),0) - coalesce(sum(s.qte_sortir),0) AS quantite')
                )->where('magasin_id', $request->input('magasin_id'))->groupBy('a.id');
            if ($request->get('order') && $request->get('columns') ){
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order){
                    $query->orderByRaw(''.$columns[$order['column']]['data'].' '.$order['dir']);
                }
            }
            $table = DataTables::of($query)->order(function (){});
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->addColumn('valeur_achats', function ($row) {
                return $row->quantite * $row->prix_achat;
            });
            $table->addColumn('valeur_ventes', function ($row) {
                return $row->quantite * $row->prix_vente;
            });
            $table->addColumn('bénéfice_potentiel', function ($row) {
                return ($row->quantite * $row->prix_vente) - ($row->quantite * $row->prix_achat);
            });
            $table->rawColumns(['selectable_td']);
            return [
                ...(array)$table->make()->getData(),
                'nombres' => view('rapports.partials.stock_par_magasin.nombres', compact('stock_achats', 'stock_ventes', 'benifice', 'profit'))->render()
            ];
        }
        $magasins = auth()->user()->magasins;
        $rapport_details = Rapport::where('route','stock-produit-magasin')->first();
        return view("rapports.stock_produit_par_magasin", compact('magasins','rapport_details'));
    }

    public function commerciaux(Request $request)
    {
        $this->guard_custom(['rapport.*']);
        $exercice_date = session()->get('exercice');
        $range = [Carbon::now()->setYear($exercice_date)->firstOfYear(), Carbon::now()->setYear($exercice_date)->lastOfYear()];
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');

        $types_inclue = ['fa'];
        if ($request->ajax()) {
            if ($request->get('i_types')) {
                $types_inclue = $request->get('i_types');
            }
            $query = DB::table('commercials')->select(
                ['commercials.nom',
                    'commercials.id as id',
                    DB::raw('count(ventes.id) AS nombre'),
                    DB::raw('COALESCE(SUM(ventes.total_ttc *  COALESCE(ventes.commission_par_defaut,0)/100),0) AS total_commission'),
                    DB::raw('COALESCE(SUM(ventes.total_ttc),0) AS total_ca')]
            )->leftJoin('ventes', function ($join) use ($types_inclue, $request) {
                $join->on('commercials.id', '=', 'ventes.commercial_id')
                    ->where('ventes.statut', 'validé')
                    ->whereIn('ventes.type_document', $types_inclue);
                if ($request->has('i_date')) {
                    $selectedDateRange = $request->get('i_date');
                    $start_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[0]))->toDateString();
                    $end_date = Carbon::createFromFormat('d/m/Y', trim(explode('-', $selectedDateRange)[1]))->toDateString();
                    $range = [$start_date, $end_date];
                    $join->whereBetween('ventes.date_emission', $range);
                }
            })->groupBy('commercials.id', 'commercials.nom');

            if ($request->get('i_search')) {
                $search = '%' . $request->get('i_search') . '%';
                $query->where(function (Builder $query) use ($search) {
                    $query->where('commercials.nom', 'LIKE', $search)->orWhere('commercials.reference', 'LIKE', $search);
                });
            }

            $table = DataTables::of($query);
            $table->addColumn('total_commission', function ($row) {
                return number_format($row->total_commission, 3, '.', ' ') . ' MAD';
            });
            $table->editColumn('total_ca', function ($row) {
                return number_format($row->total_ca, 3, '.', ' ') . ' MAD';
            });
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->rawColumns(['selectable_td']);
            return $table->make();
        }
        $types = Vente::TYPES;
        $rapport_details = Rapport::where('route','commerciaux')->first();
        return view("rapports.commerciaux", compact('date_picker_start', 'date_picker_end', 'types', 'types_inclue','rapport_details'));
    }

    public function afficher_session($id, Request $request)
    {
        $this->guard_custom(['rapport.*']);
        if ($request->ajax() && $request->get('type') == 'depense') {
            $query = PosSession::findOrFail($id)->depenses;
            $table = \Yajra\DataTables\DataTables::of($query);
            $table->editColumn('categorie_depense_id', function ($row) {
                return $row->categorie->nom;
            });
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->addColumn('reference', function ($row) {
                $route = route('depenses.afficher', ['id' => $row->id]);
                return '
                <a target="_blank" href="' . $route . '" class="text-info text-decoration-underline">
                    ' . $row->reference . '
                </a>
            ';
            });
            $table->rawColumns(['selectable_td', 'reference']);
            return $table->make();
        } elseif ($request->ajax()) {
            $query = PosSession::findOrFail($id)->ventes;
            $table = \Yajra\DataTables\DataTables::of($query);
            $table->editColumn('type_document', function ($row) {
                return (__('ventes.' . $row->type_document));
            });
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->addColumn('reference', function ($row) {
                $route = route('ventes.afficher', ['type' => $row->type_document, 'id' => $row->id]);
                return '
                <a target="_blank" href="' . $route . '" class="text-info text-decoration-underline">
                    ' . $row->reference . '
                </a>
            ';
            });
            $table->rawColumns(['selectable_td', 'reference']);
            return $table->make();
        }

        $o_session = PosSession::findOrFail($id);
        $vente = PosSettings::where('key', 'type_vente')->first();
        $retour = PosSettings::where('key', 'type_retour')->first();

        $count_ventes = Vente::where('pos_session_id', $id)
            ->where('type_document', $vente->value)->count();

        $count_retours = Vente::where('pos_session_id', $id)
            ->where('type_document', $retour->value)->count();

        $total_ventes = Vente::where('pos_session_id', $id)
            ->where('type_document', $vente->value)->sum('total_ttc');

        $total_retours = Vente::where('pos_session_id', $id)
            ->where('type_document', $retour->value)->sum('total_ttc');

        $depenses_total = Depense::where('pos_session_id', $id)->sum('montant');
        $rapport_details = Rapport::where('route','sessions.ventes')->first();
        return \view('rapports.partials.sessions.ventes', compact('o_session',
            'vente', 'retour', 'count_ventes', 'count_retours', 'depenses_total'
            , 'total_ventes', 'total_retours','rapport_details'));
    }

    public function sessions(Request $request)
    {
        $this->guard_custom(['rapport.*']);
        if ($request->ajax()) {
            $query = PosSession::query()->whereYear('created_at', session()->get('exercice'));
            if ($request->get('i_search')) {
                $query->where('magasin_id', $request->get('i_search') ?? 0);
            }
            $table = \Yajra\DataTables\DataTables::of($query);
            $table->addColumn('actions', function ($row) {
                $route = route('rapports.sessions.ventes', $row->id);
                return '
        <form class="show-form" action="' . $route . '" method="GET" style="display:inline;">
            <button type="submit" class="btn btn-sm btn-primary show-btn">
                <i class="fa fa-eye"></i>
            </button>
        </form>
    ';
            })
                ->editColumn('magasin_id', function ($row) {
                    return $row->magasin->nom;
                })->editColumn('user_id', function ($row) {
                    return $row->user->name;
                })->editColumn('ouverte', function ($row) {
                    if ($row->ouverte == 1) {
                        return 'Ouverte';
                    } else {
                        return 'Fermée';
                    }
                })->editColumn('date_fin', function ($row) {
                    return $row->date_fin ? Carbon::make($row->date_fin)->format('d/m/Y H:i:s') : '--';

                })->editColumn('created_at', function ($row) {
                    return Carbon::make($row->created_at)->format('d/m/Y H:i:s');
                })->addColumn('total_ttc',function ($row){
                    return number_format($row->ventes()->where('type_document',PosService::getValue('type_vente'))->sum('total_ttc'),3,'.',' ').' MAD';
                });

            $table->rawColumns(['selectable_td', 'actions']);
            return $table->make();
        }

        $magasins = auth()->user()->magasins;
        $rapport_details = Rapport::where('route','sessions')->first();
        return view('rapports.sessions', compact('magasins','rapport_details'));
    }

    /**
     * Handle the request to generate a TVA report.
     *
     * @param Request $request The incoming request.
     * @return JsonResponse|View The response containing the TVA report data or the view.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
//    public function tva(Request $request)
//    {
//        $exercice_date = session()->get('exercice');
//        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
//        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');
//
//        if ($request->ajax() && $request->has('i_date')) {
//            [$start_date, $end_date] = array_map(
//                fn($date) => Carbon::createFromFormat('d/m/Y', trim($date))->toDateString(),
//                explode('-', $request->get('i_date'))
//            );
//
//            $range = [$start_date, $end_date];
//
//            $tva_achats = DB::table('tva_summary')
//                ->whereBetween('date_paiement', $range)
//                ->sum('tva_achats');
//
//            $tva_ventes = DB::table('tva_summary')
//                ->whereBetween('date_paiement', $range)
//                ->sum('tva_ventes');
//
//            $tva_a_payer = $tva_ventes - $tva_achats;
//
//            return response()->json([
//                'ventes_tva' => round($tva_ventes, 3),
//                'achats_tva' => round($tva_achats, 3),
//                'somme' => round($tva_a_payer, 3),
//            ]);
//        }
//
//        return view('rapports.tva', compact('date_picker_start', 'date_picker_end'));
//    }

    public function tva(Request $request)
    {
        $exercice_date = session()->get('exercice');
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');
        if ($request->ajax() && $request->has('i_date')) {
            [$start_date, $end_date] = array_map(
                fn($date) => Carbon::createFromFormat('d/m/Y', trim($date))->toDateString(),
                explode('-', $request->get('i_date'))
            );

            $range = [$start_date, $end_date];

            $query = Paiement::whereBetween('date_paiement',$range)->leftJoin('ventes',function ($join){
                $join->on('paiements.payable_id','=','ventes.id')
                    ->where('paiements.payable_type',Vente::class);
            })->leftJoin('achats',function ($join){
                $join->on('paiements.payable_id','=','achats.id')
                    ->where('paiements.payable_type',Achat::class);
            })->select([
                    'paiements.*',
                    'ventes.reference as vente_reference',
                    'ventes.total_tva as vente_total_tva',
                    'ventes.type_document as vente_type_document',
                    'achats.reference_interne as achat_reference',
                    'achats.total_tva as achat_total_tva',
                    'achats.type_document as achat_type_document',
                    DB::raw('SUM((paiements.encaisser / ventes.total_ttc) * ventes.total_tva) AS tva_ventes'),
                    DB::raw('SUM((paiements.decaisser / achats.total_ttc) * achats.total_tva) AS tva_achats'),
                    DB::raw('SUM(CASE WHEN ventes.total_ttc != 0 THEN (paiements.encaisser / ventes.total_ttc) * ventes.total_tva ELSE 0 END) - SUM(CASE WHEN achats.total_ttc != 0 THEN (paiements.decaisser / achats.total_ttc) * achats.total_tva ELSE 0 END) AS tva_result')]
            )->where(function ($query){
                $query->where('ventes.type_document','fa')
                    ->orWhere('achats.type_document','faa');
            })->groupBy('paiements.id','ventes.reference','achats.reference');

            $paiements = $query->get();

            $table = DataTables::of($paiements)->order(function () {});
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });

            $table->addColumn('reference', function ($row) {
                if($row->payable_type == Vente::class) {
                    $route = route('ventes.afficher', ['type' => $row->vente_type_document,'id' => $row->payable_id]);
                } else {
                    $route = route('achats.afficher', ['type' => $row->achat_type_document,'id' => $row->payable_id]);
                }
                return '<a href="' . $route . '" class="text-info text-decoration-underline">' . ($row->payable_type == Vente::class ? $row->vente_reference : $row->achat_reference) . '</a>';
            });
            $table->addColumn('payable_type', function ($row) {
                return $row->payable_type == Vente::class ? 'Vente' : 'Achat';
            });

            $table->rawColumns(['selectable_td','reference']);
            return [
                ...(array)$table->make()->getData(),
                'ventes_tva' => number_format($paiements->sum('tva_ventes'),3,'.',' '),
                'achats_tva' => number_format($paiements->sum('tva_achats'),3,'.',' '),
                'somme' => number_format($paiements->sum('tva_result'),3,'.',' ')
            ];
        }
        $rapport_details = Rapport::where('route','tva')->first();
        return view('rapports.tva', compact('date_picker_start', 'date_picker_end','rapport_details'));

    }

    public function annuel(Request $request)
    {
        $this->guard_custom(['rapport.*']);

        $exercice_date = session()->get('exercice');
        $range = [Carbon::now()->setYear($exercice_date)->firstOfYear(), Carbon::now()->setYear($exercice_date)->lastOfYear()];

        $first_day_of_the_year = Carbon::now()->setYear($exercice_date )->firstOfYear();

        $vente_ca = Vente::where('type_document', 'fa')
            ->whereBetween('date_emission', $range)
            ->where('statut', 'validé')
            ->sum('total_ttc');

        $vente_ca_ht = Vente::where('type_document', 'fa')
            ->whereBetween('date_emission', $range)
            ->where('statut', 'validé')
            ->sum('total_ht');

        $vente_creance = DB::table('ventes')
            ->leftJoin('paiements', function ($join) use ($range) {
                $join->on('paiements.payable_id', '=', 'ventes.id')
                    ->where('paiements.payable_type', '=', Vente::class)
                    ->whereBetween('paiements.date_paiement', $range);
            })
            ->where('ventes.type_document', 'fa')
            ->whereBetween('ventes.date_emission', $range)
            ->where('ventes.statut', 'validé')
            ->selectRaw('SUM(ventes.total_ttc) - COALESCE(SUM(paiements.encaisser), 0) AS total_creance')
            ->value('total_creance');

        $cumulated_vente_creance = DB::table('ventes')
            ->leftJoin('paiements', function ($join) use ($first_day_of_the_year) {
                $join->on('paiements.payable_id', '=', 'ventes.id')
                    ->where('paiements.payable_type', '=', Vente::class)
                    ->where('paiements.date_paiement', '<', $first_day_of_the_year);
            })
            ->where('ventes.type_document', 'fa')
            ->where('ventes.date_emission', '<', $first_day_of_the_year)
            ->where('ventes.statut', 'validé')
            ->selectRaw('SUM(ventes.total_ttc) - COALESCE(SUM(paiements.encaisser), 0) AS total_creance')
            ->value('total_creance') ?? 0;

        $vente_recette = Paiement::whereBetween('date_paiement',$range)
            ->whereHasMorph('payable', Vente::class, function ($query) {
            $query->where('type_document', 'fa')
                ->where('statut', 'validé');
        })->sum('encaisser');

        $achat_ca = Achat::where('type_document', 'faa')
            ->whereBetween('date_emission', $range)
            ->where('statut', 'validé')
            ->sum('total_ttc');

        $achat_ca_ht = Achat::where('type_document', 'faa')
            ->whereBetween('date_emission', $range)
            ->where('statut', 'validé')
            ->sum('total_ht');

        $achat_creance = DB::table('achats')
            ->leftJoin('paiements', function ($join) use ($range) {
                $join->on('paiements.payable_id', '=', 'achats.id')
                    ->where('paiements.payable_type', '=', Achat::class)
                    ->whereBetween('paiements.date_paiement', $range);
            })
            ->where('achats.type_document', 'faa')
            ->whereBetween('achats.date_emission', $range)
            ->where('achats.statut', 'validé')
            ->selectRaw('SUM(achats.total_ttc) - COALESCE(SUM(paiements.decaisser), 0) AS total_creance')
            ->value('total_creance') ?? 0;

        $cumulated_achat_creance = DB::table('achats')
            ->leftJoin('paiements', function ($join) use ($first_day_of_the_year) {
                $join->on('paiements.payable_id', '=', 'achats.id')
                    ->where('paiements.payable_type', '=', Achat::class)
                    ->where('paiements.date_paiement', '<', $first_day_of_the_year);
            })
            ->where('achats.type_document', 'faa')
            ->where('achats.date_emission', '<', $first_day_of_the_year)
            ->where('achats.statut', 'validé')
            ->selectRaw('SUM(achats.total_ttc) - COALESCE(SUM(paiements.decaisser), 0) AS total_creance')
            ->value('total_creance') ?? 0;

        $achat_recette = Paiement::whereBetween('date_paiement', $range)
            ->whereHasMorph('payable', Achat::class, function ($query) {
                $query->where('type_document', 'faa')
                    ->where('statut', 'validé');
            })->sum('decaisser');

        $depense_ca_ht = Depense::whereBetween('date_operation', $range)
            ->sum('montant');

        $depense_ca =Depense::whereBetween('date_operation', $range)
            ->sum(DB::raw('montant + (montant * (taxe / 100))'));

        if ($request->ajax()) {
            return [
                'vente' => view('rapports.partials.annuel.vente', compact('vente_ca', 'first_day_of_the_year','vente_ca_ht', 'vente_creance', 'vente_recette', 'cumulated_vente_creance'))->render(),
                'achat' => view('rapports.partials.annuel.achat', compact('achat_ca', 'first_day_of_the_year', 'cumulated_achat_creance','achat_ca_ht','achat_creance', 'achat_recette'))->render(),
                'depense' => view('rapports.partials.annuel.depense', compact('depense_ca_ht', 'depense_ca'))->render(),
            ];
        }
        $rapport_details = Rapport::where('route','annuel')->first();
        return view("rapports.annuel",compact('vente_ca','depense_ca_ht', 'depense_ca', 'first_day_of_the_year','cumulated_achat_creance','cumulated_vente_creance','vente_ca_ht','vente_recette', 'vente_creance', 'achat_creance', 'achat_recette', 'achat_ca', 'achat_ca_ht','rapport_details'));
    }


    public function categorie_depense(Request $request)
    {
        $this->guard_custom(['rapport.*']);
        $exercice_date = session()->get('exercice');
        $date_picker_start = Carbon::now()->setYear($exercice_date)->firstOfYear()->format('d/m/Y');
        $date_picker_end = Carbon::now()->setYear($exercice_date)->lastOfYear()->format('d/m/Y');

        // Par défaut utiliser l'année complète
        $range = [
            Carbon::now()->setYear($exercice_date)->firstOfYear()->toDateString(),
            Carbon::now()->setYear($exercice_date)->lastOfYear()->toDateString()
        ];

        // Si une date est fournie, l'utiliser
        if ($request->has('i_date') && !empty($request->get('i_date'))) {
            $selectedDateRange = $request->get('i_date');
            $start_date = Carbon::createFromFormat('d/m/Y', trim(explode(' - ', $selectedDateRange)[0]))->toDateString();
            $end_date = Carbon::createFromFormat('d/m/Y', trim(explode(' - ', $selectedDateRange)[1]))->toDateString();
            $range = [$start_date, $end_date];
        }

        $depenses = \App\Models\Depense::with('categorie')
            ->whereBetween('date_operation', $range)
            ->get();

        $grouped = [];
        foreach ($depenses as $depense) {
            $cat = $depense->categorie ? $depense->categorie->nom : 'Sans catégorie';
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [
                    'total_ttc' => 0,
                    'total_impot' => 0,
                    'total_ht' => 0,
                    'nombre_encaisse' => 0,
                ];
            }
            $ttc = $depense->montant + ($depense->montant * ($depense->taxe ?? 0) / 100);
            $impot = $depense->montant * ($depense->taxe ?? 0) / 100;
            $grouped[$cat]['total_ttc'] += $ttc;
            $grouped[$cat]['total_impot'] += $impot;
            $grouped[$cat]['total_ht'] += $depense->montant;
            if ($depense->statut_paiement === 'paye') {
                $grouped[$cat]['nombre_encaisse'] += 1;
            }
        }

        $total_ttc = array_sum(array_column($grouped, 'total_ttc'));
        $chart_data = [];
        foreach ($grouped as $cat => $data) {
            $chart_data[$cat] = $total_ttc > 0 ? round($data['total_ttc'] * 100 / $total_ttc, 3) : 0;
        }

        $grouped = collect($grouped)->sortByDesc('total_ttc');
        $chart_data = collect($chart_data);
        $rapport_details = Rapport::where('route','categorie-depense')->first();
        return view('rapports.categorie_depense', compact('grouped', 'chart_data', 'date_picker_start', 'date_picker_end','rapport_details'));
    }

    public function rapport_creances(Request $request)
    {
        $this->guard_custom(['rapport.*']);

        $exercice_date = session()->get('exercice');
        $start_of_year = Carbon::now()->setYear($exercice_date)->firstOfYear()->toDateString();
        $end_of_year = Carbon::now()->setYear($exercice_date)->lastOfYear()->toDateString();

        // Par défaut, uniquement les factures "fa"
        $types_inclue = ['fa'];

        if ($request->ajax()) {
            if ($request->get('i_types')) {
                $types_inclue = $request->get('i_types');
            }

            $query = DB::table('clients')
                ->leftJoin('ventes', function ($join) use ($types_inclue) {
                    $join->on('clients.id', '=', 'ventes.client_id')
                        ->whereIn('ventes.type_document', $types_inclue);
                })
                ->select([
                    'clients.id as id',
                    'clients.nom as nom',
                    'clients.telephone as telephone',
                    DB::raw("COALESCE(SUM(CASE WHEN ventes.statut='validé' THEN ventes.solde ELSE 0 END),0) AS total_credit"),
                    DB::raw("COALESCE(SUM(CASE WHEN ventes.statut='validé' AND ventes.date_emission BETWEEN '{$start_of_year}' AND '{$end_of_year}' THEN ventes.solde ELSE 0 END),0) AS credit_n"),
                    DB::raw("COALESCE(SUM(CASE WHEN ventes.statut='validé' AND ventes.date_emission < '{$start_of_year}' THEN ventes.solde ELSE 0 END),0) AS credit_prev"),
                ])
                ->groupBy('clients.id', 'clients.nom', 'clients.telephone');

            if ($request->get('i_search')) {
                $search = '%' . $request->get('i_search') . '%';
                $query->where(function (Builder $q) use ($search) {
                    $q->where('clients.nom', 'LIKE', $search)
                        ->orWhere('clients.reference', 'LIKE', $search);
                });
            }

            // Filtrage par montant min/max selon le critère choisi
            $critere = $request->get('i_critere'); // 'total' | 'current' | 'previous'
            $min = $request->get('i_min');
            $max = $request->get('i_max');
            $column = null;
            if ($critere === 'current') {
                $column = 'credit_n';
            } elseif ($critere === 'previous') {
                $column = 'credit_prev';
            } else {
                $column = 'total_credit';
            }
            if ($column) {
                if ($min !== null && $min !== '') {
                    $query->having($column, '>=', (float)$min);
                }
                if ($max !== null && $max !== '') {
                    $query->having($column, '<=', (float)$max);
                }
            }

            // Tri DataTables
            if ($request->get('order') && $request->get('columns')) {
                $orders = $request->get('order');
                $columns = $request->get('columns');
                foreach ($orders as $order) {
                    $query->orderByRaw('' . $columns[$order['column']]['data'] . ' ' . $order['dir']);
                }
            }

            $table = DataTables::of($query)->order(function () {
            });
            $table->addColumn('selectable_td', function ($row) {
                $id = $row->id;
                return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
            });
            $table->editColumn('total_credit', function ($row) {
                return number_format($row->total_credit, 3, '.', ' ') . ' MAD';
            });
            $table->editColumn('credit_n', function ($row) {
                return number_format($row->credit_n, 3, '.', ' ') . ' MAD';
            });
            $table->editColumn('credit_prev', function ($row) {
                return number_format($row->credit_prev, 3, '.', ' ') . ' MAD';
            });
            $table->rawColumns(['selectable_td']);
            return $table->make();
        }

        // Indicateurs globaux (actuellement basés sur 'fa' uniquement)
        $total_general = DB::table('ventes')
            ->where('statut', 'validé')
            ->where('type_document', 'fa')
            ->sum('solde');
        $total_n = DB::table('ventes')
            ->where('statut', 'validé')
            ->where('type_document', 'fa')
            ->whereBetween('date_emission', [$start_of_year, $end_of_year])
            ->sum('solde');
        $total_prev = DB::table('ventes')
            ->where('statut', 'validé')
            ->where('type_document', 'fa')
            ->where('date_emission', '<', $start_of_year)
            ->sum('solde');

        $rapport_details = Rapport::where('route', 'rapport-creances')->first();
        $types = Vente::TYPES;
        return view('rapports.rapport_creances', [
            'total_general' => $total_general,
            'total_n' => $total_n,
            'total_prev' => $total_prev,
            'exercice' => $exercice_date,
            'rapport_details' => $rapport_details,
            'types' => $types,
            'types_inclue' => $types_inclue,
        ]);
    }

    public function historique_client(Request $request)
    {
        $this->guard_custom(['rapport.*']);
        $clients = \App\Models\Client::select(['id', 'nom'])->orderBy('nom')->get();
        $globals = \App\Models\GlobalSetting::first();
        $prix_revient_active = (bool)($globals->prix_revient ?? false);

        // Par défaut, on inclut uniquement les factures 'fa' (comme rapport_creances)
        $types_inclue = ['fa'];

        if ($request->ajax()) {
            if ($request->get('i_types')) {
                $types_inclue = $request->get('i_types');
            }

            $clientId = $request->get('i_client');
            $annee = $request->get('i_annee');

            // Agrégats Ventes (CA & Crédit) par client/année
            $ventesAgg = DB::table('ventes')
                ->selectRaw('ventes.client_id as client_id, YEAR(ventes.date_emission) as annee, COALESCE(SUM(ventes.total_ttc),0) as ca, COALESCE(SUM(ventes.solde),0) as credit_annee')
                ->when($clientId, function ($q) use ($clientId) { $q->where('ventes.client_id', $clientId); })
                ->when($annee, function ($q) use ($annee) { $q->whereYear('ventes.date_emission', $annee); })
                ->where('ventes.statut', 'validé')
                ->whereIn('ventes.type_document', $types_inclue)
                ->groupBy('ventes.client_id', DB::raw('YEAR(ventes.date_emission)'))
                ->get();

            // Agrégats Encaissements par client/année (paiements sur ventes)
            $encaissementsAgg = DB::table('paiements')
                ->join('ventes', function ($join) use ($types_inclue) {
                    $join->on('paiements.payable_id', '=', 'ventes.id')
                        ->where('paiements.payable_type', Vente::class)
                        ->where('ventes.statut', 'validé')
                        ->whereIn('ventes.type_document', $types_inclue);
                })
                ->when($clientId, function ($q) use ($clientId) { $q->where('ventes.client_id', $clientId); })
                ->when($annee, function ($q) use ($annee) { $q->whereYear('paiements.date_paiement', $annee); })
                ->selectRaw('ventes.client_id as client_id, YEAR(paiements.date_paiement) as annee, COALESCE(SUM(paiements.encaisser),0) as encaissements')
                ->groupBy('ventes.client_id', DB::raw('YEAR(paiements.date_paiement)'))
                ->get();

            // Agrégats Prix de revient par client/année (optionnel)
            $revientAgg = collect();
            if ($prix_revient_active) {
                $revientAgg = DB::table('vente_lignes')
                    ->join('ventes', 'vente_lignes.vente_id', '=', 'ventes.id')
                    ->when($clientId, function ($q) use ($clientId) { $q->where('ventes.client_id', $clientId); })
                    ->when($annee, function ($q) use ($annee) { $q->whereYear('ventes.date_emission', $annee); })
                    ->where('ventes.statut', 'validé')
                    ->whereIn('ventes.type_document', $types_inclue)
                    ->selectRaw('ventes.client_id as client_id, YEAR(ventes.date_emission) as annee, COALESCE(SUM(vente_lignes.revient * vente_lignes.quantite),0) as prix_revient')
                    ->groupBy('ventes.client_id', DB::raw('YEAR(ventes.date_emission)'))
                    ->get();
            }

            // Indexer par clé composite client|annee
            $toKey = function ($client_id, $year) { return $client_id . '|' . $year; };
            $ventesMap = collect($ventesAgg)->keyBy(function ($r) use ($toKey) { return $toKey($r->client_id, $r->annee); });
            $encaissMap = collect($encaissementsAgg)->keyBy(function ($r) use ($toKey) { return $toKey($r->client_id, $r->annee); });
            $revientMap = collect($revientAgg)->keyBy(function ($r) use ($toKey) { return $toKey($r->client_id, $r->annee); });

            // Ensemble des clés
            $allKeys = $ventesMap->keys()->merge($encaissMap->keys())->merge($revientMap->keys())->unique();

            // Récupérer les noms des clients concernés
            $clientIds = $allKeys->map(function ($k) { return (int)explode('|', $k)[0]; })->unique()->values();
            $clientsMap = DB::table('clients')->whereIn('id', $clientIds)->pluck('nom', 'id');

            // Construire les lignes
            $rows = [];
            foreach ($allKeys as $key) {
                [$cid, $year] = explode('|', $key);
                $cid = (int)$cid; $year = (int)$year;
                $v = $ventesMap->get($key);
                $e = $encaissMap->get($key);
                $r = $prix_revient_active ? $revientMap->get($key) : null;

                $ca = $v->ca ?? 0;
                $credit = $v->credit_annee ?? 0;
                $enc = $e->encaissements ?? 0;
                $rev = $prix_revient_active ? ($r->prix_revient ?? 0) : null;

                $rows[] = [
                    'id' => $cid . ':' . $year,
                    'client' => $clientsMap[$cid] ?? ('Client #' . $cid),
                    'annee' => $year,
                    'ca' => number_format($ca, 3, '.', ' ') . ' MAD',
                    'encaissements' => number_format($enc, 3, '.', ' ') . ' MAD',
                    'prix_revient' => $prix_revient_active ? number_format($rev, 3, '.', ' ') . ' MAD' : '--',
                    'credit_annee' => number_format($credit, 3, '.', ' ') . ' MAD',
                ];
            }

            // Tri: année desc puis client asc
            $rows = collect($rows)->sortBy([["annee", 'desc'], ['client', 'asc']])->values();

            $table = DataTables::of($rows)->order(function () {});
            $table->addColumn('selectable_td', function ($row) {
                $id = data_get($row, 'id');
                return '<input type="checkbox" class="row-select form-check-input" value="' . e($id) . '">';
            });
            $table->rawColumns(['selectable_td']);
            return $table->make();
        }
        // Valeurs par défaut pour la vue
        $currentYear = Carbon::now()->year;
        $rapport_details = Rapport::where('route', 'historique-client')->first();
        $types = Vente::TYPES;
        return view('rapports.historique_client', [
            'clients' => $clients,
            'prix_revient' => $prix_revient_active,
            'currentYear' => $currentYear,
            'rapport_details' => $rapport_details,
            'types' => $types,
            'types_inclue' => $types_inclue,
        ]);
    }


}

