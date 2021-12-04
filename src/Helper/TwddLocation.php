<?php
namespace Mtsung\TwddLocation\Helper;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class TwddLocation
{
    public function __construct()
    {
        
    }

    /**
     * 取得可能的縣市
     *
     * @param [type] $lat
     * @param [type] $lng
     * @return Collection
     */
    public function getPossibleCitys($lat, $lng): Collection
    {
        $twCityRange = Storage::get(Config::get('twdd-location.output.tw_city_range'));
        $twCityRange = json_decode($twCityRange);
        $twCityRange = collect($twCityRange);
        return $twCityRange
            ->where('limit_location.min_lat', '<=', $lat)
            ->where('limit_location.min_lng', '<=', $lng)
            ->where('limit_location.max_lat', '>=', $lat)
            ->where('limit_location.max_lng', '>=', $lng);
    }
}
