<?php

namespace Aboleon\Publisher\Components;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class Form extends Component
{
    public function __construct(
        public string $form,
        public string $label = ''
    )
    {
        //
    }

    public function render(): ?Renderable
    {
        return $this->form ? view('aboleon.publisher::components.form') : null;
    }
}
