<?php

namespace App\Http\Middleware;

use App\Services\Modules\ModuleManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function __construct(protected ModuleManager $modules) {}

    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! $this->modules->isActive($module)) {
            abort(403, "Module [{$module}] is not active for this business.");
        }

        return $next($request);
    }
}
