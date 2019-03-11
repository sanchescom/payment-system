<?php

namespace App\Providers;

use App\Services\AccountProcessor;
use App\Services\CurrencyCollector;
use App\Services\CurrencyConverter;
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
        //
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'CurrencyConverter',
            function () {
                return new CurrencyConverter();
            }
        );
        $this->app->bind(
            'AccountProcessor',
            function () {
                return new AccountProcessor();
            }
        );
        $this->app->bind(
            'CurrencyCollector',
            function () {
                return new CurrencyCollector();
            }
        );

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
