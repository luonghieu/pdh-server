<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class LastActiveAt
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
        if (!Auth::check()) {
            return $next($request);
        }

        try {
            // store last_active_at to Redis
            $redis = Redis::connection();
            $redis->set('last_active_at_' . Auth::id(), now());
            $redis->set('is_online_' . Auth::id(), true);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            // store last_active_at to DB
            $user = Auth::user();
            $user->update([
                'last_active_at' => now(),
                'is_online' => 1,
            ]);
        }

        return $next($request);
    }
}
