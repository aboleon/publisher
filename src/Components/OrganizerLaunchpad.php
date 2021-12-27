<?php

namespace Aboleon\Publisher\Components;

use Aboleon\Publisher\Models\Configs;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class OrganizerLaunchpad extends Component
{
    public Collection $listables;
    public Collection $formables;

    public function __construct(
        public string $section = 'left',
        public array  $data = []
    )
    {
        $this->data = array_filter($this->data, function ($item) {
            return $item['params']['belongs'] == 'organizer_' . $this->section;
        });
        $this->listables = Configs::listables();
        $this->formables = collect(config('forms'));
    }

    public function render(): Renderable
    {
        return view('aboleon.publisher::components.organizer-launchpad')->with('data', $this->data);
    }
}
