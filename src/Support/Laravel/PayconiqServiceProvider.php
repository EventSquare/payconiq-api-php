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
        $this->publishes([
            __DIR__.'/PayconiqSettings.php' => config_path('payconiq.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('payconiq', function() { return new \Payconiq\Client(config('payconiq.merchant_id'), config('payconiq.access_token')); });
    }
}
