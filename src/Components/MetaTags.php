<?php

namespace Aboleon\Publisher\Components;

use Aboleon\Publisher\Models\Publisher;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class MetaTags extends Component
{
    public function __construct(public Publisher $content)
    {
    }

    public function render(): Renderable
    {
        return view('aboleon.publisher::components.meta-tags');
    }
}
