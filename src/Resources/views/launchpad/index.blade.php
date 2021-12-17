<x-aboleon.publisher-layout title="Pages">

    <div class="mb-3 text-center">
        <a class="btn btn-sm btn-success" href="{{ route('aboleon.publisher.launchpad.create') }}">Créer</a>
    </div>


        <x-aboleon.framework-response-messages/>

        @include('aboleon.publisher::components.instant-search', ['scope'=>'', 'classes'=>'w-100 mb-3'])

        <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
            <thead>
            <tr>
            <tr>
                <th>#</th>
                <th style="width: 50%">{!! trans('aboleon.publisher::ui.meta.MetaSlug') !!}</th>
                <th>ID</th>
                <th>{!! trans('aboleon.framework::ui.created') !!}</th>
                <th>{!! trans('aboleon.framework::ui.buttons.actions') !!}</th>
            </tr>
            </thead>

            <tbody>
            @forelse($pages as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{!! $item->meta->title ?? ($item->title ?? trans('aboleon.publisher::ui.untitled')) !!}</td>
                    <td>{!! $item->type ?? null !!}</td>
                    <td class="time">{!! $item->created_at !!}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-xs btn-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuLink_submenu_launchpad_{{$item->id}}" data-bs-toggle="dropdown"
                                aria-expanded="false">Actions
                            </button>

                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink_launchpad_{{$item->id}}">
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('aboleon.publisher.launchpad.edit', $item->id) }}">Éditer</a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('aboleon.publisher.launchpad.pages.create', $item->type) }}">Créer une page</a>
                                </li>
                                <x-aboleon.framework-delete-link-actions modalreference="publisher_action_{{ $item->id }}" title="Supprimer"/>
                            </ul>
                        </div>
                        <x-aboleon.framework-delete-link-modal :reference="$item->id"
                                             :route="route('aboleon.publisher.launchpad.destroy', $item->id)"
                                             question="Supprimer cette configuration ?"
                                             :title="trans('aboleon.framework::ui.buttons.delete')"
                                             modalreference="publisher_action_{{ $item->id }}"/>
                    </td>
                </tr>
            @empty
                <x-aboleon.framework-a
                {{ trans('aboleon.framework::ui.database.no_records') }}
            @endforelse

            </tbody>
        </table>
        {{ $pages->links() }}
</x-aboleon.publisher-layout>