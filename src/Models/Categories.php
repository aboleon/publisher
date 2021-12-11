<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends \Illuminate\Database\Eloquent\Model {

    public function index(): callable
    {
        return view('aboleon.publisher::pages.categories')->with('pages', collect(Pages::where('type','category')->with([
            'globalMeta',
            'hasParent.meta'
        ])->get()));
    }


}
