<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\Affaire;
use App\Models\Renouvellement;
use App\Services\LimiteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;



class AbonnementController extends Controller
{



    public function liste(Request $request)
    {
        $this->guard_custom(['abonnement.liste']);
        if ($request->ajax()) {
            $data = Abonnement::with(['client', 'article'])
                ->select('abonnements.*')
                ->whereNull('is_archived');


            // Appliquer les filtres si les paramètres sont présents
            if ($request->has('client_id') && $request->get('client_id')) {
                $data->where('client_id', $request->get('client_id'));
            }

            if ($request->has('article_id') && $request->get('article_id')) {
                $data->where('article_id', $request->get('article_id'));
            }

            if ($request->has('prix') && $request->get('prix')) {
                $data->where('prix',  $request->get('prix'));
            }
            if ($request->has('titre') && $request->get('titre')) {
                $data->where('titre', 'LIKE', '%' . $request->get('titre') . '%');
            }

            if ($request->has('date_abonnement') && $request->get('date_abonnement')) {
                $dates = explode(' - ', $request->get('date_abonnement'));
                $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
                $data->whereBetween('date_abonnement', [$startDate, $endDate]);
            }

            if ($request->has('date_expiration') && $request->get('date_expiration')) {
                $dates = explode(' - ', $request->get('date_expiration'));
                $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
                $data->whereBetween('date_expiration', [$startDate, $endDate]);
            }


            return DataTables::of($data)
                ->editColumn('client_id', function ($row) {
                    return $row->client->nom ?? 'N/A';
                })
                ->addColumn('selectable_td', function ($row) {
                    $id = $row['id'];
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                })
                ->addColumn('article_id', function ($row) {
                    return $row->article->designation ?? 'N/A';
                })
                ->editColumn('date_abonnement', function ($row) {
                    return Carbon::parse($row->date_abonnement)->format('d/m/Y');
                })
                ->editColumn('date_expiration', function ($row) {
                    return Carbon::parse($row->date_expiration)->format('d/m/Y');
                })
                ->addColumn('remain', function ($row) {
                    $currentDate = Carbon::now();
                    $classname = "bg-dark";
                    $expirationDate = Carbon::parse($row->date_expiration);
                    $remainDays = $currentDate->diffInDays($expirationDate, false);
                    if ($remainDays < 0) {
                        $classname = "bg-dark";
                    } elseif ($remainDays <= 15 && $remainDays >= 3) {
                        $classname = "bg-warning";
                    } elseif ($remainDays <= 3 && $remainDays >= 0) {
                        $classname = "bg-danger";
                    } else {
                        $classname = "bg-success";
                    }
                    return '<span class="badge ' . $classname . '">' . str_pad($remainDays, 3, 0, STR_PAD_LEFT) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $viewAction = '<a class="btn btn-sm btn-soft-primary mx-1" href="' . route("abonnements.afficher", $row->id) .'">
                                    <i class="fa fa-eye"></i></a>';
                    $editAction = '<a href="' . route('abonnements.modifier', ['id' => $row->id]) . '" class="btn btn-sm btn-soft-warning">
                                    <i class="fa fa-edit"></i></a>';
                    $deleteAction = '<button data-url="' . route('abonnements.supprimer', ['id' => $row->id]) . '"
                                      class="btn btn-sm btn-soft-danger sa-warning mx-1"><i class="fa fa-trash-alt"></i></button>';
                    $action = $viewAction . $editAction . $deleteAction;
                    return $action;
                })
                ->orderColumn('remain', function($query, $order) {
                    $query->orderByRaw('DATEDIFF(date_expiration, ?) ' . $order, [Carbon::now()]);
                })

                ->orderColumn('article_id', function ($query, $order) {
                    $query->join('articles', 'abonnements.article_id', '=', 'articles.id') // Assurez-vous que c'est le bon champ
                    ->orderBy('articles.designation', $order);
                })

                ->rawColumns(['selectable_td', 'action', 'remain']) // Permettre HTML dans les colonnes "action" et "remain"
                ->make(true);
        }

        return view('abonnements.liste');
    }




