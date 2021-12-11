@if ($replicate_fields)
    @if (Arr::has($sc['replicate_fields'], 'label'))
        <strong class="replicate_fields clear">{{ $sc['replicate_fields']['label'] }}</strong>
    @endif

    @php
        $base_dynamic_text_field = current(array_keys(array_filter($sc['replicate_fields']['fields'], function($val) { return $val['type'] == 'text'; })));
        $dynamic_count = $replica_content->filter(function($val) use($replicate_id, $base_dynamic_text_field) { return $val['field'] == $base_dynamic_text_field.'_'. $replicate_id; })->count();
        $dynamic = $replica_content->filter(function($val) use($replicate_id) { return strstr($val['field'], $replicate_id); });

        foreach($sc['replicate_fields']['fields'] as $k=>$v) {
            ${'dynamic_'.$k} = $replica_content->filter(function($val) use($replicate_id, $k) { return $val['field'] == $k.'_'.$replicate_id; })->values();
        }
    @endphp

    @if ($dynamic_count)
        @for($i=0;$i<$dynamic_count;++$i)
            @include('aboleon.publisher::pages.inc.replicate_dynamic_fields')
        @endfor
    @else
        @include('aboleon.publisher::pages.inc.replicate_dynamic_fields')
    @endif

    <button data-replica="replicate_fields"
            class="btn btn-sm btn-info replicate_fields replicatable">Ajouter une ligne
    </button>

@endif