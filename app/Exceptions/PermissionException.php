<?php

namespace App\Exceptions;

use Exception;

class PermissionException extends Exception
{
    function render(): \Illuminate\Http\RedirectResponse
    {
        abort(403);
    }
}