    public function archives(Request $request)
    {
        $this->guard_custom(['abonnement.archives']);

        if ($request->ajax()) {
            // Récupérer les abonnements avec is_archived non null, et les trier par date d'archivage
            $data = Abonnement::whereNotNull('is_archived')
                ->with(['client', 'article']);

            // Appliquer les filtres si les paramètres sont présents
            if ($request->has('client_id') && $request->get('client_id')) {
                $data->where('client_id', $request->get('client_id'));
            }

            if ($request->has('article_id') && $request->get('article_id')) {
                $data->where('article_id', $request->get('article_id'));
            }
            if ($request->has('prix') && $request->get('prix')) {
                $data->where('prix' , $request->get('prix'));
            }
            if ($request->has('titre') && $request->get('titre')) {
                $data->where('titre', 'LIKE', '%' . $request->get('titre') . '%');
            }
            if ($request->get('date_abonnement')) {
                $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_abonnement'))[0]))->toDateString();
                $end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_abonnement'))[1]))->toDateString();
                if ($start === $end) {
                    $data->whereDate('date_abonnement', $end);
                }
                $data->where(function ($data) use ($start, $end) {
                    $data->whereDate('date_abonnement', '>=', $start)
                        ->whereDate('date_abonnement', '<=', $end)
                        ->orWhereNull('date_abonnement');
                });
            }

            if ($request->get('date_expiration')) {
                $start = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_expiration'))[0]))->toDateString();
                $end = Carbon::createFromFormat('d/m/Y', trim(explode('-', $request->get('date_expiration'))[1]))->toDateString();
                if ($start === $end) {
                    $data->whereDate('date_expiration', $end);
                }
                $data->where(function ($data) use ($start, $end) {
                    $data->whereDate('date_expiration', '>=', $start)
                        ->whereDate('date_expiration', '<=', $end)
                        ->orWhereNull('date_expiration');
                });
            }


            return DataTables::of($data)
                ->editColumn('client_id', function ($row) {
                    return $row->client->nom ?? 'N/A';
                })
                ->addColumn('article_id', function ($row) {
                    return $row->article->designation ?? 'N/A';
                })
                ->editColumn('date_abonnement', function ($row) {
                    return Carbon::parse($row->date_abonnement)->format('d/m/Y');
                })
                ->editColumn('date_expiration', function ($row) {
                    return Carbon::parse($row->date_expiration)->format('d/m/Y');
                })
                ->editColumn('is_archived', function ($row) {
                    return Carbon::parse($row->is_archived)->format('d/m/Y');
                })
                ->addColumn('remain', function ($row) {
                    $currentDate = Carbon::now();
                    $expirationDate = Carbon::parse($row->date_expiration);
                    $remainDays = $currentDate->diffInDays($expirationDate, false);
                    $classname = "bg-dark";

                    if ($remainDays < 0) {
                        $classname = "bg-dark";
                    } elseif ($remainDays <= 15 && $remainDays >= 3) {
                        $classname = "bg-warning";
                    } elseif ($remainDays <= 3 && $remainDays >= 0) {
                        $classname = "bg-danger";
                    } else {
                        $classname = "bg-success";
                    }
                    return '<span class="badge ' . $classname . '">' . str_pad($remainDays, 3, 0, STR_PAD_LEFT) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $viewAction = '<a class="btn btn-sm btn-soft-primary mx-1" href="' . route("abonnements.afficher", $row->id) .'">
                                    <i class="fa fa-eye"></i></a>';
                    $action = $viewAction;
                    return $action;
                })
                // Tri des colonnes remain et article_id
                ->orderColumn('remain', function($query, $order) {
                    $query->orderByRaw('DATEDIFF(date_expiration, ?) ' . $order, [Carbon::now()]);
                })
                ->orderColumn('article_id', function ($query, $order) {
                    $query->join('articles', 'abonnements.article_id', '=', 'articles.id')
                        ->orderBy('articles.designation', $order);
                })

                ->rawColumns(['selectable_td', 'action', 'remain'])
                ->make(true);
        }

        return view('abonnements.archives');
    }


    public function ajouter(){
        $this->guard_custom(['abonnement.sauvegarder']);
        return view('abonnements.ajouter');
    }

    public function afficher($id){
        $this->guard_custom(['abonnement.afficher']);
        $abonnement = Abonnement::findOrFail($id);
        $renouvellements = Renouvellement::where('abonnement_id', $id)->orderby('id','desc')->get();
        return view('abonnements.afficher', compact('abonnement','renouvellements'));
    }

