{!! Aboleon\Publisher\Models\Nav::section_navbar($data->hasParent->hasParent) !!}
<li>
    <a href="{!! url('panel/Publisher/pages/edit/'.$data->hasParent->hasParent->parent.'/subpage/'.str_replace('section_','', $data->hasParent->hasParent->type)) !!}">{!! $data->hasParent->hasParent->title !!}</a>
</li>
<li>
    <a href="{!! url('panel/Publisher/pages/list/'.$data->parent.'/subpage') !!}">Sous Pages</a>
</li>
<li>
    <a href="{!! url('panel/Publisher/pages/edit/'.$data->hasParent->id) !!}">
        {!! $data->hasParent->meta->title !!}</a>
    </li>
    <li>
        <a href="{!! url('panel/Publisher/pages/list/'.$data->hasParent->id.'/'.$data->type) !!}">
            Sous-pages {!! $data->hasParent->meta->title !!}</a>
        </li>