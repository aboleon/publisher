@if ($data->hasParent && strstr($data->hasParent->type, 'section'))
@include('aboleon.publisher::pages.inc.ariane.section')
@else
@includeIf('aboleon.publisher::pages.inc.ariane.'.$data->type)
@endif
<li class="active">@yield('ariane_default', $data->meta->title ?? null)</li>
