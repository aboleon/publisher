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
                <th style="width: 50%">{!! trans('aboleon.publisher::ui.meta.title')!!}</th>
            <!--<th>{!! trans('aboleon.publisher::ui.meta.status')!!}</th>-->
                <th>{!! trans('aboleon.publisher::ui.meta.updated') !!}</th>
                @if (!$type)
                    <th>{!! trans('aboleon.publisher::ui.meta.type') !!}</th>
                @endif
                <th>Parent</th>
                <th>{!! trans('aboleon.framework::ui.edit') !!}</th>
            <!--<th>{!! trans('aboleon.framework::ui.buttons.show') !!}</th>-->
            </tr>
            </thead>

            <tbody>
            @forelse($pages as $page)
                <tr>
                    <td>{{$page->id}}</td>
                    <td>{!! $page->title ?? trans('aboleon.framework::ui.untitled') !!}</td>
                <!--<td class="status {!! $page->published ? 'success':'danger' !!}">
                        {!! trans('aboleon.framework::ui.' . ($page->published ? 'online': 'offline')) !!}
                        </td> -->
                    <td class="time">{!! $page->updated_at !!}</td>
                    @if(!($type))
                        <td>{!! $page->configs->title !!}</td>
                    @endif
                    <td>{!! ($page->hasParent ? ($page->hasParent->meta->title ?? null) : null) !!}</td>
                    <td>
                        <x-aboleon.framework-edit-link :route="route('aboleon.publisher.pages.edit', $page->id)"/>
                        <!--<x-aboleon.framework-delete-link :route="route('aboleon.publisher.pages.destroy', $page->id)" :reference="$page->id" />-->
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