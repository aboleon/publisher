<x-aboleon.publisher-layout>
    @if(!$data->isEmpty())
        <div class="form" data-url="panel/Publisher/ajax"></div>
        <table class="table table-striped table-bordered table-hover {{ $is_sortable ? 'sortable' : null }}"
               cellspacing="0" width="100%">
            <thead>
            <tr>
            <tr>
                <th class="dbid">#</th>
                <th>Personne</th>
                <th>Personne</th>
                <th>Sujet</th>
                @if (isset($config['has']))
                    @foreach($config['has'] as $item)
                        <th class="text-center">{!! $item['label'] !!}</th>
                    @endforeach
                @endif
                <th>{!! trans('aboleon.framework::ui.edit') !!}</th>
                @if (!in_array('no_links',$config['is_listable']))
                    <th>{!! trans('aboleon.framework::ui.buttons.show') !!}</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($data as $item)
                @php
                    $media_folder = Media::getAccessKeyWithSeparator($item);
                @endphp
                <tr>
                    <td class="dbid">
                        {{$item->id}}
                        <input class="hidden order" name="position[{{ $item->id }}]"/>
                    </td>
                    @if ($is_sortable)
                        <td class="sort_order">{{ $loop->iteration }}</td>
                    @endif
                    @if ($with_image)
                        <?php $item_img = optional($item->mediaContent->where('type', 'image')->first())->content;?>
                        <td{!! $item_img ? ' style="background:url('.Project::media($media_folder.'images/th_'.$item_img).');
            background-size:cover"' : null !!}></td>
                    @endif
                    <td>{!! (!empty($item->meta->title) ? $item->meta->title : trans('aboleon.framework::ui.untitled')) !!}</td>
                    @if (!in_array('hide_status', $config))
                        <td class="status {!! $item->published ? 'success':'danger' !!}">
                            {!! trans('aboleon.framework::ui.' . ($item->published ? 'online': 'offline')) !!}
                        </td>
                    @endif
                    <td class="time">{!! $item->updated_at !!}</td>
                    @if (isset($config['has']))
                        @foreach($config['has'] as $key => $item)
                            <td class="text-center">
                                <a href="panel/Publisher/pages/list/{{ $item->id .'/'.$key}}"
                                   class="intbox {{ $item->children_count < 1 ? 'inactive':''}}">
                                    {{ $item->children_count }}
                                </a>
                            </td>
                        @endforeach
                    @endif
                    <td>
                        <a class="btn btn-sm btn-warning" title="Éditer"
                           href="{!! url('panel/Publisher/pages/edit/'. $item->id ) !!}">
                            <i class="fas fa-pen"></i>
                        </a>
                        @if ($item->trashed())
                            <a class="btn btn-sm btn-success" title="Restaurer"
                               href="{!! url('panel/Publisher/pages/restoreContent/'. $item->id ) !!}">
                                <i class="fas fa-undo"></i>
                            </a>
                        @endif
                        @if($can_be_deleted)
                            <a class="btn btn-sm btn-danger" title="Supprimer"
                               href="{!! url('panel/Publisher/pages/remove/'. $item->id ) !!}">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        @endif
                    </td>
                    @if (!in_array('no_links', $config['is_listable']))
                        <td class="langs">
                            @if ($is_multilang)
                                @foreach($locales as $l)
                                    <a target="_blank"
                                       href="{!! url($l .'/'. ( Arr::has($config['is_listable'], 'url_prefix') ? $config['is_listable']['url_prefix'] .'/' : null ) . $item->meta->whereLg($l)->first()->url) !!}">
                                        <img width="18" src="{!! asset('vendor/flags/4x3/'.$l.'.svg') !!}"
                                             alt="{{ trans('aboleon.framework::ui.lang.'.$l) }}"
                                             title="{{ trans('aboleon.framework::ui.lang.'.$l) }}"/>
                                    </a>
                                @endforeach
                            @else
                                <a title="Afficher" class="btn btn-sm btn-info" target="_blank" href="{!!
                    (Arr::has($config['is_listable'], 'url_prefix') ? $config['is_listable']['url_prefix'] .'/' : null ) .
                    $item->meta->url
                    !!}">
                                    <i class="fas fa-link"></i>
                                </a>
                            @endif
                        </td>
                    @endif

                </tr>
            @endforeach
            </tbody>
        </table>

        @if($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $data->links() }}
        @endif

    @else
        {!! ResponseRenderers::warning('Aucun enregistrement') !!}
    @endif
</x-aboleon.publisher-layout>
