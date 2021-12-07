<?php

namespace Mtsung\TwddLocation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use pcrov\JsonReader\JsonReader;

class MakeTwDistrictRange extends Command
{
    protected $signature = 'twdd-location:make-tw-district-range';
    protected $description = '產生鄉鎮區範圍';
    private $minLat;
    private $minLng;
    private $maxLat;
    private $maxLng;

    public function __construct()
    {
        parent::__construct();
        $this->resetLoction();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $outputPath = Config::get('twdd-location.output.tw_district_range');
        $cityPath = Config::get('twdd-location.input.district_geojson');

        $reader = new JsonReader();
        $reader->open($cityPath);
        $reader->read('features');
        $depth = $reader->depth();
        $reader->read();
        $out = [];
        do {
            $feature = $reader->value();

            $this->resetLoction();
            $coordinates = $feature['geometry']['coordinates'];
            if (is_null($coordinates)) {
                continue;
            }

            $temp = [];
            $temp['city_code'] = str_pad($feature['properties']['COUNTYCODE'], 5, "0", STR_PAD_LEFT);
            $temp['city_name'] = $feature['properties']['COUNTYNAME'];
            $temp['district_code'] = str_pad($feature['properties']['TOWNCODE'], 8, "0", STR_PAD_LEFT);
            $temp['district_name'] = $feature['properties']['TOWNNAME'];

            if ($feature['geometry']['type'] === 'MultiPolygon') {
                foreach ($coordinates as $coordinate) {
                    foreach ($coordinate as $mix) {
                        foreach ($mix as $latLng) {
                            $this->updateLocation($latLng);
                        }
                    }
                }
            } else {
                foreach ($coordinates as $coordinate) {
                    foreach ($coordinate as $latLng) {
                        $this->updateLocation($latLng);
                    }
                }
            }

            $temp['limit_location'] = $this->getLimitLocation();

            $out[] = $temp;

        } while ($reader->next() && $reader->depth() > $depth);

        file_put_contents($outputPath, json_encode($out, JSON_UNESCAPED_UNICODE));
        $reader->close();
    }

    private function resetLoction()
    {
        $this->minLat = PHP_INT_MAX;
        $this->minLng = PHP_INT_MAX;
        $this->maxLat = PHP_INT_MIN;
        $this->maxLng = PHP_INT_MIN;
    }

    private function updateLocation($latLng)
    {
        $this->minLat = min($this->minLat, $latLng[1]);
        $this->minLng = min($this->minLng, $latLng[0]);
        $this->maxLat = max($this->maxLat, $latLng[1]);
        $this->maxLng = max($this->maxLng, $latLng[0]);
    }

    private function getLimitLocation()
    {
        return [
            'min_lat' => $this->minLat,
            'min_lng' => $this->minLng,
            'max_lat' => $this->maxLat,
            'max_lng' => $this->maxLng,
        ];
    }
}
