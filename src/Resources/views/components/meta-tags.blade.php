@section('meta-tags')
    <title>{!! ($content->m_title ?: $content->title) . ' :: '. config('app.name') !!}</title>
    <meta name="description" content="{!! $content->m_desc ?: $content->abstract  !!}">
    <!-- open graph -->
    <meta property="og:title" content="{!! ($content->m_title ?: $content->title) . ' :: '. config('app.name') !!}"/>
    <meta property="og:locale" content="{{ app()->getLocale() }}_{{ strtoupper(app()->getLocale()) }}"/>
    <meta property="og:description" content="{{ $content->m_desc ?: $content->abstract }}">
    <meta property="og:url" content="{{ config('app.url') }}"/>
@endsection