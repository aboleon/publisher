<?php

declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Illuminate\Support\{
    Facades\Artisan,
    Str
};

use Illuminate\Database\Eloquent\Model;
use Aboleon\Publisher\Traits\Navigation;

class NavBuilder extends Model
{
    protected $table = 'publisher_nav';
    private $menu_builder = [];
    private $menu = [];

    use Navigation;

    public function build()
    {
        $this->composesNavs();
        $this->buildMenu('primary');
        $this->buildMenu('secondary');

        return $this->menu;
    }

    private function buildMenu(string $type)
    {
        foreach ($this->navigation[$type] as $item) {
            $this->menu_builder[$item->id] = $this->navElement($item);
            $this->recursiveNavElement($item);
        }
        $this->menu[$type] = $this->menu_builder;
        $this->resetMenuBuilder();
    }

    private function navElement($item)
    {
        if ($item->customLinks->isNotEmpty()) {
            $el = $item->customLinks->where('lg', app()->getLocale())->first();
            return [
                'type' => 'custom_link',
                'url' => $el->url ?? '',
                'title' => $el->title ?? '',
                'full_title' => $el->title ?? '',
                'is_primary' => $item->is_primary
            ];
        } else {
            $menu = [
                'type' => $item->meta->type,
                'url' => $item->meta->meta->url ?? '',
                'title' => $item->meta->meta->nav_title ?? '',
                'full_title' => $item->meta->meta->title ?? '',
                'is_primary' => $item->is_primary,
                'page_id' => $item->pages_id,
            ];
            return $menu;
        }
    }

    private function recursiveNavElement($item)
    {
        if ($this->navigation['children']->has($item->id)) {
            foreach ($this->navigation['children'][$item->id] as $subitem) {
                $this->menu_builder[$item->id]['children'][] = $this->navElement($subitem);
                $this->recursiveNavElement($subitem);
            }
        }
    }

    private function resetMenuBuilder()
    {
        $this->menu_builder = [];
    }
}

