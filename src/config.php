<?php
return [
    'input' => [
        'city_geojson' => __DIR__ . '/RawData/COUNTY_MOI_1090820.geojson',
        'district_geojson' => __DIR__ . '/RawData/TOWN_MOI_1100415.geojson',
    ],
    'output' => [
        'tw_city_range' => '/twdd-location/tw_city_range.json',
        'tw_district_range' => '/twdd-location/tw_district_range.json',
    ],
];
