<?php

namespace Payconiq\Support\Laravel;

use Illuminate\Support\ServiceProvider;
use App;

class PayconiqServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('payconiq', function() { return new \Payconiq\Client; });
    }
}
