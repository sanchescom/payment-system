<?php

namespace App\Providers;

use App\Services\AccountProcessor;
use App\Services\CurrencyCollector;
use App\Services\CurrencyConverter;
use App\Services\PaymentOperationWriter;
use Illuminate\Support\ServiceProvider;
use SplTempFileObject;

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
            CurrencyConverter::class,
            function () {
                return new CurrencyConverter();
            }
        );
        $this->app->bind(
            AccountProcessor::class,
            function () {
                return new AccountProcessor();
            }
        );
        $this->app->bind(
            CurrencyCollector::class,
            function () {
                return new CurrencyCollector();
            }
        );
        $this->app->bind(
            PaymentOperationWriter::class,
            function () {
                return new PaymentOperationWriter(
                    new SplTempFileObject()
                );
            }
        );

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
