<?php

namespace App\Http\Controllers;

use App\Exports\AchatsExport;
use App\Exports\ArticlesExport;
use App\Exports\ClientsExport;
use App\Exports\FournisseursExport;
use App\Exports\PaiementsExport;
use App\Exports\ProductExport;
use App\Exports\StocksExport;
use App\Exports\VentesExport;
use App\Models\Magasin;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function liste()
    {
        $this->guard_custom(['exporter.*']);
        return view('exportations.liste');
    }
    public function exporter_stock_page()
    {
        $this->guard_custom(['exporter.*']);
        if(Magasin::count()> 1 ){
            $o_magasins = Magasin::all();
            return view('exportations.stocks',compact('o_magasins'));
        }else{
            return view('exportations.stocks');
        }
    }
    public function exporter_vente_page()
    {
        $this->guard_custom(['exporter.*']);
        if(Magasin::count()> 1 ){
            $o_magasins = Magasin::all();
            return view('exportations.ventes',compact('o_magasins'));
        }else {
            return view('exportations.ventes');
        }
    }
    public function exporter_achat_page()
    {
        $this->guard_custom(['exporter.*']);
        if(Magasin::count()> 1 ){
            $o_magasins = Magasin::all();
            return view('exportations.achats',compact('o_magasins'));
        }else {
            return view('exportations.achats');
        }
    }
    public function exporter_paiement_page()
    {
        $this->guard_custom(['exporter.*']);
        if (Magasin::count() > 1) {
            $o_magasins = Magasin::all();
            return view('exportations.paiements', compact('o_magasins'));
        } else {
            return view('exportations.paiements');
        }
    }
    public function exporter_client()
    {
        $this->guard_custom(['exporter.*']);
        return Excel::download(new ClientsExport(), 'export_clients.xlsx');
    }
    public function exporter_fournisseur()
    {
        $this->guard_custom(['exporter.*']);
        return Excel::download(new FournisseursExport(), 'export_fournisseurs.xlsx');
    }
    public function exporter_produit()
    {
        $this->guard_custom(['exporter.*']);
        return Excel::download(new ArticlesExport(), 'export_produits.xlsx');
    }

    public function exporter_stock(Request $request)
    {
        $this->guard_custom(['exporter.*']);
        if($request->get('magasin')){
            $magasin_id = $request->get('magasin');
            $magasin = Magasin::where('id', $magasin_id)->first();
        }else{
            $magasin = Magasin::first();
        }
        $filename = 'export_stocks_'.$magasin->reference.'.xlsx';
        return Excel::download(new StocksExport($magasin->id), $filename);
    }
    public function exporter_vente(Request $request)
    {
        $this->guard_custom(['exporter.*']);
        if($request->get('magasin')){
        $magasin_id = $request->get('magasin');
        $magasin = Magasin::where('id', $magasin_id)->first();
        }else{
            $magasin = Magasin::first();
        }

        $type = $request->get('type');
        $filename = 'export_ventes_'.$type.'_'.$magasin->reference.'.xlsx';
        return Excel::download(new VentesExport($type,$magasin->id), $filename);
    }
    public function exporter_achat(Request $request)
    {
        $this->guard_custom(['exporter.*']);
        if($request->get('magasin')){
            $magasin_id = $request->get('magasin');
            $magasin = Magasin::where('id', $magasin_id)->first();
        }else{
            $magasin = Magasin::first();
        }

        $type = $request->get('type');
        $filename = 'export_achats_'.$type.'_'.$magasin->reference.'.xlsx';
        return Excel::download(new AchatsExport($type,$magasin->id), $filename);
    }
    public function exporter_paiement(Request $request)
    {
        $this->guard_custom(['exporter.*']);
        if($request->get('magasin')){
            $magasin_id = $request->get('magasin');
            $magasin = Magasin::where('id', $magasin_id)->first();
        }else{
            $magasin = Magasin::first();
        }
        $type = $request->get('type');
        $filename = 'export_paiements_'.$type.'_'.$magasin->reference.'.xlsx';
        return Excel::download(new PaiementsExport($type,$magasin->id), $filename);
    }
}
