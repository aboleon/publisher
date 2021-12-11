<?php

namespace Aboleon\Publisher\Components;

use Aboleon\Publisher\Models\Configs;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class OrganizerNodeParams extends Component
{
    public array $associatables;

    public function __construct(
        public bool|array $type,
        public string $name,
        public array  $node
    )
    {
        $this->associatables = Configs::associatables()->toArray();
    }

    public function render(): Renderable
    {
        return view('aboleon.publisher::components.organizer-node-params')->with([
            'node' => $this->node,
            'name' => $this->name,
            'type' => $this->type
        ]);
    }
}
