<?php

namespace App\Http\Controllers\Api\management;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\LogService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function liste($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            $users = $tenant->run(function () {
                return \DB::table('users')
                    ->select('id', 'name', 'email', 'role', 'disabled')
                    ->get();
            });

            return response()->json(['users' => $users], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la récupération des utilisateurs'], 500);
        }
    }

    public function afficher($tenantId, $userId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);

            $user = $tenant->run(function () use ($userId) {
                return \DB::table('users')
                    ->select('id', 'name', 'email', 'role', 'disabled')
                    ->where('id', $userId)
                    ->first();
            });

            if (!$user) {
                return response()->json(['message' => 'Utilisateur non trouvé'], 404);
            }

            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la récupération de l\'utilisateur'], 500);
        }
    }

    public function activer(Request $request, $tenantId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);

            $tenant->run(function () use ($request) {
                \DB::table('users')
                    ->where('id', $request->input('id'))
                    ->update(['disabled' => false]);
            });

            return response()->json(['message' => 'Utilisateur activé avec succès'], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de l\'activation de l\'utilisateur'], 500);
        }
    }

    public function desactiver(Request $request, $tenantId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);

            $tenant->run(function () use ($request) {
                \DB::table('users')
                    ->where('id', $request->input('id'))
                    ->update(['disabled' => true]);
            });

            return response()->json(['message' => 'Utilisateur désactivé avec succès'], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la désactivation de l\'utilisateur'], 500);
        }
    }


    public function modifier_mot_de_passe(Request $request, $tenantId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);

            $tenant->run(function () use ($request) {
                \DB::table('users')
                    ->where('id', $request->input('id'))
                    ->update(['password' => \Hash::make($request->input('password'))]);
            });

            return response()->json(['message' => 'Mot de passe modifié avec succès'], 200);
        } catch (\Exception $e) {
            LogService::logException($e);
            return response()->json(['message' => 'Erreur lors de la modification du mot de passe'], 500);
        }
    }


}

