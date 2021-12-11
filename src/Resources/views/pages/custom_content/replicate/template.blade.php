<template class="replicate {!! $sc['replicate']['id'] !!}" data-lang="{{ $l }}">
    <input type="hidden" class="replicate_id" name="replicate_{!! $sc['replicate']['id'] !!}[]" value="{!! $sc['replicate']['id'] !!}"/>

    {{-- <input type="hidden" class="replicate_guid" name="replicate_guid[]" value="{!! Str::random(8) !!}" />  --}}

    @if(array_key_exists('replicate_'.$sc['replicate']['id'], $sc))
    @foreach($sc['replicate_'.$sc['replicate']['id']]['fields'] as $key_replicate => $item_replicate)

    <div class="{!! $item_replicate['grid'] ?? 'col-sm-'.$sc_col !!}"> {{-- Ici était rattaché la classe form / il
    faudrait faire la différence avec UN SEUL champ et plusieurs --}}

        <span class="content_key hidden">{!! $key_replicate !!}</span>
        @if (array_key_exists('label', $item_replicate))
        <h{{ $has_label ? 5 : 3 }} class="header blue
        smaller">{{ strstr($item_replicate['label'], 'trans_') ? trans(str_replace('trans_','', $item_replicate['label'])) : $item_replicate['label'] }}</h{{ $has_label ? 5 : 3 }}>
        @endif
        <div>
            @switch($item_replicate['type'])

            @case('email')
            @case('number')
            @case('text')
            <input type="{!! $item_replicate['type'] !!}" data-replicate_id="{!! $sc['replicate']['id'] !!}" data-replicate_key="{!! $key_replicate !!}" class="form-control col-sm-11">
            @break

            @case('textarea')
            <textarea data-replicate_id="{!! $sc['replicate']['id'] !!}" data-replicate_key="{!! $key_replicate !!}" class="form-control col-sm-11"></textarea>
            @break

            @case('radio')
            @foreach($item_replicate['options'] as $optionKey => $option)
            <input type="radio" data-replicate_id="{!! $sc['replicate']['id'] !!}" data-replicate_key="{!! $key_replicate !!}" value="{!! $optionKey !!}" {!! in_array('default', $option) ? 'checked="checked"' : null !!}/> {!! $option['label'] !!}
            <br>
            @endforeach
            @break

            @endswitch
        </div>
    </div>
    @endforeach

        <button class="btn btn-danger btn-sm delete_replica">
            Supprimer
        </button>
    @endif


    @if (array_key_exists('replicate_fields', $item_replicate))
    @include('aboleon.publisher::pages.inc.replicate_dynamic_fields')
    <button data-replica="replicate_fields" class="btn btn-sm btn-nfo replicate_fields replicatable">
        Ajouter une ligne
    </button>
    @endif

</template>