    public function sauvegarder(Request $request){
        $this->guard_custom(['abonnement.sauvegarder']);
        $request->validate([
            'titre' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'article_id' => 'required|exists:articles,id',
            'date_abonnement' => 'required|date_format:d/m/Y',
            'date_expiration' => 'required||date_format:d/m/Y|after:date_abonnement',
            'prix' => 'nullable|numeric|min:0', // Prix nullable
            'description' => 'nullable|string',
        ]);
        Abonnement::create([
            'titre' => $request->titre,
            'client_id' => $request->client_id,
            'article_id' => $request->article_id,
            'date_abonnement' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_abonnement)->format('Y-m-d'),
            'date_expiration' =>\Carbon\Carbon::createFromFormat('d/m/Y', $request->date_expiration)->format('Y-m-d'),
            'prix' => $request->prix,
            'description' => $request->description,
        ]);

        return redirect()->route('abonnements.liste')->with('success', 'Abonnement créé avec succès.');
    }



    public function archiver($id)
    {
        $this->guard_custom(['abonnement.archiver']);
        $abonnement = Abonnement::findOrFail($id);
        $abonnement->is_archived = Carbon::now();  // Enregistrer la date d'archivage
        $abonnement->save();

        return response()->json([
            'message' => 'L’abonnement a été archivé avec succès.',
        ]);
    }


public function desarchiver($id)
{
    $this->guard_custom(['abonnement.desarchiver']);
    $abonnement = Abonnement::findOrFail($id);
    $abonnement->is_archived = null;  // Réinitialiser la date d'archivage
    $abonnement->save();

    return response()->json([
        'message' => 'L’abonnement a été désarchivé avec succès.',
    ]);
}




    public function modifier($id)
    {
        $this->guard_custom(['abonnement.mettre_a_jour']);

        $abonnement = Abonnement::findOrFail($id);
        return view('abonnements.modifier', compact('abonnement'));
    }

    public function mettre_a_jour(Request $request, $id){
        $this->guard_custom(['abonnement.mettre_a_jour']);

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'article_id' => 'required|exists:articles,id',
            'titre' => 'required|string|max:255',
            'date_abonnement' => 'required|date_format:d/m/Y',
            'date_expiration' => 'required|date_format:d/m/Y',
            'prix' => 'nullable|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $abonnement = Abonnement::findOrFail($id);
        $abonnement->client_id = $request->input('client_id');
        $abonnement->article_id = $request->input('article_id');
        $abonnement->titre = $request->input('titre');
        $abonnement->date_abonnement = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('date_abonnement'));
        $abonnement->date_expiration = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('date_expiration'));
        $abonnement->prix = $request->input('prix');
        $abonnement->description = $request->input('description');
        $abonnement->save();

        // Redirection avec message de succès
        return redirect()->route('abonnements.liste')->with('success', 'Abonnement mis à jour avec succès.');
    }


    public function supprimer(int $id)
    {
        $this->guard_custom(['abonnement.supprimer']);

        if (\request()->ajax()) {
            $abn = Abonnement::find($id);
            if ($abn) {
                $abn->delete();
                return response('Abonnement supprimée avec succès', 200);
            } else {
                return response('Erreur', 404);
            }
        }
        abort(404);
    }


    public function renew_modal($id)
    {
        $this->guard_custom(['abonnement.renouveler']);
        $abonnement = Abonnement::findOrFail($id) ;
        return view('abonnements.partials.renew_modal',compact('abonnement')) ;
    }

    public function renouveler(Request $request)
    {
        $this->guard_custom(['abonnement.renouveler']);
        $validatedData = $request->validate([
            'abonnement_id' => 'required|exists:abonnements,id',
            'date_renouvellement' => 'required|date_format:d/m/Y',
            'date_expiration' => 'required|date_format:d/m/Y|after_or_equal:date_renouvellement',
            'montant' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'document_reference' => 'nullable|string|max:255',
        ]);
        $validatedData['date_renouvellement'] = Carbon::createFromFormat('d/m/Y', $validatedData['date_renouvellement'])->format('Y-m-d');
        $validatedData['date_expiration'] = Carbon::createFromFormat('d/m/Y', $validatedData['date_expiration'])->format('Y-m-d');


        $renouvellement = Renouvellement::create($validatedData);

        if($renouvellement)
        {
            $abn = Abonnement::find($renouvellement->abonnement_id);
            $abn->date_expiration = $renouvellement->date_expiration ;
            $abn->save() ;
        }

        return response('Renouvellement créé avec succès.', 201);
    }

    public function supprimer_renouvellement($id) {
        $this->guard_custom(['abonnement.mettre_a_jour']);

        $renouvellement = Renouvellement::findOrFail($id);
        $renouvellement->delete();
        return response('Renouvellement supprimé avec succès.',200);
    }





}
