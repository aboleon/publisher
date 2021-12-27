@php
    $config = collect(config('forms'))->where('name', $form)->first();
@endphp

@if (is_null($config))
    @if (config('app.debug'))
        Le formulaire {{ $form }} n'existe pas dans les configurations
    @endif
@else

    <form class="form-{{ $form }}" action="{{ route('form', $form) }}" method="post">
        <h3>{{ $label ?: __('forms.labels.'.$form) }}</h3>
        <div class="row">
            @foreach($config['fields'] as $field)
                <div class="col {{ $field['grid'] }} mb-3">
                    @switch($field['type'])
                        @case('textarea')
                        <x-aboleon.framework-bootstrap-textarea :name="$field['label']" :label="__('forms.'.$field['label'])" value=""/>
                        @break
                        @default
                        <x-aboleon.framework-bootstrap-input :name="$field['label']" :label="__('forms.'.$field['label'])"/>
                    @endswitch
                </div>
            @endforeach
        </div>
    </form>
@endif
