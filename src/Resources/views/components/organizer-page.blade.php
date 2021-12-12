@if ($data)
    @php
        $listable_options = [];
    @endphp

    @foreach($data as $section)
        <section id="section_{{ $section->id }}" data-id="{{ $section->id }}">
            <h2>{{ $section['title'] }}</h2>
            @if ($section->has('children'))
                @foreach($section->children as $element)
                    @php
                        $uploadable = in_array($element->type, ['image','gallery']);
                    @endphp
                    <div id="node_{{ $element->id }}" data-id="{{ $element->id }}" class="mb-3{{ $uploadable ? ' uploadable' : '' }}"{!! $uploadable ? ' data-identifier="node_'.$element->id.'"' : '' !!}>
                        @php
                            $name = 'elements['.$section->id .'][children]['. $element->id .']';
                            $name_loc = $name.'['. $locale .']';
                            $content = $page->content->where('node_id', $element->id);
                            $node = $content->first();
                            $value = $node ? ($node->value ?: $node->translation('content', $locale)) : null;
                        @endphp
                        @switch($element->type)
                            @case('input')
                            <x-aboleon.framework-bootstrap-input name="{{ $name_loc }}" :value="$value" :label="$element->title"/>
                            @break
                            @case('email')
                            <x-aboleon.framework-bootstrap-input type="email" name="{{ $name_loc }}" :value="$value" :label="$element->title"/>
                            @break
                            @case('editor_lite')
                            @case('editor')
                            <x-aboleon.framework-bootstrap-textarea :className="$element->type == 'intro' ? 'simplified' :'textarea'" name="{{ $name_loc }}" :value="$value" :label="$element->title"/>
                            @break
                            @case('list')
                            @php
                                $list_id = $element['params']['list_id'];
                                    if (!array_key_exists($list_id, $listable_options)) {
                                        if($element['params']['tag'] == 'select') {
                                            $listable_options[$list_id] =\Aboleon\Publisher\Models\Lists::where('list_id', $list_id)->orderBy('content')->pluck('value')->toArray();
                                        }
                                        if ($element['params']['tag'] == 'checkbox') {
                                            $listable_options[$list_id] = \Aboleon\Publisher\Models\Lists::where('list_id', $list_id)->whereNull('parent')->with(['children'])->get();
                                        }
                                    }
                            @endphp
                            @if (isset($element['params']['tag']))
                                <div class="listable list_id_{{$list_id}}" data-type="{{ $element['params']['tag'] }}" data-list-id="{{$list_id}}">
                                    @switch($element['params']['tag'])
                                        @case('select')
                                        <x-aboleon.framework-bootstrap-select name="{{ $name }}" :values="$listable_options[$list_id]" :label="$element->title" :affected="$value"/>
                                        @break

                                        @case('checkbox')
                                        @php
                                            $currentCategories = $content ? $content->pluck('value')->toArray() : [];
                                        @endphp
                                        <strong>{{ $element->title }}</strong>
                                        <div class="nested_categories">
                                            <input type="hidden" name="{!! $name !!}[arrayable]" value="[]">
                                            <ul>
                                                @if($listable_options[$list_id]->isNotEmpty())
                                                    @foreach($listable_options[$list_id] as $value)
                                                        @php
                                                            $count = $value->children->count();
                                                            $id= 'ch_' . $value->id;
                                                        @endphp
                                                        <li data-parent="main" data-id="{{ $value->id }}">
                                                            <div class="form-check">
                                                                <input class="form-check-input" id="{{ $id }}" type="checkbox" name='{!! $name !!}[values][]' value="{!! $value->id !!}" {!! (in_array($value->id, $currentCategories) ? "checked='checked'" : null) !!}/>
                                                                <label class="form-check-label" for="{{ $id }}">
                                                                    <span{!! ($count ? ' class="has"':'') !!}>{!! $value->content . ($count ? ' ('.$count.')' : '')!!}</span>
                                                                </label>
                                                                <span class="sublistable btn btn-xs btn-info"><i class="fas fa-plus"></i></span>
                                                            </div>
                                                            {!! $value->printNestedTree($currentCategories, $name.'[values][]')!!}
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                        @break

                                    @endswitch
                                    <span class="btn btn-info btn-sm create-list-entry mt-2">Créer</span>


                                    {{-- d($listable_options[$list_id]) --}}
                                </div>
                            @endif
                            @break
                            @case('image')
                            @case('gallery')
                            @php
                                $dims = (new \Aboleon\Publisher\Models\FileUploadImages)->setWidthHeight($element->params['dim']);
                            @endphp
                            <label for="">{{ $element->title }}</label>
                            <div class="controls mt-2 mb-4">
                                <span class="subcontrol uploader"><i class="fa fa-download"></i> Télécharger</span>
                            </div>
                            <div id="uploader-node_{{ $element->id }}"></div>
                            <div class="uploaded">
                                @forelse ($element->media as $media)

                                    <div class="unlinkable uploaded-image" data-id="{{ $media->id }}">
                                        <a target="_blank" href="{{ asset(Storage::disk('publisher')->url($page->key().'/'.$dims[0]['width'].'_'.$media->content)) }}">
                                            <img src="{{ asset(Storage::disk('publisher')->url($page->key().'/'.$dims[count($dims)-1]['width'].'_'.$media->content)) }}" alt=""/>
                                        </a>
                                        <div>
                                            @foreach($dims as $dim)
                                                <span>{{ $dim['width'] .' x '. $dim['height'] }}</span>
                                            @endforeach
                                        </div>
                                        <span class="btn btn-sm btn-danger unlink"><i class="fas fa-times"></i></span>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                            @break
                        @endswitch
                    </div>
                @endforeach
            @endif

        </section>
    @endforeach
@endif