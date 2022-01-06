<?php

namespace Aboleon\Publisher\Components;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component;

class Form extends Component
{
    public function __construct(
        public string $form,
        public string $btn_txt = '',
        public string $label = ''
    )
    {
        //
    }

    public function render(): ?Renderable
    {
        $this->btn_txt = $this->btn_txt ?: trans('aboleon.framework::forms.buttons.send');
        return $this->form ? view('aboleon.publisher::components.form') : null;
    }
}
