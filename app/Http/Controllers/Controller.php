<?php

namespace App\Http\Controllers;

use App\Services\PermissionsService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    function guard_custom(array $permissions)
    {
        PermissionsService::guard_custom($permissions);
    }
}
