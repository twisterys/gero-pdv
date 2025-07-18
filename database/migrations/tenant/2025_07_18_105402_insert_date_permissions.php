<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


    protected $permissions = [
        'vente.date',
        'achat.date',
        'paiement.date',
        'depense.date'
    ];
    public function up()
    {
        foreach ($this->permissions as $permission) {
            $exists =\Illuminate\Support\Facades\DB::table('permissions')->where('name', $permission)->exists();
            if (!$exists){
                \Illuminate\Support\Facades\DB::table('permissions')->insert([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
                $ids = \Illuminate\Support\Facades\DB::table('permissions')->whereIn('name',['vente.*','achat.*','paiement.*','depense.*'] )->pluck('id');
                \Illuminate\Support\Facades\DB::table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
            }
        }
    }

    public function down()
    {
        foreach ($this->permissions as $permission) {
            \Illuminate\Support\Facades\DB::table('permissions')->where('name', $permission)->delete();
        }
    }
};
