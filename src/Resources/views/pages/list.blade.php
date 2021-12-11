<x-aboleon.publisher-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $data->title . ($data->hasParent ? ' '.$data->hasParent->meta->title : null) .' / '.($listConfig['label'] ?? 'Enregistrements')   }}
            @if ($archives)
                <h4 class="smaller" style="margin-left: 8px;">Éléments archivés</h4>
            @endif
        </h2>
    </x-slot>


@section('ariane')
@include('aboleon.publisher::pages.inc.ariane')
@stop

@php
$has_limit = array_key_exists('limit',$listConfig) ? intval($listConfig['limit']) : null;
$with_image = in_array('with_image', $listConfig);

if ($with_image) {
    $media_folder = Media::getAccessKeyWithSeparator($data);
}

$is_sortable = in_array('sortable', $listConfig);
@endphp

<div class="row page-main-title">
    <div class="col-md-9">
        <div class="inner-main-title">
            <h1 class="fr">
                {{ $data->title . ($data->hasParent ? ' '.$data->hasParent->meta->title : null) .' / '.($listConfig['label'] ?? 'Enregistrements')   }}
            </h1>
            @if ($archives)
            <h4 class="smaller" style="margin-left: 8px;">Éléments archivés</h4>
            @endif
        </div>
    </div>

    <div class="col-md-3 text-right">
        <form method="post" action="{!! $_SERVER['REQUEST_URI'] !!}">
            {!! csrf_field() !!}
            <div id="save-buttons">
                @if (!$has_limit or ($has_limit && $items_count < $has_limit))
                <button id="single-save-btn" class="btn btn-info" type="submit"><i class="fas fa-ok bigger-110"></i>Ajouter un enregistrement</button>
                @endif
                @if(in_array('archive', $listConfig))
                <a href="{!! url('panel/Publisher/pages/list/'.$data->id . ($archives ? null : '?archives')) !!}" class="btn btn-{!! $archives ? 'success':'danger' !!}">{{ $archives ? 'Actifs':'Archivés'}}</a>
                @endif
            </div>
            <input type="hidden" id="page_id" value="{!! $data->id !!}" >
            <input type="hidden" id="list_type" value="{!! $list_type !!}" >
            <input type="hidden" id="contentType" value="listable" >
        </form>
    </div>
</div>
@if(!$pages->isEmpty())

<div class="form"></div>
<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap table-hover {{ $is_sortable ? 'sortable' : null }}" cellspacing="0" width="100%">
    <thead>
        <tr>
           <tr>
            <th class="dbid">#</th>
            @if ($is_sortable)
            <th class="text-center">Ordre</th>
            @endif
            @if ($with_image)
            <th>Image</th>
            @endif
            <th style="width: 50%">{!! trans('aboleon.framework::ui.meta.title')!!}</th>
            @if (!$typeConfig->contains('hide_status'))
            <th>{!! trans('aboleon.framework::ui.meta.status')!!}</th>
            @endif
            <th>{!! trans('aboleon.framework::ui.meta.updated') !!}</th>
            <th>{!! trans('aboleon.framework::ui.edit') !!}</th>
            @if ($config->has('sublist'))
            <th>{!! $config['sublist']['label'] !!}</th>
            @endif
            @if (!in_array('no_links',$listConfig))
            <th>{!! trans('aboleon.framework::ui.buttons.show') !!}</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($pages as $page)

        <tr>
            <td class="dbid">
                {{$page->id}}
                <input class="hidden order" name="position[{{ $page->id }}]"/>
            </td>
            @if ($is_sortable)
            <td class="sort_order">{{ $loop->iteration }}</td>
            @endif
            @if ($with_image)
            <?php $item_img = optional($page->mediaContent->where('type', 'image')->first())->content;?>
            <td{!! $item_img ? ' style="background:url('.asset('upload/'.$media_folder.'images/th_'.$item_img).');background-size:cover"' : null !!}></td>
            @endif
            <td>{!! (!empty($page->meta->title) ? $page->meta->title : trans('aboleon.framework::ui.untitled')) !!}</td>
            @if (!$typeConfig->contains('hide_status'))
            <td class="status {!! $page->published ? 'success':'danger' !!}">
                {!! trans('aboleon.framework::ui.' . ($page->published ? 'online': 'offline')) !!}
            </td>
            @endif
            <td class="time">{!! $page->updated_at !!}</td>
            <td>
                <a class="btn btn-sm btn-warning" title="Éditer" href="{!! url('panel/Publisher/pages/edit/'. $page->id ) !!}">
                    <i class="fas fa-pen"></i>
                </a>
                @if ($page->trashed())
                <a class="btn btn-sm btn-success" title="Restaurer" href="{!! url('panel/Publisher/pages/restoreContent/'. $page->id ) !!}">
                    <i class="fas fa-undo"></i>
                </a>
                @endif
                <a class="btn btn-sm btn-danger" title="Supprimer" href="{!! url('panel/Publisher/pages/remove'.($archives ? 'Archives' : null).'/'. $page->id ) !!}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
            @if (!in_array('no_links',$listConfig))
            <td class="langs">
                @if ($is_multilang)
                @foreach($locales as $l)
                <a target="_blank" href="{!! url($l .'/'. ( Arr::has($listConfig, 'url_prefix') ? $listConfig['url_prefix'] .'/' : null ) . $page->meta->whereLg($l)->first()->url) !!}">
                    <img width="18" src="{!! asset('vendor/flags/4x3/'.$l.'.svg') !!}" alt="{{ trans('aboleon.framework::ui.lang.'.$l) }}" title="{{ trans('aboleon.framework::ui.lang.'.$l) }}"/>
                </a>
                @endforeach
                @else
                <a title="Afficher" class="btn btn-sm btn-info" target="_blank" href="{!! url(
                    ($config->has('url_prefix') ? $config['url_prefix'] .'/'.$data->meta->url.'/' : null ) .
                    (Arr::has($listConfig, 'url_prefix') ? $listConfig['url_prefix'] .'/' : null ) .
                    $page->meta->url
                    ) !!}">
                    <i class="fas fa-link"></i>
                </a>
                @endif
            </td>
            @endif

        </tr>
        @endforeach
    </tbody>
</table>

@if($pages instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $pages->links() }}
@endif

@else
{!! ResponseRenderers::warning('Aucun enregistrement') !!}
@endif


</x-aboleon.publisher-layout>

@push('js')
<script>
    $(function() {
        $('[rel=tooltip]').tooltip();
    });
</script>
@if ($is_sortable)
    <script>publisherSortable();</script>
@endif
@endpush
