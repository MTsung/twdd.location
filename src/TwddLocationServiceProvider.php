<?php

namespace Mtsung\TwddLocation;

use Illuminate\Support\ServiceProvider;
use Mtsung\TwddLocation\Console\MakeTwCityRange;
use Mtsung\TwddLocation\Console\MakeTwDistrictRange;

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
                MakeTwDistrictRange::class,
            ]);
        }
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'twdd-location');
    }
}
