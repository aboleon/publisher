<div class="row replicate_fields">
    @php
    $replicate_cols = 12/count($sc['replicate_fields']);
    @endphp
    @foreach($sc['replicate_fields']['fields'] as $k=>$v)

    <div class="{!! $v['grid'] ?? 'col-sm-'.$replicate_cols !!}">
        <span class="content_key hidden">{!! $k !!}</span>

        @if (Arr::has($v, 'label'))
        <h{{ $has_label ? 5 : 3 }} class="header blue smaller">{{ $v['label'] }}</h{{ $has_label ? 5 : 3 }}>
        @endif

        @switch($v['type'])

        @case('email')
        @case('number')
        @case('text')

        <input type="{!! $v['type'] !!}" name="replica_content[{!! $k !!}][{!! $replicate_id ?? null !!}][]" value="{!! isset(${'dynamic_'.$k}) && !${'dynamic_'.$k}->isEmpty() ? ${'dynamic_'.$k}[$i]->content : null !!}" class="form-control col-sm-11" {!! Arr::has($v, 'placeholder') ? ' placeholder="'.$v['placeholder'].'"' : null !!}>
        @break

        @endswitch

    </div>
    @endforeach
    <button class="btn btn-sm btn-danger delete_replica" data-target="replicate_fields">supprimer</button>
</div>
