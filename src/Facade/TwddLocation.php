<?php
namespace Mtsung\TwLocationToCity\Facade;

use Illuminate\Support\Facades\Facade;

class TwddLocation extends Facade
{
    protected static function getFacadeAccessor() { return TwLocationToCity::class; }
}