<?php

namespace App\Http\Middleware;

use App\Services\ModuleService;
use Closure;
use Illuminate\Http\Request;

class ActiveModuleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $type = $request->route('type');
        $modules = ModuleService::getModules();
        if (!in_array($type,$modules->where('active',1)->pluck('type')->toArray())){
            abort(404);
        }
        return $next($request);
    }
}
