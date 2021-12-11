<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Callables;

use Helpers;
use Aboleon\Publisher\Models\CustomContent;

class CustomContentCallable
{
    public static function categories(array $arguments)
    {
        return Publisher::where($arguments)->orderBy('title')->get();
    }

    public static function nested_categories(array $arguments)
    {
        return Publisher::where($arguments)->whereNull('parent')->select('id','parent','title')->orderBy('title')->with('nested')->get();
    }

    public static function attached_subcontent(array $arguments)
    {
        $data = [];
        $data['values'] = Publisher::where($arguments)->select('id','title')->orderBy('title')->get();
        $data['attached'] = CustomContent::select('a.id','a.title', 'value')
        ->join('publisher_pages as a', function($join) use($data) {
            $join->on('pages_id','=','a.id')->where('field','taxonomy')->whereIn('value', $data['values']->pluck('id'));
        })->orderBy('title')->get()->toArray();
        return $data;
    }
}
