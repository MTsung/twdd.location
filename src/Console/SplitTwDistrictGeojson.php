<?php

namespace Mtsung\TwddLocation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mtsung\TwddLocation\Facade\TwddDistrict;
use pcrov\JsonReader\JsonReader;

class SplitTwDistrictGeojson extends Command
{
    protected $signature = 'twdd-location:split-tw-district-geojson';
    protected $description = '分割鄉鎮區 geojson';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $outputPath = Config::get('twdd-location.output_path.tw_district_path');
        $districtPath = Config::get('twdd-location.input.district_geojson');

        $reader = new JsonReader();
        $reader->open($districtPath);
        $reader->read("features");
        $depth = $reader->depth();
        $reader->read();
        do {
            $feature = $reader->value();
            $coordinates = $feature['geometry']['coordinates'];
            if (is_null($coordinates)) {
                continue;
            }

            $districtCode = str_pad($feature['properties']['TOWNCODE'], 8, "0", STR_PAD_LEFT);
            $zipCode = TwddDistrict::getInfoByDistrictCode($districtCode)['zip_code'] ?? 0;
            $out = [
                'type' => 'FeatureCollection',
                'features' => [
                    [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => $feature['geometry']['type'],
                            'coordinates' => $coordinates,
                        ],
                        'properties' => [
                            'city_code' => str_pad($feature['properties']['COUNTYCODE'], 5, "0", STR_PAD_LEFT),
                            'city_name' => $feature['properties']['COUNTYNAME'],
                            'district_code' => $districtCode,
                            'district_name' => $feature['properties']['TOWNNAME'],
                            'zip_code' => $zipCode,
                        ],
                    ],
                ],
            ];

            Storage::put($outputPath . '/' . $out['features'][0]['properties']['district_code'] . '.geojson', json_encode($out, JSON_UNESCAPED_UNICODE));
        } while ($reader->next() && $reader->depth() > $depth);

        $reader->close();
    }
}
