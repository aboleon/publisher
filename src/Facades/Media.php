<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Facades;

class Media extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'media';
    }
}