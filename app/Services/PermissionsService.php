<?php

namespace App\Services;

use App\Exceptions\PermissionException;

class PermissionsService
{

    /**
     * @param array $permissions
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws PermissionException
     */
    public static function guard_custom(array $permissions){
            if (!request()->user()->can($permissions)){
               throw new PermissionException();
        }
    }
}
