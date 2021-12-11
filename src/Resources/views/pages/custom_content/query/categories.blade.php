<div class="row with-query">
    <div class="col-sm-11">
        <select class="form-control" id="custom_content_{{ $custom_content_key }}" name="custom_content[{!! $custom_content_key !!}]">
            <option value=''>{{ $select_values[$custom_content_key]->isEmpty() ? 'Aucun choix disponible' : 'Choisissez' }}</option>
            @foreach($select_values[$custom_content_key] as $value)
            @if ($query_with_children)
            <optgroup label="{{ $value->title }}" data-optgroup="{{ $value->id }}">
                @foreach($value->children->where('type', $query_with_children) as $child)
                @php
                $selected =  $page_custom_data && array_key_exists($custom_content_key, $page_custom_data) && $page_custom_data[$custom_content_key] == $child->id ? ' selected' : null;
                @endphp
                <option value="{{ $child->id }}"{!! $selected !!}>
                    {{ $child->title }}
                </option>
                @endforeach
            </optgroup>
            @else
            @php
            $selected =  $page_custom_data && array_key_exists($custom_content_key, $page_custom_data) && $page_custom_data[$custom_content_key] == $value->id ? ' selected' : null;
            @endphp
            <option value="{{ $value->id }}"{!! $selected !!}>
                {{ $value->title }}
            </option>
            @endif
            @endforeach
        </select>
    </div>
    <div class="col-sm-1">
        <button class="btn btn-sm btn-success">
            <i class="fas fa-plus"></i>
        </button>
    </div>
</div>
