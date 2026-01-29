<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'achat.controler',
                'guard_name' => 'web',
            ],
        ];

        $permissionIds = [];
        foreach ($permissions as $permission) {
            // Find or create the permission
            $perm = DB::table('permissions')->where('name', $permission['name'])->first();
            if (!$perm) {
                $id = DB::table('permissions')->insertGetId($permission);
            } else {
                $id = $perm->id;
            }
            $permissionIds[] = $id;
        }

        // Find or create the admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if (!$adminRole) {
            $adminRoleId = DB::table('roles')->insertGetId([
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $adminRoleId = $adminRole->id;
        }

        foreach ($permissionIds as $permissionId) {
            $exists = DB::table('role_has_permissions')
                ->where('permission_id', $permissionId)
                ->where('role_id', $adminRoleId)
                ->exists();

            if (!$exists) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $adminRoleId
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
