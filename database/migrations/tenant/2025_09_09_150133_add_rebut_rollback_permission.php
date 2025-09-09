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
        $permissions = [
            [
                'name' => 'rebut.rollback',
                'guard_name' => 'web',
            ],
        ];

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

        // Find the admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();

        if ($adminRole) {
            // Assign the permissions to the admin role
            foreach ($permissionIds as $permissionId) {
                // Check if the role-permission relationship already exists
                $exists = DB::table('role_has_permissions')
                    ->where('permission_id', $permissionId)
                    ->where('role_id', $adminRole->id)
                    ->exists();

                // Only create the relationship if it doesn't exist
                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'role_id' => $adminRole->id
                    ]);
                }
            }
        }
        if ($superAdminRole){
            foreach ($permissionIds as $permissionId) {
                $exists = DB::table('role_has_permissions')->where('permission_id', $permissionId)->where('role_id', $superAdminRole->id)->exists();
                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'role_id' => $superAdminRole->id,
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

        // Get the permission IDs
        $permissionIds = DB::table('permissions')
            ->whereIn('name', [
                'rebut.rollback',
            ])
            ->pluck('id')
            ->toArray();

        // Remove the role-permission relationships
        if (!empty($permissionIds)) {
            DB::table('role_has_permissions')
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }

        // Remove the permissions
        DB::table('permissions')
            ->whereIn('name', [
                'rebut.rollback',
            ])
            ->delete();



    }

};
