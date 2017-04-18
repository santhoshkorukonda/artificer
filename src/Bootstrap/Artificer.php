<?php

namespace SanthoshKorukonda\Artificer\Bootstrap;

use Illuminate\Support\Facades\Facade;

class Artificer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'artificer';
    }
}
