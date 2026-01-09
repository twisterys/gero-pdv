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
                'name' => 'transfert.controler',
                'guard_name' => 'web',
            ],
            [
                'name' => 'depense.controler',
                'guard_name' => 'web',
            ],
            [
                'name' => 'rebut.controler',
                'guard_name' => 'web',
            ],
        ];

        $permissionIds = [];
        foreach ($permissions as $permission) {
            // Check if the permission already exists
            $existingPermission = DB::table('permissions')->where('name', $permission['name'])->first();

            if ($existingPermission) {
                // If exists, add its ID to the array
                $permissionIds[] = $existingPermission->id;
            } else {
                // If doesn't exist, insert it and get the ID
                $id = DB::table('permissions')->insertGetId($permission);
                $permissionIds[] = $id;
            }
        }

        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($permissionIds as $permissionId) {
                $exists = DB::table('role_has_permissions')
                    ->where('permission_id', $permissionId)
                    ->where('role_id', $adminRole->id)
                    ->exists();

                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'role_id' => $adminRole->id
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = ['transfert.controler', 'depense.controler', 'rebut.controler'];
        $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
