<x-aboleon.framework-layout :title="$title">

    @push('css')
        {!! csscrush_tag(public_path('aboleon/publisher/css/panel.css')) !!}
    @endpush


    {{ $slot }}

    @push('js')
            {{--<script src="{{ asset('aboleon/publisher/js/publisher_sortable.js') }}"></script>--}}
    @endpush

</x-aboleon.framework-layout>