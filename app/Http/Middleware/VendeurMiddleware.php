<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VendeurMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->hasRole('vendeur')){
            if (!in_array(str_replace('/','',$request->getRequestUri()),['point-de-vente','session-point-de-vente','point-de-ventedemandes'])){
               return redirect()->route('pos');
            }
        }
        return $next($request);
    }
}
