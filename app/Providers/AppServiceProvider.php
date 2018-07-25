<?php

namespace App\Providers;

use App\Message;
use App\Observers\MessageObserver;
use App\Observers\OrderObserver;
use App\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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

        Message::observe(MessageObserver::class);
        Order::observe(OrderObserver::class);
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
