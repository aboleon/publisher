<?php

namespace Aboleon\Publisher\Components;

use Aboleon\Publisher\Models\Publisher;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class MetaTags extends Component
{
    public string $title;
    public string $desc;

    public function __construct()
    {
        $this->title = ($this->content->m_title ?: $this->content->title) . ' :: ' . config('app.name');
        $this->desc = $this->content->m_desc ?: $this->content->abstract;
    }

    public function render(): Renderable
    {
        return view('aboleon.publisher::components.meta-tags');
    }
}
