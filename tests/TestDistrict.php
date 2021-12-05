<?php

use Mtsung\TwddLocation\Facade\TwddDistrict;
use Tests\TestCase;

class TestDistrict extends TestCase
{
    public function test()
    {
        $res = TwddDistrict::getInfoByZipCode(238);
        $this->assertEquals($res['zip_code'], 238);

        $res = TwddDistrict::getInfoByZipCode(114);
        $this->assertEquals($res['zip_code'], 114);

        $res = TwddDistrict::getInfoByZipCode(9999);
        $this->assertNull($res);

        $res = TwddDistrict::getInfoByDistrictCode(67000300);
        $this->assertEquals($res['zip_code'], 719);

        $res = TwddDistrict::getInfoByDistrictCode(9999);
        $this->assertNull($res);
    }

}
