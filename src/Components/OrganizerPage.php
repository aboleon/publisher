<?php

namespace Aboleon\Publisher\Components;

use Aboleon\Publisher\Models\Configs;
use Aboleon\Publisher\Models\Publisher;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class OrganizerPage extends Component
{
    public Collection $listables;

    public function __construct(
        public string $locale,
        public Publisher $page,
        public Collection $data,
        public string $section = 'left',
    ) {
        $this->data = $this->data->filter(function ($item) {
            return $item->params['belongs'] == 'organizer_' . $this->section;
        });
        $this->listables = Configs::listables();
    }
    public function render(): Renderable
    {
        return view('aboleon.publisher::components.organizer-page')->with('data', $this->data);
    }
}
