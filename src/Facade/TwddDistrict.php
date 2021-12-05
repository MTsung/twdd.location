<?php
namespace Mtsung\TwddLocation\Facade;

use Illuminate\Support\Facades\Facade;

class TwddDistrict extends Facade
{
    protected static function getFacadeAccessor() { return \Mtsung\TwddLocation\Helper\TwddDistrict::class; }
}