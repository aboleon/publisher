@forelse($replicate_data as $value)
    <div class="replicate row {{ $replicate_tag }}">
        <div class="form {!! $item_replicate['grid'] ?? 'col-sm-'.$sc_col !!}">
            <div>
                @switch($item_replicate['type'])

                    @case('email')
                    @case('number')
                    @case('text')
                    <input name="replica_content[{!! $sc['replicate']['id'] !!}][{{ $key_replicate }}][]"
                           type="{!! $item_replicate['type'] !!}" value="{{ $value->value }}"
                           class="form-control col-sm-11">
                    @break

                    @case('textarea')

                    <textarea name="replica_content[{!! $sc['replicate']['id'] !!}][{{ $key_replicate }}][]"
                              class="form-control col-sm-11">{!! $value->value !!}</textarea>
                    @break

                    @case('radio')
                    @case('checkbox')
                    @foreach($item_replicate['options'] as $optionKey => $option)
                        <div class="form-check {{ $item_field['class'] ?? '' }}">
                            <label class="form-check-label">
                                <input
                                    name="replica_content[{!! $sc['replicate']['id'] !!}][{{ $key_replicate }}]"
                                    type="{!! $item_field['type'] !!}" class="form-check-input"
                                    value="{!! $optionKey !!}" {!! in_array('default', $option) ? 'checked="checked"' : null !!}/> {!! $option['label'] !!}
                            </label>
                        </div>
                    @endforeach
                    @break

                @endswitch
            </div>
            <button class="btn btn-danger btn-sm delete_replica">
                Supprimer
            </button>
        </div>
    </div>
@empty
@endforelse
