<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

        public function up(): void
    {

        DB::table('permissions')->insert([
            'name' => 'parametres.relance',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roles = DB::table('roles')
            ->whereIn('name', ['admin', 'super_admin'])
            ->pluck('id');

        $permissionId = DB::table('permissions')
            ->where('name', 'parametres.relance')
            ->value('id');

        foreach ($roles as $roleId) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permissionId,
                'role_id' => $roleId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionId = DB::table('permissions')
            ->where('name', 'parametres.relance')
            ->value('id');

        DB::table('role_has_permissions')
            ->where('permission_id', $permissionId)
            ->delete();

        DB::table('permissions')
            ->where('id', $permissionId)
            ->delete();
    }
};
