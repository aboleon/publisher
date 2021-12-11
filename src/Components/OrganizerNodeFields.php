<?php

namespace Aboleon\Publisher\Components;

use Aboleon\Publisher\Models\Configs;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class OrganizerNodeFields extends Component
{
    public function __construct(
        public string $name,
        public array  $node
    )
    {
    }

    public function render(): Renderable
    {
        return view('aboleon.publisher::components.organizer-node-fields')->with([
            'node' => $this->node,
            'name' => $this->name
        ]);
    }
}
