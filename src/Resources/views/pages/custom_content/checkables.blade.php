<!-- checkables -->
@if(isset($item_field['options']))
    @foreach($item_field['options'] as $optionKey => $option)
        <div class="form-check {{ $item_field['class'] ?? '' }}">
            @php $subkey = $item_field['type'] == 'checkbox' ? '_' . $optionKey : null; @endphp
            <label class="form-check-label">
                <input type="{!! $item_field['type'] !!}" class="form-check-input"
                       name="custom_content[{!! $item_key . $subkey !!}]"
                       value="{!! $optionKey !!}" {!! $page_custom_data && array_key_exists($item_key . $subkey, $page_custom_data)
        ? ($page_custom_data[$item_key . $subkey] == $optionKey
            ? 'checked="checked"'
            : null)
        : (in_array('default', $option) ? 'checked="checked"' : null) !!} /> {!! $option['label'] !!}
            </label>
        </div>
    @endforeach
@elseif(isset($item_field['values']))
    @php
        $current_elements = $data->customContent->where('field', $item_key)->pluck('value')->toArray();
    @endphp
    @foreach($item_field['values'] as $values_key => $values_value)
        <div class="form-check {{ $item_field['class'] ?? '' }}">
            <label class="form-check-label">
                <input type="{!! $item_field['type'] !!}" class="form-check-input"
                       name="custom_content[{!! $item_key !!}][]"
                       value="{!! $values_key !!}" {{ in_array($values_key, $current_elements) ? 'checked': (
                       array_key_exists('default', $item_field) && $item_field['default']  == $values_key ?
                       'checked' : '')}} />
                {{ translatable($values_value) }}
            </label>
        </div>
    @endforeach
@elseif(isset($item_field['query']))
    @php
        include('query/prepare.php');
        $current_elements = $data->customContent->where('field', $custom_content_key)->pluck('value')->toArray();
    @endphp
    <ul class="checkables" id="custom_content_{{ $item_key }}">
        @foreach($select_values[$custom_content_key] as $value)
            <li>
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input"
                           name="custom_content[{!! $item_field['query']['arguments']['type'] !!}][]"
                           value="{!! $value->id !!}" {{ in_array($value->id, $current_elements) ? 'checked':'' }}/> {!! $value->title !!}
                </label>
            </li>
        @endforeach
    </ul>
    @if (array_key_exists('create', $item_field))
        <div class="checkables with-query">
            <button class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    @endif
    @php include('query/ajaxsubpages.php')); @endphp
@endif
