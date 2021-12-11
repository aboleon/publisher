<x-aboleon.publisher-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pages de type <strong>{{ $type }}</strong>
            @if ($archives)
                <h4 class="smaller" style="margin-left: 8px;">Éléments archivés</h4>
            @endif
        </h2>
    </x-slot>
@php
$archives = $config->contains('archive');
$is_sortable = $config->contains('sortable');
@endphp


<form method="post" action="{!! $_SERVER['REQUEST_URI'] !!}">
    @csrf
    <div class="float-end" id="save-buttons">

        <button id="single-save-btn" class="btn btn-info" type="submit"><i class="fas fa-ok bigger-110"></i>Ajouter un enregistrement</button>
        @if($archives)
        <a href="{!! url('panel/Publisher/pages/ofType/'.$type.'?archives') !!}" class="btn btn-danger">Archivés</a>
        @endif
    </div>
    <input type="hidden" id="list_type" value="{!! $type !!}" >
    <input type="hidden" id="contentType" value="listable" >
</form>

<div class="space-14" style="clear: both;"></div>
@if(!$pages->isEmpty())

<div class="form"></div>
<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap table-hover {{ $is_sortable ? 'sortable' : null }}" cellspacing="0" width="100%">
    <thead>
        <tr>
           <tr>
            <th class="dbid">#</th>
            @if ($is_sortable)
            <th class="text-center" style="padding: 0;">Ordre</th>
            @endif
            <th style="width: 50%">{!! trans('aboleon.framework::ui.meta.title')!!}</th>
            @if (!$config->contains('hide_status'))
            <th>{!! trans('aboleon.framework::ui.meta.status')!!}</th>
            @endif
            <th>{!! trans('aboleon.framework::ui.meta.updated') !!}</th>
            <th>{!! trans('aboleon.framework::ui.edit') !!}</th>
            @if ($config->has('sublist'))
            <th>{!! $config['sublist']['label'] !!}</th>
            @endif
            @if (!$config->contains('no_links'))
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
            <td>{!! (!empty($page->meta->title) ? $page->meta->title : trans('aboleon.framework::ui.untitled')) !!}</td>
            @if (!$config->contains('hide_status'))
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
            @if (!$config->contains('no_links'))
            <td class="langs">
                @if ($is_multilang)
                @foreach($locales as $l)
                <a target="_blank" href="{!! url($l .'/'. ( $config->has('url_prefix') ? $config->url_prefix .'/' : null ) . $page->meta->whereLg($l)->first()->url) !!}">
                    <img width="18" src="{!! asset('vendor/flags/4x3/'.$l.'.svg') !!}" alt="{{ trans('aboleon.framework::ui.lang.'.$l) }}" title="{{ trans('aboleon.framework::ui.lang.'.$l) }}"/>
                </a>
                @endforeach
                @else
                <a title="Afficher" class="btn btn-sm btn-info" target="_blank" href="{!! url(
                    ($config->has('url_prefix') ? $config->url_prefix .'/' : null ) .
                    $page->meta->url
                    ) !!}">
                    <i class="fas fa-eye"></i>
                </a>
                @endif
            </td>
            @endif

        </tr>
        @endforeach
    </tbody>
</table>
@else
{!! ResponseRenderers::warning('Aucun enregistrement') !!}
@endif

</x-aboleon.publisher-layout>

@push('js')

@if ($config->contains('dataTables'))
@include('aboleon.framework::lib.dataTables')
@endif

<script>
    $(function() {
        $('[rel=tooltip]').tooltip();
    });
</script>
@if ($is_sortable)
    <script>publisherSortable();</script>
@endif
@endpush
