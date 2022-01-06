@php
    $config = collect(config('forms'))->where('name', $form)->first();
    $error = $errors->any();;
@endphp

@if (is_null($config))
    @if (config('app.debug'))
        Le formulaire {{ $form }} n'existe pas dans les configurations
    @endif
@else

    <form class="form-{{ $form }}" action="{{ route('form', $form) }}" method="post" id="form-{{ $form }}">
        @csrf
        <!-- <h3>{{ $label ?: __('forms.labels.'.$form) }}</h3> -->
        <div class="row">
            @foreach($config['fields'] as $field)
                <div class="col {{ $field['grid'] }} mb-3">
                    @switch($field['type'])
                        @case('textarea')
                        <x-aboleon.framework-bootstrap-textarea :name="$field['label']" :label="__('forms.'.$field['label'])" :value="$error ? old($field['label']) : ''"/>
                        @break
                        @default
                        <x-aboleon.framework-bootstrap-input :type="$field['type']" :name="$field['label']" :label="__('forms.'.$field['label'])" :value="$error ? old($field['label']) : ''"/>
                    @endswitch
                </div>
            @endforeach
        </div>
        <div class="text-center">
            <button type="submit" form="form-{{ $form }}">{{ $btn_txt }}</button>
        </div>
    </form>
@endif
