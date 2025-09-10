<?php

namespace Jhonoryza\LaravelModelHistory;

use Illuminate\Support\ServiceProvider as SupportServiceProvider;
use Jhonoryza\LaravelModelHistory\Command\Generator;

class ServiceProvider extends SupportServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Generator::class,
            ]);
        }
    }
}
