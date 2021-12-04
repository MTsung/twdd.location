<?php
namespace Mtsung\TwddLocation\Facade;

use Illuminate\Support\Facades\Facade;

class TwddLocation extends Facade
{
    protected static function getFacadeAccessor() { return \Mtsung\TwddLocation\Helper\TwddLocation::class; }
}