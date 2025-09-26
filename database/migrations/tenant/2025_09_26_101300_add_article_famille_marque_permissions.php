<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            // Articles
            'article.*',
            'article.liste',
            'article.afficher',
            'article.sauvegarder',
            'article.mettre_a_jour',
            'article.supprimer',
            // Familles
            'famille.*',
            'famille.liste',
            'famille.sauvegarder',
            'famille.mettre_a_jour',
            'famille.supprimer',
            // Marques
            'marque.*',
            'marque.liste',
            'marque.sauvegarder',
            'marque.mettre_a_jour',
            'marque.supprimer',
        ];

        // Ensure permissions exist
        $now = now();
        foreach ($permissions as $name) {
            $exists = DB::table('permissions')
                ->where('name', $name)
                ->where('guard_name', 'web')
                ->exists();
            if (! $exists) {
                DB::table('permissions')->insert([
                    'name' => $name,
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Assign to admin role if it exists
        $adminRole = DB::table('roles')
            ->where('name', 'admin')
            ->where('guard_name', 'web')
            ->first();

        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissions)
                ->where('guard_name', 'web')
                ->pluck('id')
                ->all();

            foreach ($permissionIds as $pid) {
                $linkExists = DB::table('role_has_permissions')
                    ->where('role_id', $adminRole->id)
                    ->where('permission_id', $pid)
                    ->exists();
                if (! $linkExists) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $pid,
                        'role_id' => $adminRole->id,
                    ]);
                }
            }
        }

        // Optional: if you want to ensure the admin role exists uncomment below
        // if (! $adminRole) {
        //     DB::table('roles')->insert([
        //         'name' => 'admin',
        //         'guard_name' => 'web',
        //         'created_at' => $now,
        //         'updated_at' => $now,
        //     ]);
        // }

        // Note: spatie/permission caches permissions. Consider running:
        // php artisan cache:forget spatie.permission.cache
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'article.*', 'article.liste', 'article.afficher', 'article.sauvegarder', 'article.mettre_a_jour', 'article.supprimer',
            'famille.*', 'famille.liste', 'famille.sauvegarder', 'famille.mettre_a_jour', 'famille.supprimer',
            'marque.*', 'marque.liste', 'marque.sauvegarder', 'marque.mettre_a_jour', 'marque.supprimer',
        ];

        // Detach from admin role if present
        $adminRole = DB::table('roles')
            ->where('name', 'admin')
            ->where('guard_name', 'web')
            ->first();

        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissions)
                ->where('guard_name', 'web')
                ->pluck('id')
                ->all();

            if (!empty($permissionIds)) {
                DB::table('role_has_permissions')
                    ->where('role_id', $adminRole->id)
                    ->whereIn('permission_id', $permissionIds)
                    ->delete();
            }
        }

        // Delete the permissions
        DB::table('permissions')
            ->whereIn('name', $permissions)
            ->where('guard_name', 'web')
            ->delete();

        // Note: You may want to clear permission cache after rollback as well.
    }
};
