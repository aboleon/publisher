<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Facades;

use Illuminate\Support\Facades\Facade;

class Publisher extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'publisher';
    }
}