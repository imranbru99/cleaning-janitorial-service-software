<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancy
{
    public function __construct(protected TenantManager $tenants) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->tenants->initialize($request);

        return $next($request);
    }
}
