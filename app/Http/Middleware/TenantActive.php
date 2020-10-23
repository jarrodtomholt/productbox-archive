<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        abort_unless(tenant()->active, 404);

        return $next($request);
    }
}
