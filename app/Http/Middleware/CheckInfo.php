<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckInfo
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

        if (empty($user->nickname) || empty($user->date_of_birth) || empty($user->avatars[0])) {
            return redirect()->route('web.index');
        }

        return $next($request);
    }
}
