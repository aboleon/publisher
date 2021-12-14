<x-aboleon.publisher-layout title="Contenus {{ $type ? ' de type '.$type->title : '' }}">

    <div class="mb-3 text-center">
        @if ($type)
            <a class="btn btn-sm btn-success" href="{{ route('aboleon.publisher.launchpad.pages.create', $type->type) }}">Créer</a>
        @endif
    </div>

    <div class="bg-white container my-3 p-3 rounded">

        <x-aboleon.framework-response-messages/>

        {{--@include('aboleon.publisher::components.instant-search', ['scope'=>'', 'classes'=>'w-100 mb-3'])--}}

        <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
            <thead>
            <tr>
            <tr>
                <th>#</th>
                <th>{!! trans('aboleon.publisher::ui.meta.title')!!}</th>
                <th class="text-center" style="width: 100px">{!! trans('aboleon.publisher::ui.meta.status')!!}</th>
                <th style="width: 180px" class="text-center">{!! trans('aboleon.publisher::ui.meta.updated') !!}</th>
                @if (!$type)
                    <th>{!! trans('aboleon.publisher::ui.meta.type') !!}</th>
                @endif
                <th style="width:88px"></th>
            </tr>
            </thead>

            <tbody>
            @forelse($pages as $page)
                <tr>
                    <td>{{ $page->id}}</td>
                    <td>{!! $page->title ?? trans('aboleon.framework::ui.untitled') !!}</td>
                    <td class="status {!! $page->published ? 'success':'danger' !!}">
                        {!! trans('aboleon.framework::ui.' . ($page->published ? 'online': 'offline')) !!}
                    </td>
                    <td class="time text-center">{!! $page->updated_at->format('d/m/Y à H:i') !!}</td>
                    @if(!($type))
                        <td>{!! $page->configs->title !!}</td>
                    @endif
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-xs btn-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuLink_submenu_actions_{{$page->id}}" data-bs-toggle="dropdown"
                                    aria-expanded="false">Actions
                            </button>

                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink_actions_{{$page->id}}">
                                <li>
                                    <a target="_blank" href="{{ url($page->url) }}" class="dropdown-item"><i class="fas fa-file"></i> Visualiser</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('aboleon.publisher.pages.edit', $page->id) }}"><i class="fas fa-pen"></i> Éditer</a>
                                </li>

                                <x-aboleon.framework-delete-link-actions modalreference="page_action_{{ $page->id }}" icon='<i class="fas fa-trash"></i>' title="Supprimer"/>
                            </ul>
                        </div>
                        <x-aboleon.framework-delete-link-modal :reference="$page->id"
                                                               :route="route('aboleon.publisher.pages.destroy', $page->id)"
                                                               question="Supprimer ce contenu ?"
                                                               :title="__('aboleon.framework::ui.buttons.delete')"
                                                               modalreference="page_action_{{ $page->id }}"/>
                    </td>
                </tr>
            @empty
                {!! ResponseRenderers::warning(trans('aboleon.framework::ui.database.no_records')) !!}
            @endforelse

            </tbody>
        </table>
        {{ $pages->links() }}
    </div>
</x-aboleon.publisher-layout>