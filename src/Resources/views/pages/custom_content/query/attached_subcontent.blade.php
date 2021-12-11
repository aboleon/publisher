@php
$query_with_children = true;
@endphp
<div class="row with-query">
    <div class="col-sm-10">
        <select class="form-control" id="custom_content_{{ $custom_content_key }}" name="custom_content[{!! $custom_content_key !!}]">
            <option value=''>{{ empty($select_values[$custom_content_key]['values']) ? 'Aucun choix disponible' : 'Choisissez' }}</option>
            @foreach($select_values[$custom_content_key]['values'] as $value)
            @php
            $attached = array_filter($select_values[$custom_content_key]['attached'], function($item) use($value) {
                return $item['value'] == $value->id;
            });
            @endphp
            <optgroup label="{{ $value->title }}" data-optgroup="{{ $value->id }}">
                @foreach($attached as $attached)
                @php
                $selected =  $page_custom_data && array_key_exists($custom_content_key, $page_custom_data) && $page_custom_data[$custom_content_key] == $attached['id'] ? ' selected' : null;
                @endphp
                <option value="{{ $attached['id'] }}"{!! $selected !!}>
                    {{ $attached['title'] }}
                </option>
                @endforeach
            </optgroup>
            @endforeach
        </select>
    </div>
    <div class="col-sm-2" style="padding: 0">
        <button class="btn btn-sm btn-success">
            <i class="fas fa-plus"></i>
        </button>
    </div>
</div>
