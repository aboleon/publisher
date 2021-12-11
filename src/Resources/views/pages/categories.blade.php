<x-aboleon.publisher-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Catégories
        </h2>
    </x-slot>


<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
           <tr>
            <th>#</th>
            <th style="width: 50%">{!! trans('aboleon.framework::ui.meta.title')!!}</th>
            <th>{!! trans('aboleon.framework::ui.meta.status')!!}</th>
            <th>{!! trans('aboleon.framework::ui.meta.updated') !!}</th>
            <th>{!! trans('aboleon.framework::ui.edit') !!}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pages as $page)
        <tr>
            <td>{{$page->id}}</td>
            <td>{!! $page->meta->title!='' ? $page->meta->title : trans('aboleon.framework::ui.untitled')) !!}
                @if($page->access_key)<span class='label label-danger'>{{ trans('aboleon.framework::ui.meta.access_key') }}</span>@endif
            </td>
            <td class="status {!! $page->published ? 'success':'danger' !!}">
                {!! trans('aboleon.framework::ui.' . ($page->published ? 'online': 'offline')) !!}
            </td>
            <td class="time">{!! $page->updated_at !!}</td>
            <td>
                <a class="btn btn-sm btn-warning" href="{!! url('panel/Publisher/pages/edit/'. $page->id ) !!}">
                    <i class="fas fa-pen"></i>
                </a>
                <a class="btn btn-sm btn-danger" href="{!! url('panel/Publisher/pages/remove/'. $page->id ) !!}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
        @empty
        No Pages Yet
        @endforelse

    </tbody>
</table>


@push('js')
@include('aboleon.framework::lib.dataTables')
@endpush

</x-aboleon.publisher-layout>