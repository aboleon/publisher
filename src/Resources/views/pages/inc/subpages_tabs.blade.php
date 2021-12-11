@php
$translation_anchor = $data->type;
$subpageConfig = $config;
$is_subpage = in_array('subpage',request()->segments());
if($is_subpage) {
    $subpageConfig = config('project.content.'.$data->hasParent->type);
    $translation_anchor = $data->hasParent->type;
}
$node_id = $is_subpage ? $data->parent : $data->id;
$last_segment = Arr::last(request()->segments());
@endphp


@if (Arr::has($subpageConfig,'has.subpages') && Arr::has($subpageConfig,'has.subpages.presentation') && $subpageConfig['has']['subpages']['presentation'] == 'tabs')
<ul id="subpagesTab" class="nav nav-tabs">
        <li {!! !$is_subpage ? 'class="active"' : null !!}>
            <a href="{!! url('panel/Publisher/pages/edit/'.($is_subpage ? $data->parent : $data->id)) !!}">
                {{ $is_subpage ? $data->hasParent->meta->nav_title : $data->content->nav_title }}
            </a>
        </li>

    @if (Arr::has($subpageConfig,'has.subpages.list'))
        <?php $subpages = $subpageConfig['has']['subpages']['list'];?>
        @foreach($subpages as $subpage)
        <li {!! ($last_segment == $subpage) ? ' class="active"' : null !!}>
            <a href="{!! url('panel/Publisher/pages/edit/'.$node_id.'/subpage/'.$subpage) !!}">
                {{ trans('site.config.'.$translation_anchor.'.subpages.'.$subpage) }}
            </a>
        </li>
        @endforeach
    @else
        <?php $subpages = $is_subpage ? Publisher::where(['parent'=>$data->parent, 'type'=>'subpages'])->with('meta')->get() : $data->children->where('type','subpages') ;?>
        @foreach($subpages as $subpage)
        <li {!! ($last_segment == $subpage->id) ? ' class="active"' : null !!}>
            <a href="{!! url('panel/Publisher/pages/edit/'.$subpage->parent.'/subpage/'.$subpage->id) !!}">
                {{ $subpage->meta->nav_title }}
            </a>
        </li>
        @endforeach
    @endif

    @push('css')
    <style>#btn_save_quit { display: none; } </style>
    @endpush
</ul>
@endif
