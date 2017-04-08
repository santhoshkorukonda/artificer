<?php

namespace SanthoshKorukonda\Fartisan\Bootstrap;

use Illuminate\Support\Facades\Facade;

class Fartisan extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fartisan';
    }
}
