<?php

namespace Mtsung\TwddLocation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use pcrov\JsonReader\JsonReader;

class MakeTwCityRange extends Command
{
    protected $signature = 'twdd-location:make-tw-city-range';
    protected $description = '產生縣市範圍';
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
        $outputPath = Config::get('twdd-location.output.tw_city_range');
        $cityPath = Config::get('twdd-location.input.city_geojson');

        $reader = new JsonReader();
        $reader->open($cityPath);
        $reader->read('features');
        $depth = $reader->depth();
        $reader->read();
        $out = [];
        do {
            $feature = $reader->value();

            $this->resetLoction();

            if (is_null($feature['geometry'])) {
                continue;
            }
            $coordinates = $feature['geometry']['coordinates'];

            $temp = [];
            $temp['city_code'] = str_pad($feature['properties']['行政區域代碼'], 5, "0", STR_PAD_LEFT);
            $temp['city_name'] = $feature['properties']['名稱'];

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

        Storage::put($outputPath, json_encode($out, JSON_UNESCAPED_UNICODE));
        $reader->close();

        return Command::SUCCESS;
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
