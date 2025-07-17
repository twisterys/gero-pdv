<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {


            if ($request->ajax()) {
                $contacts = Contact::all(); // Fetch all contacts from the database
                $table = DataTables::of($contacts);
                // $table->addColumn('action', function($contact) {
                //     return view('partials.__datatable-action', ['row'=>$contact,"crudRoutePart"=>"contact","viewGate"=>true,"deleteGate"=>true,"editGate"=>null]);
                // });
                $table->addColumn('actions', '&nbsp;');

                $table->editColumn('actions', function ($row) {
                    $viewGate      = 'contacts.show';
                    $editGate      = 'contacts.modifier';
                    $deleteGate    = 'contacts.delete';
                    $crudRoutePart = 'contacts';

                    return view('partials.__datatable-action', compact(
                        'viewGate',
                        'editGate',
                        'deleteGate',
                        'crudRoutePart',
                        'row'
                    ));
                });
                $table->addColumn(
                    'selectable_td',
                    function ($contact) {
                        $id = $contact->id;
                        return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                    }
                );
                $table->rawColumns(['actions','selectable_td']);
                return $table->make();
            }
            $form_juridique_types = __("cruds.contact.form_juridique_type");
            return view("contacts.liste", compact('form_juridique_types'));
        } catch (\Exception $e) {
            // Handle the exception, you can log it or return an error response
            return response()->json(['error' => 'An error occurred while processing the request.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        try {
        // dd($request->validated());
        Contact::create($request->validated());
        return redirect()->back()->with('success', 'Client ajouter avec success');
        }
        catch (\Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function afficher(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function supprimer(Contact $contact)
    {
        //
    }
}
