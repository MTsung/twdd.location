<?php
namespace Mtsung\TwddLocation\Helper;

use Illuminate\Support\Facades\Config;

class TwddDistrict
{
    private $data;

    public function __construct()
    {
        $this->loadJson();
    }

    public function getInfoByDistrictCode($districtCode)
    {
        return $this->format($this->data->where('TOWNCODE', $districtCode)->first());
    }

    public function getInfoByZipCode($zipCode)
    {
        return $this->format($this->data->where('ZIPCODE', $zipCode)->first());
    }

    private function format($d)
    {
        if (is_null($d)) {
            return null;
        }

        return [
            'city_code' => str_pad($d->COUNTYCODE, 5, "0", STR_PAD_RIGHT),
            "city_name" => $d->COUNTYNAME,
            'district_code' => $d->TOWNCODE,
            'district_name' => $d->TOWNNAME,
            'zip_code' => $d->ZIPCODE,
        ];
    }

    private function loadJson()
    {
        $fileName = Config::get('twdd-location.input.zipcode_json');
        $this->data = collect(json_decode(file_get_contents($fileName)));
    }
}
