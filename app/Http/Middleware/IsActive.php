<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!Auth::user()->status) {
            return redirect()->route('web.index');
        }

        return $next($request);
    }
}
