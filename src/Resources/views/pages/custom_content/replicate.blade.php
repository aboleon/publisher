<div class="replica_container {{ $sc['replicate']['id'] }}">
    @php
        $replicate_tag = $sc['replicate']['id'];
    @endphp

    <input type="hidden" name="replicate_group[]" value="{{ $replicate_tag }}">

    @php
        $replicate_tags[$replicate_tag] = '';
        $replicate_group = $data->customContent->where('field','replicate_group')->where('value', $replicate_tag)
        ->isNotEmpty();

        $replica_content =$data->customContent->filter(function($val) use($replicate_tag) { return
            strstr($val['field'],
            $replicate_tag); });
    @endphp

    @if ($replicate_group)
        @php
            $replicate_keys = $data->customContent->where('field',$replicate_tag)->pluck('value');
        @endphp
        @foreach($replicate_keys as $rep_key)
            <div class="replicate row {{ $replicate_tag }}">
                <input type="hidden" class="replicate_id" name="replicate_{{$replicate_tag}}[]" value="{{$rep_key}}">
            @foreach($sc['replicate_'.$replicate_tag]['fields'] as $key_replicate => $item_replicate)
                @php
                    $replicate_data = $data->customContent->filter(function($val) use($rep_key, $key_replicate) {
                    return $val['field'] == $key_replicate.'_'. $rep_key;
                    })->first();

                @endphp
                @include('aboleon.publisher::pages.custom_content.replicate.loop_groups')
            @endforeach
                <button class="btn btn-danger btn-sm delete_replica">
                    Supprimer
                </button>
            </div>
        @endforeach
    @else



        @if (!$replica_content->isEmpty())

            @foreach($sc['replicate_'.$sc['replicate']['id']]['fields'] as $key_replicate => $item_replicate)

                @php
                    $replicate_data = $replica_content->filter(function($item) use($key_replicate) {
                        return $item->field == $key_replicate;
                    });
                @endphp

                @include('aboleon.publisher::pages.custom_content.replicate.loop')
            @endforeach
        @endif
    @endif

    <button class="btn btn-sm btn-success replicate" data-template="{{ $sc['replicate']['id'] }}">
        {!! $sc['replicate']['button'] ?? 'Ajouter' !!}
    </button>


    @include('aboleon.publisher::pages.custom_content.replicate.template')

</div>
