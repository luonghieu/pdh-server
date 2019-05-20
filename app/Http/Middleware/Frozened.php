<?php

namespace App\Http\Middleware;

use Closure;
use Twilio\Jwt\JWT;

class Frozened
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
        $user = \Auth::user();

        if ($user->is_verified == 1 && $user->status == 0 && !$request->is('admin/*'))
        {
            dd(123);
        }

        return $next($request);
    }
}
