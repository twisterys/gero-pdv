<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function liste(){
        $this->guard_custom(['permission.liste']);
        if (\request()->ajax()){
            $query = Role::whereNot('name','super_admin')->get();
            $table = \DataTables::of($query);
            $table->addColumn(
                'selectable_td',
                function ($row) {
                    $id = $row['id'];
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                }
            );
            $table->addColumn('actions',function ($row){
                $crudRoutePart = 'permissions';
                $edit = 'modifier';
                $id = $row->id;
                return view('partials.__datatable-action',compact('crudRoutePart','edit','id'));
            });
            $table->rawColumns(['selectable_td']);
            return  $table->make();
        }
        return view('permissions.liste');
    }
    public function ajouter(){
        $this->guard_custom(['permission.sauvegarder']);
        $permissions = Permission::all();
//        $wild_cards = array_map(function ($item){
//            if (str_contains($item,'.*') || !str_contains($item,'.'))
//                return $item;
//        },$permissions);
        $wild_cards = $permissions->map(function ($item){
            if (str_contains($item->name,'.*') || !str_contains($item->name,'.')){
                return explode('.',$item->name)[0];
            }
        })->reject(function ($item){
            return empty($item);
        })->toArray();
        return view('permissions.ajouter', compact('wild_cards','permissions'));
    }

    public function sauvegarder(Request $request){
        $this->guard_custom(['permission.sauvegarder']);

        $request->validate([
            'nom'=>'unique:roles,name'
        ]);
        $role = Role::create(['name'=>$request->get('nom')]);
        $permissions = $request->except('_token','nom');
        $permissions = array_keys($permissions);
        $permissions = array_map(function ($permission){
            if (str_contains($permission,'__')){
                return str_replace('__','.',$permission);
            }
            return $permission.'.*';
        },$permissions);
        $role->givePermissionTo($permissions);
        session()->flash('success','Role ajouté');
        return redirect()->route('permissions.liste');
    }

    public function modifier(Request $request, $id){
        $o_role = Role::findOrFail($id);
        $permissions = Permission::all();
        $wild_cards = $permissions->map(function ($item){
            if (str_contains($item->name,'.*') || !str_contains($item->name,'.')){
                return explode('.',$item->name)[0];
            }
        })->reject(function ($item){
            return empty($item);
        })->toArray();
        $o_role_permissions = $o_role->getAllPermissions()->map(function ($item){
            if (str_contains($item->name,'.*') || !str_contains($item->name,'.')){
                return explode('.',$item->name)[0];
            }else {
                return  $item->name;
            }
        })->toArray();
        return view('permissions.modifier',compact('o_role','permissions','o_role_permissions','wild_cards'));
    }

    public function mettre_a_jour(Request $request,$id){
        $this->guard_custom(['permission.mettre_a_jour']);

        $request->validate([
            'nom'=>'unique:roles,name,'.$id
        ]);
        $role = Role::find($id);
        $role->update(['name' => $request->get('nom')]);
        $permissions = $request->except('_token','nom','_method');
        $permissions = array_keys($permissions);
        $permissions = array_map(function ($permission){
            if (str_contains($permission,'__')){
                return str_replace('__','.',$permission);
            }
            return $permission.'.*';
        },$permissions);
        $role->syncPermissions($permissions);
        session()->flash('success','Role mise à jour');
        return redirect()->route('permissions.liste');
    }
}
