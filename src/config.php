<?php
return [
    'input' => [
        'city_geojson' => __DIR__ . '/RawData/COUNTY_MOI_1090820.geojson',
        'district_geojson' => __DIR__ . '/RawData/TOWN_MOI_1100415.geojson',
        'zipcode_json' => __DIR__ . '/RawData/zipcode.json',
    ],
    'output' => [
        'tw_city_range' => __DIR__ . '/OutputData/tw_city_range.json',
        'tw_district_range' => __DIR__ . '/OutputData/tw_district_range.json',
    ],
    'output_path' => [
        'tw_city_path' =>  __DIR__ . '/OutputData/city',
        'tw_district_path' =>  __DIR__ . '/OutputData/district',
    ],
];
