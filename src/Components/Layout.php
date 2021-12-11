<?php

namespace Aboleon\Publisher\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class Layout extends Component
{
    public function __construct(
        public string $title = '',
    ) {}
    public function render(): Renderable
    {
        return view('aboleon.publisher::layouts.panel');
    }
}
