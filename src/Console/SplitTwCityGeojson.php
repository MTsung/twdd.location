<?php

namespace Mtsung\TwddLocation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use pcrov\JsonReader\JsonReader;

class SplitTwCityGeojson extends Command
{
    protected $signature = 'twdd-location:split-tw-city-geojson';
    protected $description = '分割縣市 geojson';

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
        $outputPath = Config::get('twdd-location.output_path.tw_city_path');
        $cityPath = Config::get('twdd-location.input.city_geojson');

        $reader = new JsonReader();
        $reader->open($cityPath);
        $reader->read("features");
        $depth = $reader->depth();
        $reader->read();
        do {
            $feature = $reader->value();
            $coordinates = $feature['geometry']['coordinates'];
            if (is_null($coordinates)) {
                continue;
            }

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
                            'city_code' => str_pad($feature['properties']['行政區域代碼'], 5, "0", STR_PAD_LEFT),
                            'city_name' => $feature['properties']['名稱'],
                        ],
                    ],
                ],
            ];

            Storage::put($outputPath . '/' . $out['features'][0]['properties']['city_code'] . '.geojson', json_encode($out, JSON_UNESCAPED_UNICODE));
        } while ($reader->next() && $reader->depth() > $depth);

        $reader->close();

        return Command::SUCCESS;
    }
}
