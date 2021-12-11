<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Facades;

use Illuminate\Support\Facades\Facade;

class Helpers extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'aboleon_publisher_helpers';
    }
}