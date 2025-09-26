<?php

namespace App\Http\Controllers;

use App\Models\Marque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class MarqueController extends Controller
{
    public function liste()
    {
        $this->guard_custom(['marque.liste']);
        if (\request()->ajax()){
            $query = Marque::all();
            $table = DataTables::of($query);
            $table->addColumn(
                'selectable_td',
                function ($row) {
                    $id = $row['id'];
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            )->addColumn('actions',function ($row){
                $crudRoutePart = 'marques';
                $id = $row->id;
                $delete = 'supprimer';
                $edit_modal = ['url'=>route('marques.modifier',$row->id),'modal_id'=>'edit-marque-modal'];
               return view('partials.__datatable-action',compact('id','edit_modal','delete','crudRoutePart'));
            });
            $table->rawColumns(['actions','selectable_td']);
            return $table->make();
        }
        return view('marque.liste');
    }

    public function sauvegarder(Request $request)
    {
           $this->guard_custom(['marque.sauvegarder']);
           $request->validate([
               'nom' => ['required','string','max:255','unique:marques,nom'],
           ]);
           $o_marque = Marque::create([
               'nom' => $request->get('nom')
           ]);
           if ($request->ajax()){
               return  $o_marque;
           }
           session()->flash('success','Marque ajouté !');
           return redirect()->route('marques.liste');

    }


    public function modifier(Request $request, $id)
    {
      if ($request->ajax()){
          $request->validate([
              'id'=>'exists:marques,id'
          ]);
          $o_maruqe = Marque::find($id);
          return view('marque.partials.modifier_modal',compact('o_maruqe'));
      }
      abort('404');
    }

    public function mettre_a_jour(Request $request, $id){
           $validation = Validator::make($request->all(),[
               'id'=>'required,exists:marques,id',
               'nom' => ['required','string','max:255','unique:marques,nom,'.$id],
           ]);
           if ($validation->fails()) {
               $messaget = '';
               foreach ($validation->messages()->getMessages() as $message) {
                   $messaget .= $message[0] . '<br>';
               }
               session()->flash('warning',$messaget);
               return redirect()->route('marques.liste');
           }
           $o_marque = Marque::find($id);
           $o_marque->update([
               'nom' => $request->get('nom')
           ]);

           session()->flash('success','Marque mise à jour !');
           return redirect()->route('marques.liste');
    }

    public function supprimer(Marque $id)
    {
      if (\request()->ajax()){
          $id->delete();

          return response('Marque supprimé');
      }
      abort('404');
    }
}
