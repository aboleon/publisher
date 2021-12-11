<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Facades;

class Nav extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'publisher_nav';
    }
}