@if ($config->has('has'))
    @php
        $listable_pages = Publisher::listablePages($config['has'], $data->id);
        $single_pages = Publisher::listableSinglePages($config['has'], $data->id);

    @endphp

    @foreach($config['has'] as $listable => $entry)
        @php
            $configuration = config('project.content.'.$listable)['is_listable'];
                        $listable_type = str_replace('_list','', $listable);
                        $is_listable = (strstr($listable, '_list') or isset(config('project.content.'.$listable)['is_listable']));
                        $is_single = in_array('is_single', $configuration);
                        $has_page = !in_array('no_page', $configuration);
                        $parent = strstr($listable, '_list') ? $listable_pages[$listable]['id'] : $data->id;
        @endphp

        <div class="bloc-editable">

            @if(!Arr::has($config, 'has.'.$listable.'.presentation') or $config['has'][$listable]['presentation'] != 'tabs')

                @php
                    $listable_config = is_array(config('project.content.'.$listable)) ? config('project.content.'.$listable) : [];
                    $has_limit = isset(config('project.content.'.$data->type)['has'][$listable]['limit']) ? intval(config('project.content.'.$data->type)['has'][$listable]['limit']) : null;
                    $with_image = in_array('with_image', $configuration);
                @endphp

                @if ($with_image)
                    <?php $media_folder = Media::getAccessKeyWithSeparator($data);?>
                @endif

                <h3>{!! $entry['label'] ??  ($configuration['label'] ?? 'Liste')  !!} </h3>

                @if ($is_single)
                    <a href="{{ route('publisher.edit', ['id'=>$single_pages[$listable]['id']]) }}" class="mt-n1 mb-2 btn btn-warning btn-sm">
                        Éditer
                    </a>
                @endif


                @if ($is_listable)
                    @if ($has_page)
                        <a href="{{ route('publisher.edit', ['id'=>$listable_pages[$listable]['id']]) }}" class="d-block mt-n3 mb-2">
                            Présentation
                        </a>
                    @endif
                    @php
                        $items_q = Publisher::where(['type'=>$listable_type, 'parent'=> $parent ])->with('globalMeta')->orderBy('id','desc');
                        $items_count = $items_q->count();
                        $items = $items_q->take(5)->get();
                        $can_add = (!$has_limit or ($items_count < $has_limit)) && !in_array('is_single', $configuration);
                    @endphp

                    @if (!$items->isEmpty())
                        <table class="table">
                            <thead>
                            <tr>
                                @if ($with_image)
                                    <th>Image</th>
                                @endif
                                <th>Titre</th>
                                <th>Edité</th>
                                @if (!in_array('hide_status', $configuration))
                                    <th>Publié</th>
                                @endif
                                <th>Actions</th>
                                @if (!in_array('no_links', $configuration))
                                    <th>Afficher</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>

                            @php
                            $url_prefix = '';
                            if (!in_array('no_links', $configuration)) {
                                $url_prefix = $configuration['url_prefix'];
                                if ($configuration['url_prefix'] == 'parent' && strstr($listable,'_list')) {
                                    $url_prefix = $data->content->url;
                                }
                            }
                            @endphp

                            @foreach($items as $item)
                                <?php
                                $deletable = !in_array('is_single', $configuration) && !in_array('no_delete', $configuration);
                                $title = array_key_exists('title', $configuration) ? strip_tags($item->content->{$configuration['title']}) : $item->title;
                                ?>
                                <tr>
                                    @if ($with_image)
                                        <td style="background:url({{ str_replace('images/','images/th_',$item->imageUrl()) }});background-size:cover"></td>
                                    @endif
                                    <td>{{ $title }}</td>
                                    <td>{{ $item->updated_at->format('d/m/Y à H:i') }}</td>
                                    @if (!in_array('hide_status', $configuration))
                                        <td class="status bg-{!! $item->published ? 'success':'danger' !!}">
                                            {!! trans('aboleon.framework::ui.' . ($item->published ? 'online': 'offline')) !!}
                                        </td>
                                    @endif
                                    <td class="nowrap">
                                        <a class="btn btn-sm btn-warning" href="{!! url('panel/Publisher/pages/edit/'. $item->id ) !!}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @if ($deletable)
                                            <a class="btn btn-sm btn-danger" href="{!! url('panel/Publisher/pages/remove/'. $item->id ) !!}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        @endif
                                    </td>

                                    @if (!in_array('no_links', $configuration))
                                        <td class="langs">
                                            @if(count(config('project.config.locales'))>1)
                                                @foreach(config('project.config.locales') as $l)
                                                    <a target="_blank" href="{!! url($l .'/'. $item->globalMeta->where('lg',$l)->first()->url) . ($item->access_key ? '?access_key='.$item->access_key : null) !!}">
                                                        <img width="18" src="{!! asset('vendor/flags/4x3/'.$l.'.svg') !!}" alt="{{ trans('aboleon.framework::ui.lang.'.$l) }}" title="{{ trans('aboleon.framework::ui.lang.'.$l) }}"/>
                                                    </a>
                                                @endforeach
                                            @else

                                                <a title="Afficher" class="btn btn-sm btn-info" target="_blank" href="{!! url($url_prefix.'/'.$item->globalMeta->where('lg',app()->getLocale())->first()->url) !!}">
                                                    <i class="fas fa-link"></i>
                                                </a>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if (!in_array('is_single', $configuration))
                            <div class="notice">
                                @if ( ($has_limit && $has_limit > 5) or !$has_limit )
                                    Vous voyez les 5 derniers éléments ajoutés à cette liste
                                @else
                                    Maximum {{ $has_limit }} enregistrements
                                @endif
                                <div>
                                    @if ($can_add)
                                        <a href="#" class="trigger-ajaxable btn btn-sm btn-success">Ajouter</a>
                                    @endif
                                    <a class="btn btn-info btn-sm" href="{!! url('panel/Publisher/pages/list/'.$listable.'/parent/'.(strstr($listable, '_list') ? $data->type.'__'.$data->id : $data->id)) !!}">Voir tout</a>
                                </div>
                            </div>
                        @endif
                    @else
                        {!! ResponseRenderers::warning('Aucun enregistrement') !!}
                    @endif

                    @if ($can_add)
                        @php
                            // TODO: l'ajout en mode ajax ne prends pas en compte la limite configurée
                        @endphp
                        <div class="ajax_subpages form" data-url="panel/Publisher/ajax" {!! $items->isEmpty() && $can_add ? 'style="display:block"':''  !!}>
                            <button class="ajaxable btn btn-success btn-sm">Ajouter</button>
                            &nbsp;&nbsp;
                            <input type="hidden" name="ajax_object" value="PagesCreateContent">
                            <input type="hidden" name="ajax_type" value="{!! str_replace('_list','',$listable) !!}">
                            <input type="hidden" name="ajax_parent" value="{!! $parent !!}">
                            <input type="hidden" name="ajax_action" value="addAjaxPage">
                            <input type="hidden" name="ajax_with_redirect"/>
                        </div>
                    @endif
                @endif

            @endif
        </div>
    @endforeach
@endif

@push('js')
    <script>
        $(function () {
            $('.trigger-ajaxable').click(function (e) {
                e.preventDefault();
                var target = $(this).closest('.bloc-editable').find('.ajax_subpages');
                target.find('button').addClass('hidden').trigger('click');
                target.fadeIn();
            });
        });
    </script>
@endpush
