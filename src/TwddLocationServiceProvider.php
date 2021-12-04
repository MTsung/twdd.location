<?php

namespace Mtsung\TwddLocation;

use Illuminate\Support\ServiceProvider;
use Mtsung\TwddLocation\Console\MakeTwCityRange;

class TwddLocationServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeTwCityRange::class,
            ]);
        }
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'twdd-location');
    }
}
