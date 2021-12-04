<?php

namespace Mtsung\TwddLocation;

use Illuminate\Support\ServiceProvider;
use Mtsung\TwddLocation\Console\MakeTwCityRange;
use Mtsung\TwddLocation\Console\MakeTwDistrictRange;
use Mtsung\TwddLocation\Console\SplitTwCityGeojson;
use Mtsung\TwddLocation\Console\SplitTwDistrictGeojson;

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
                SplitTwCityGeojson::class,
                SplitTwDistrictGeojson::class,
            ]);
        }
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'twdd-location');
    }
}
