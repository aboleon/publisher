<?php
unset($sc['fields']['media']);
?>

<div class="row">
    <div class="form-group col-sm-12">

        @foreach($sc['fields'] as $item_key => $item_field)
            <?php
            $custom_value = $data->fetchCustomValue($item_key);
            $h_title = ($has_label ? 5 : 3);
            if (!array_key_exists('type', $item_field)) {
                $item_field['type'] = 'text';
            }
            ?>
            <div data-multilang="{!! $sc['multi_lang'] !!}"
                 class="form type-{!! $item_field['type'] .' ' . ($is_grid ? $item_field['grid'] : 'col-sm-'.$sc_col) !!}">
                <span class="content_key hidden">{!! $item_key !!}</span>
                @if (Arr::has($item_field, 'label'))
                    <h{{ $h_title }}>
                        {{ translatable($item_field['label']) }}
                    </h{{ $h_title }}>
                @endif

                @switch($item_field['type'])

                    @case('text')
                    @case('email')
                    @case('number')
                    @case('textarea')
                    <?php $custom_value = $data->fetchCustomValue($item_key, 'string');?>

                    @case('number')
                    <?php $custom_value = AboleonPublisherHelpers::castFromInteger($item_field, $custom_value); ?>

                    @case('email')
                    @case('number')
                    @case('text')

                    @if ($item_field['type'] == 'textarea')

                        <textarea class="form-control" style="min-height: 100px"
                                  name="custom_content[{!! $item_key !!}]">{{ $custom_value }}</textarea>
                    @else

                        <input placeholder="{{ $item_field['placeholder'] ?? null }}" type="{!! $item_field['type'] !!}"
                               name="custom_content[{!! $item_key !!}]" value="{{ $custom_value }}"
                               class="form-control col-sm-11">
                    @endif
                    @break

                    @case('textarea')

                    <textarea class="form-control" style="min-height: 100px"
                              name="custom_content[{!! $item_key !!}]">{{ $custom_value }}</textarea>
                    @break

                    @case('nested_categories')
                    @include('aboleon.publisher::pages.custom_content.nested_categories')
                    @break

                    @case('datepicker')
                    @include('aboleon.publisher::pages.custom_content.datepicker')
                    @break

                    @case('select')
                    @include('aboleon.publisher::pages.custom_content.select')
                    @break

                    @case('radio')
                    @case('checkbox')
                    @include('aboleon.publisher::pages.custom_content.checkables')
                    @break

                    @case('file')
                    @includeIf('aboleon.publisher::'.$item_field['path'])
                    @break

                    @case('google_places')
                    @include('aboleon.publisher::pages.custom_content.google_places')
                    @break

                @endswitch
            </div>
        @endforeach
    </div>
</div>
