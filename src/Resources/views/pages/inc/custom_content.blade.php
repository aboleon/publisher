@push('css')
    {!! csscrush_inline(public_path('aboleon/publisher/css/custom-content.css'), ['minify'=>true]) !!}
@endpush


@if (isset($noLangCustomContent))

    @php
        global $replicate_tags;
        $sc_col = 'col-sm-10';
        $replicas = $noLangCustomContent->filter(function($val) { return array_key_exists('replicate', $val); })->count();
    @endphp

    @if ($replicas>0)
        @push('css')
            {!! csscrush_inline(public_path('aboleon/publisher/css/replicator.css'), ['minify'=>true]) !!}
        @endpush
        @push('js')
            <script src="{!! asset('aboleon/publisher/js/replicate_custom_content.js') !!}"></script>
        @endpush
    @endif

    @php
        $replicate_tags = [];
        $replicate_ids = [];
    @endphp

    <div class="custom-content">
    @foreach($noLangCustomContent as $sc)

        @php
            $has_label = array_key_exists('label', $sc);
            if (array_key_exists('fields', $sc) && array_key_exists
            ('media', $sc['fields'])) {
                $has_label = false;
            }
            $sc_col = array_key_exists('fields', $sc) ? 12/count($sc['fields']) : 12;
            $replicate =array_key_exists('replicate',$sc);
            $replicate_fields = array_key_exists('replicate_fields', $sc);
            $is_grid = (array_key_exists('grid', $sc) && $sc['grid'] == 'custom');
        @endphp

        <div class="bloc-editable">

            @if ($has_label)
                <h2>
                    {{ translatable($sc['label']) }}
                </h2>
            @endif

            @if (array_key_exists('notice', $sc))
                <p class="notice">{!! $sc['notice']  !!}</p>
            @endif

            @if (array_key_exists('class_managed_content', $sc))
                @include('aboleon.publisher::pages.custom_content.class_managed_content')
            @else

            @if ($replicate)
            <!-- begin replicate -->
            <div>
                @include('aboleon.publisher::pages.custom_content.replicate')
            </div>
            @else
            <!-- begin custom content -->
            @include('aboleon.publisher::pages.custom_content.unique')
            @endif
            @endif
        </div>
        @endforeach
        @endif
    </div>

    @push('js')
    <script src="aboleon/publisher/js/custom_content.js"></script>
    @endpush
