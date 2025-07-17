<?php
namespace App\Http\Controllers;



use App\Models\Compte;
use App\Models\ReleveBancaire;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ReleveBancaireController extends Controller
{
    public function liste(Request $request)
    {
        $this->guard_custom(['compte.liste']);
        if ($request->ajax()) {
            $exercice = session()->get('exercice');
            $data = ReleveBancaire::with(['compte'])->where('year', $exercice);

            return DataTables::of($data)
                ->editColumn('compte_id', function ($row) {
                    return $row->compte->nom ?? 'N/A';
                })
                ->addColumn('selectable_td', function ($row) {
                    $id = $row['id'];
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                })
                ->editColumn('month', function ($row) {
                    $months = [
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                    ];
                    return $months[$row->month] ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $viewAction = '<a class="btn btn-sm btn-soft-primary mx-1" href="' . $row->url . '" target="_blank">
                <i class="fa fa-eye"></i></a>';
                    $deleteAction = '<button data-url="' . route('releve-bancaire.supprimer', ['id' => $row->id]) . '"
                                  class="btn btn-sm btn-soft-danger sa-warning mx-1"><i class="fa fa-trash-alt"></i></button>';
                    $action = $viewAction . $deleteAction;
                    return $action;
                })
                ->rawColumns(['selectable_td', 'action']) // Permettre HTML dans les colonnes "action" et "remain"
                ->make(true);
        }
        $o_comptes = Compte::where('type', 'banque')->get();
        return view('releve_bancaires.liste', compact('o_comptes'));
    }

    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['compte.ajouter']);
        $request->validate([
            'compte_id' => 'required|exists:comptes,id',
            'month' => 'required|integer',
            'url' => 'required|url',
        ]);

        DB::beginTransaction();

        try {
            $o_releve = new ReleveBancaire();
            $o_releve->compte_id = $request->compte_id;
            $o_releve->month = $request->month;
            $o_releve->year=  session()->get('exercice');
            $o_releve->url = $request->url;
            $o_releve->save();

            DB::commit();
            session()->flash('success', "Relevé bancaire ajouté avec succès");
            return response('Relevé bancaire ajouté avec succès', 200);
        } catch (\Exception $e) {
            DB::rollback();
            LogService::logException($e);
            return redirect()->route('releve-bancaire.liste')->with('error', 'Une erreur est survenue lors de l\'ajout du relevé bancaire');
        }
    }

    public function supprimer(int $id)
    {
        $this->guard_custom(['compte.supprimer']);

        if (\request()->ajax()) {
            $o_releve = ReleveBancaire::find($id);
            if ($o_releve) {
                $o_releve->delete();
                return response('Relevé bancaire supprimée avec succès', 200);
            } else {
                return response('Erreur', 404);
            }
        }
        abort(404);
    }
}
