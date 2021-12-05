# 台灣位置資訊工具

## Install

1. run `composer require mtsung/twdd.location`
2. run `php artisan twdd-location:make-tw-city-range`
3. run `php artisan twdd-location:make-tw-district-range`
4. run `php artisan twdd-location:split-tw-city-geojson`
5. run `php artisan twdd-location:split-tw-district-geojson`
6. run test `vendor/bin/phpunit .\vendor\mtsung\twdd.location\tests\TestLocation.php`

## 使用方式

### 座標轉地區

```php
use Mtsung\TwddLocation\Facade\TwddLocation;

$res = TwddLocation::getCity(25.03375, 121.564879);
/*
array:2 [
  "city_code" => "63000"
  "city_name" => "臺北市"
]
*/

$res = TwddLocation::getDistrict(25.077741, 121.574829);
/*
array:4 [
  "city_code" => "63000"
  "city_name" => "臺北市"
  "district_code" => "63000100"
  "district_name" => "內湖區"
  "zip_code" => "114"
]
*/

$res = TwddLocation::getCity(34.92475,135.79863);
// null

$res = TwddLocation::getDistrict(39.256422, 140.986340);
// null
```

### zip code 或 district code 查詢
```php
use Mtsung\TwddLocation\Facade\TwddDistrict;

$res = TwddDistrict::getInfoByZipCode(238);
/*
array:5 [
  "city_code" => "65000"
  "city_name" => "新北市"
  "district_code" => "65000070"
  "district_name" => "樹林區"
  "zip_code" => "238"
]
*/

$res = TwddDistrict::getInfoByZipCode(114);
/*
array:5 [
  "city_code" => "63000"
  "city_name" => "臺北市"
  "district_code" => "63000100"
  "district_name" => "內湖區"
  "zip_code" => "114"
]
*/

$res = TwddDistrict::getInfoByZipCode(9999);
// null

$res = TwddDistrict::getInfoByDistrictCode(67000300);
/*
array:5 [
  "city_code" => "67000"
  "city_name" => "臺南市"
  "district_code" => "67000300"
  "district_name" => "龍崎區"
  "zip_code" => "719"
]
*/

$res = TwddDistrict::getInfoByDistrictCode(9999);
// null

```

## 資料來源
[直轄市、縣市界線](https://data.gov.tw/dataset/7442)

[鄉鎮市區界線](https://data.gov.tw/dataset/7441)

[3碼郵遞區號](https://data.gov.tw/dataset/37759)

## License
[MIT license](https://opensource.org/licenses/MIT)