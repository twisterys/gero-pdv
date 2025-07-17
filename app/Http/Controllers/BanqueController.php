<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use URL;
use Validator;
use Yajra\DataTables\Facades\DataTables;

class BanqueController extends Controller
{
    public function liste(Request $request)
    {
        if ($request->ajax()) {
            $query = Banque::query();
            $table = DataTables::of($query);
            $table->addColumn('actions', function ($row) {
                $crudRoutePart = 'banques';
                $delete = 'supprimer';
                $id = $row->id;
                $edit_modal = [
                    'url' => route('banques.modifier', $row->id),
                    'modal_id' => 'edit-modal'
                ];
                return view('partials.__datatable-action', compact('id', 'crudRoutePart', 'edit_modal','delete'));
            })->editColumn('image', function ($row) {
                return '<img style="max-width:100px" src="' . asset($row->image) . '" >';
            })->rawColumns(['actions', 'image']);
            return $table->make();
        }
        return view('parametres.banques.liste');
    }

    public function modifier(int $id)
    {
        $banque = Banque::findOrFail($id);
        return view('parametres.banques.partials.modifier_modal', compact('banque'));
    }

    public function mettre_a_jour(Request $request, int $id)
    {
        $banque = Banque::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nom' => 'required|min:1|max:255',
            'i_image' => 'nullable|image|max:6144|mimes:jpeg,png,jpg'
        ], [], [
            'nom' => 'nom',
            'i_image' => 'logo'
        ])->validate();
        \DB::beginTransaction();
        try {
            $image = $banque->image;
            if ($request->file('i_image')) {
                $file = $request->file('i_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = 'public' . DIRECTORY_SEPARATOR . 'banques' . DIRECTORY_SEPARATOR . $fileName;
                \Storage::disk('external_storage')->put($path, file_get_contents($file));
                $image = URL::to('tenants/' . tenancy()->tenant->id . '/public/banques/' . $fileName);

            }
            $banque->update([
                'nom' => $request->input('nom'),
                'image' => $image
            ]);
            \DB::commit();
            return response('Banque mise à jour !');
        }catch (\Exception $exception){
            \DB::rollBack();
            LogService::logException($exception);
            return response($exception->getMessage(),$exception->getCode());
        }
    }

    public function sauvegarder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|min:1|max:255',
            'i_image' => 'required|image|max:6144|mimes:jpeg,png,jpg'
        ], [], [
            'nom' => 'nom',
            'i_image' => 'logo'
        ])->validate();
        \DB::beginTransaction();
        try {
            $image = null;
            if ($request->file('i_image')) {
                $file = $request->file('i_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = 'public' . DIRECTORY_SEPARATOR . 'banques' . DIRECTORY_SEPARATOR . $fileName;
                \Storage::disk('external_storage')->put($path, file_get_contents($file));
                $image = URL::to('tenants/' . tenancy()->tenant->id . '/public/banques/' . $fileName);

            }
            Banque::create([
                'nom' => $request->input('nom'),
                'image' => $image
            ]);
            \DB::commit();
            return response('Banque ajouté !');
        }catch (\Exception $exception){
            \DB::rollBack();
            LogService::logException($exception);
            return response($exception->getMessage(),$exception->getCode());
        }
    }

    public function supprimer(int $id){
       if (\request()->ajax()){
           Banque::findOrFail($id)->delete();
           return response('Banque supprimé');
       }
       abort(404);
    }
}
