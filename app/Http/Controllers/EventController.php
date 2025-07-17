<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function liste(Request $request)
    {
        $this->guard_custom(['activite.liste']);
        if ($request->ajax()) {
            $query = Event::query();
            //-------------Filters-------------
            if ($request->get('client_id')) {
                $query->where('client_id', $request->get('client_id'));
            }
            if ($request->get('date')) {
                $query->where('date', Carbon::createFromFormat('d/m/Y', $request->get('date'))->toDateString());
            }
            if ($request->get('type')) {
                $query->where('type', $request->get('type'));
            }

            $table = \DataTables::of($query);
            $table->addColumn(
                'selectable_td',
                function ($contact) {
                    $id = $contact->id;
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            )->addColumn('actions', function ($row) {
                $edit_modal = ['url' => route('events.modifier', $row->id), 'modal_id' => 'edit-modal'];
                $delete = 'supprimer';
                $crudRoutePart = 'events';
                $id = $row?->id;
                $action = view(
                    'partials.__datatable-action',
                    compact(
                        'edit_modal',
                        'delete',
                        'crudRoutePart',
                        'id',
                    )
                )->render();
                return $action;
            })->editColumn('client_id', function ($row) {
                return $row->client->nom;
            })->editColumn('type', function ($row) {
                return Event::TYPES[$row->type];
            })->addColumn('dure', function ($row) {
                return Carbon::createFromFormat('H:i', $row->debut)->diffForHumans(Carbon::createFromFormat('H:i', $row->fin), true);
            })->rawColumns(['actions', 'selectable_td']);
            return $table->make();
        }
        $types = Event::TYPES;
        return view('events.liste', compact('types'));
    }

    public function sauvegarder(Request $request)
    {
        $this->guard_custom(['activite.sauvegarder']);
        $validation = \Validator::make($request->all(), [
            'titre' => 'required|string|min:3|max:255',
            'type' => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date_format:d/m/Y',
            'fin' => 'required|date_format:H:i',
            'debut' => 'required|date_format:H:i',
            'client_id' => 'required|exists:clients,id'
        ])->validate();
        Event::create([
            'titre' => $request->input('titre'),
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'date' => Carbon::createFromFormat('d/m/Y', $request->input('date')),
            'debut' => $request->input('debut'),
            'fin' => $request->input('fin'),
            'client_id' => $request->input('client_id')
        ]);
        return response('Activité ajouté');
    }

    public function modifier($id)
    {
        $this->guard_custom(['activite.mettre_a_jour']);

        $event = Event::findOrFail($id);
        $types_event = Event::TYPES;
        return view('events.modifier', compact('event', 'types_event'));
    }

    public function mettre_a_jour(Request $request, $id)
    {
        $this->guard_custom(['activite.mettre_a_jour']);

        $validation = \Validator::make($request->all(), [
            'titre' => 'required|string|min:3|max:255',
            'type' => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date_format:d/m/Y',
            'fin' => 'required|date_format:H:i',
            'debut' => 'required|date_format:H:i',
        ])->validate();
        $event = Event::findOrFail($id);
        $event->update([
            'titre' => $request->input('titre'),
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'date' => Carbon::createFromFormat('d/m/Y', $request->input('date')),
            'debut' => $request->input('debut'),
            'fin' => $request->input('fin'),
        ]);
        return response('Activité mise à jour');
    }

    public function afficher($id)
    {
        $this->guard_custom(['activite.afficher']);

        $event = Event::findOrFail($id);
        $types_event = Event::TYPES;
        return view('events.show', compact('event', 'types_event'));

    }

    public function supprimer($id)
    {
        $this->guard_custom(['activite.supprimer']);

        $event = Event::findOrFail($id);
        $event->delete();
        return response('Activité supprimée');
    }
}
