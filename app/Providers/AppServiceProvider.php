<?php

namespace App\Providers;

use App\Order;
use App\Message;
use App\PaymentRequest;
use Laravel\Horizon\Horizon;
use App\Observers\OrderObserver;
use App\Observers\MessageObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Observers\PaymentRequestObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Fixing for RDS old version
        Schema::defaultStringLength(191);

        Horizon::auth(function ($request) {
            return auth()->check() && auth()->user()->is_admin;
        });

        Message::observe(MessageObserver::class);
        Order::observe(OrderObserver::class);
        PaymentRequest::observe(PaymentRequestObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
