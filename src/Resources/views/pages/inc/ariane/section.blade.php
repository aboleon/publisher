{!! Aboleon\Publisher\Models\Nav::section_navbar($data) !!}
{{-- Skip Base Section --}}

@php
global $ariane_url;
$ariane_url ='/';
$ariane_default_text = $data->title;
@endphp

@if (view()->exists('panel.nav.ariane.'.$data->type))
@include('panel.nav.ariane.'.$data->type)
@else

@switch($data->type)
    @case('section')
       <?php $section_parent_config = config('project.content.'.$data->hasParent->type);?>
    @break;

    @case('category')
    <?php $section_parent_config = config('project.content.'.$data->hasParent->hasParent->hasParent->type);?>
    {!! Aboleon\Publisher\Models\Nav::section_navbar($data->hasParent) !!}
        <li>
            <a href="{!! url('panel/Publisher/pages/edit/'.$data->hasParent->parent.'/subpage/'.str_replace('section_','', $data->hasParent->type)) !!}">{!! $data->hasParent->title !!}</a>
        </li>
        <li>
            <a href="{!! url('panel/Publisher/pages/list/'.$data->parent.'/category') !!}">Catégories</a>
        </li>
        @php
        $ariane_default_text = $data->hasParent->meta->title;
        $ariane_url = $section_parent_config['url_prefix'].'/'.
        $data->hasParent->hasParent->hasParent->meta->url.'/'.
        $section_parent_config['has']['section']['url_prefix'].'/'.
        $data->hasParent->hasParent->meta->url.'/'.
        str_replace('section_','', $data->hasParent->type);
        @endphp
    @break;

    @case('subpage')
        {!! Aboleon\Publisher\Models\Nav::section_navbar($data->hasParent) !!}
        <?php
        $section_parent_config = config('project.content.'.$data->hasParent->hasParent->hasParent->type);
        $ariane_default_text = $data->meta->title;
        ?>
        <li>
            <a href="{!! url('panel/Publisher/pages/edit/'.$data->hasParent->parent.'/subpage/'.str_replace('section_','', $data->hasParent->type)) !!}">{!! $data->hasParent->title !!}</a>
        </li>
        <li>
            <a href="{!! url('panel/Publisher/pages/list/'.$data->parent.'/subpage') !!}">Sous Pages</a>
        </li>
        @php
        $ariane_url = $section_parent_config['url_prefix'].'/'.
        $data->hasParent->hasParent->hasParent->meta->url.'/'.
        $section_parent_config['has']['section']['url_prefix'].'/'.
        $data->hasParent->hasParent->meta->url.'/'.
        $data->meta->url;
        @endphp
    @break;

    @default
        <?php $section_parent_config = config('project.content.'.$data->hasParent->hasParent->type);?>

        @if (isset($listConfig))
            <li>
                <a href="{!! url('panel/Publisher/pages/edit/'.$data->parent.'/subpage/'.str_replace('section_','', $data->type)) !!}">{!! $data->title !!}</a>
            </li>
            <li>
                <a href="{!! url('panel/Publisher/pages/list/'.$data->id.'/category') !!}">Catégories</a>
            </li>
        @endif

        @php
        $ariane_url = (array_key_exists('url_prefix', $section_parent_config)
        ? $section_parent_config['url_prefix'].'/'. $data->hasParent->hasParent->meta->url.'/'. $section_parent_config['has']['section']['url_prefix'].'/'
        : null) .
        ($data->type == 'section'
        ? $data->meta->url
        : ($data->type == 'subpage'
            ? str_replace('section_','', $data->hasParent->type)
            : $data->hasParent->meta->url).
            '/'. str_replace('section_','', $data->type)
        )
    @endphp
@endswitch
@endif

@section('ariane_default')
<a class="outer label label-info" target="_blank" href="{!! url($ariane_url) !!}"><i class="fas fa-globe"></i>&nbsp;&nbsp;{!! $ariane_default_text !!}</a>
@endsection
