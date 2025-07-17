<?php

namespace App\Http\Middleware;

use App\Services\LimiteService;
use Closure;
use Illuminate\Http\Request;

class ApiSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->get('limite')){
            LimiteService::generate_limite_session();
        }
        return $next($request);
    }


}
