<?php
namespace Mtsung\TwddLocation\Helper;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Location\Coordinate;
use Location\Polygon;
use pcrov\JsonReader\JsonReader;

class TwddLocation
{
    /**
     * 取得縣市資訊
     *
     * @param [type] $lat
     * @param [type] $lng
     * @return void
     */
    public function getCity(float $lat, float $lng)
    {
        $citys = $this->getPossibleCitys($lat, $lng);
        $searchLocation = new Coordinate($lat, $lng);
        return $this->searchProperties($searchLocation, $citys, 'city');
    }

    /**
     * 取得縣市鄉鎮區資訊
     *
     * @param [type] $lat
     * @param [type] $lng
     * @return void
     */
    public function getDistrict(float $lat, float $lng)
    {
        $districts = $this->getPossibleDistricts($lat, $lng);
        $searchLocation = new Coordinate($lat, $lng);
        return $this->searchProperties($searchLocation, $districts, 'district');
    }

    private function searchProperties($searchLocation, $data, $type)
    {
        foreach ($data as $item) {
            $fileName = Storage::path(Config::get('twdd-location.output_path.tw_' . $type . '_path') . '/' . $item->{$type . '_code'} . '.geojson');
            if (!is_file($fileName)) {
                throw new \Exception('Please run php artisan twdd-location:split-tw-' . $type . '-geojson');
            }

            $reader = new JsonReader();
            $reader->open($fileName);

            $reader->read("features");
            $depth = $reader->depth();
            $reader->read();
            do {
                $feature = $reader->value();
                if ($feature['geometry']['type'] === 'MultiPolygon') {
                    $multiCoordinates = $feature['geometry']['coordinates'];
                    foreach ($multiCoordinates as $coordinates) {
                        if ($this->locationInCoordinate($searchLocation, $coordinates)) {
                            return $feature['properties'];
                        }
                    }
                } else if ($feature['geometry']['type'] === 'Polygon') {
                    $coordinates = $feature['geometry']['coordinates'];
                    if ($this->locationInCoordinate($searchLocation, $coordinates)) {
                        return $feature['properties'];
                    }
                }
            } while ($reader->next() && $reader->depth() > $depth);

            $reader->close();
        }
        return null;
    }

    private function locationInCoordinate($searchLocation, $coordinates): bool
    {
        $check = true;
        foreach ($coordinates as $i => $coordinate) {
            $geofence = new Polygon();
            foreach ($coordinate as $latLng) {
                $geofence->addPoint(new Coordinate($latLng[1], $latLng[0]));
            }

            // 第一個是包含 其餘皆為不包含
            if ($i == 0) {
                $check &= $geofence->contains($searchLocation);
            } else {
                $check &= !$geofence->contains($searchLocation);
            }

            // 已經 false 就提早 return
            if (!$check) {
                return $check;
            }
        }
        return $check;
    }

    /**
     * 取得可能的縣市
     *
     * @param [type] $lat
     * @param [type] $lng
     * @return Collection
     */
    private function getPossibleCitys($lat, $lng): Collection
    {
        $fileName = Config::get('twdd-location.output.tw_city_range');
        if (!Storage::has($fileName)) {
            throw new \Exception('Please run php artisan twdd-location:make-tw-city-range');
        }

        $twCityRange = Storage::get($fileName);
        $twCityRange = json_decode($twCityRange);
        $twCityRange = collect($twCityRange);
        return $twCityRange
            ->where('limit_location.min_lat', '<=', $lat)
            ->where('limit_location.min_lng', '<=', $lng)
            ->where('limit_location.max_lat', '>=', $lat)
            ->where('limit_location.max_lng', '>=', $lng)
            ->sortByDesc('city_name');
    }

    /**
     * 取得可能的鄉鎮
     *
     * @param [type] $lat
     * @param [type] $lng
     * @return Collection
     */
    private function getPossibleDistricts($lat, $lng): Collection
    {
        $fileName = Config::get('twdd-location.output.tw_district_range');
        if (!Storage::has($fileName)) {
            throw new \Exception('Please run php artisan twdd-location:make-tw-district-range');
        }

        $twDistrictRange = Storage::get($fileName);
        $twDistrictRange = json_decode($twDistrictRange);
        $twDistrictRange = collect($twDistrictRange);
        return $twDistrictRange
            ->where('limit_location.min_lat', '<=', $lat)
            ->where('limit_location.min_lng', '<=', $lng)
            ->where('limit_location.max_lat', '>=', $lat)
            ->where('limit_location.max_lng', '>=', $lng);
    }
}
