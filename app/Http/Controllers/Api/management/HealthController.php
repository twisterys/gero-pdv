<?php

namespace App\Http\Controllers\Api\management;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;


class HealthController extends Controller
{
    /**
     * Check the health of the application.
     *
     * @return JsonResponse
     */
    public function version_info()
    {
        $version = trim(shell_exec('git describe --tags --abbrev=0'));
        $commitMessage = trim(shell_exec('git log -1 --pretty=%B'));
        preg_match('/\[desc:(.*?)\]/', $commitMessage, $matches);
        $description = isset($matches[1]) ? trim($matches[1]) : null;
        $commitMessage = preg_replace('/\[desc:.*?\]/', '', $commitMessage);
        return response()->json([
            'version' => $version,
            'commit_message' => trim($commitMessage),
            'description' => $description,
        ]);
    }

    /**
     * Check if the application is up.
     *
     * @return JsonResponse
     */
    public function up()
    {
        return response()->json([
            'status' => 'up',
            'message' => 'L\'application est en ligne.',
        ]);
    }


    public function instance_status($id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json(['error' => 'Tenant introuvable'], 404);
        }
        return response()->json([
            'id' => $id,
            'status' => $tenant->status,
        ]);
    }

}

