<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictManagementAccess
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
        if (!$request->user() || !$request->user()->tokenCan(sprintf('manage:%s', tenant('id')))) {
            return abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}
