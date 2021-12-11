<?php

declare(strict_types = 1);

namespace Aboleon\Publisher\Traits;

use Aboleon\Publisher\Models\NavLinks;
use Aboleon\Publisher\Models\Pages;

trait Navigation
{
    private $navigation = [];


    public function customLinks()
    {
        return $this->hasMany(NavLinks::class, 'nav_id');
    }

    public function meta()
    {
        return $this->belongsTo(Pages::class, 'pages_id')->select('id', 'type', 'published')->with('meta');
    }

    private function composesNavs()
    {
        $data = self::query()
            ->select('publisher_nav.*', 'b.published')
            ->leftJoin('publisher_pages as b', 'b.id', '=', 'publisher_nav.pages_id')
            ->with(['meta', 'customLinks'])
            ->orderBy('publisher_nav.position')->get();

        $this->navigation['primary'] = $data->filter(function ($item) {
            return $item->is_primary && empty($item->parent);
        });
        $this->navigation['secondary'] = $data->reject(function ($item) {
            return $item->is_primary;
        });
        $this->navigation['children'] = $data->filter(function ($item) {
            return $item->is_primary && !empty($item->parent);
        })->groupBy('parent');
    }

}
