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

                            @case('link')
                            <h2 class="pt-4">Lien</h2>
                            <x-aboleon.framework-bootstrap-input name="{{ $name_loc.'[link]' }}" :value="$value['link'] ?? null" label="URL"/>
                            <div class="my-2">
                                <x-aboleon.framework-bootstrap-input name="{{ $name_loc.'[btn]' }}" :value="$value['btn'] ?? null" label="Texte bouton" class=""/>
                            </div>
                            <x-aboleon.framework-bootstrap-select name="{{ $name_loc.'[target]' }}" :values="['_self'=>'-- par défaut--','_blank'=>'Nouvelle fenêtre']" label="Texte bouton" :affected="$value['target'] ?? null"/>
                            @break

                            @case('editor_lite')
                            @case('editor')
                            <x-aboleon.framework-bootstrap-textarea :className="$element->type == 'intro' ? 'simplified' :'textarea'" name="{{ $name_loc }}" :value="$value" :label="$element->title"/>
                            @break


                            @case('associated')
                            @php
                                $associated = Publisher::where('type', $element['params']['associated_id'])->select('title','id','published')->get();
                                $associated_config = $page->config['associated'][$element['params']['associated_id']] ?? [];
                                $associated_ids = $associated_config ? collect($associated_config['id']) : collect();
                            @endphp
                            <h4>{{$element->title}}</h4>
                            <div class="associated associated_id_{{ $element['params']['associated_id'] }} sortables">
                                @forelse($associated as $ass)
                                    <x-aboleon.framework-bootstrap-checkbox class="sortable" name="meta[config][associated][{{$element['params']['associated_id']}}][id][]" :id="$ass->id" :label="$ass->title . (is_null($ass->published) ? ' <span class=text-danger>Hors ligne</span>':'')" :affected="$associated_ids" />
                                        <input type="hidden" class="order" name="meta[config][associated][{{$element['params']['associated_id']}}][order][{{ $ass->id }}]" value="{{ $associated_config['order'][$ass->id] ?? $loop->iteration }}"/>
                                @empty
                                @endforelse
                            </div>
                            @break


                            @case('list')
                            @php
                                $list_id = $element['params']['list_id'];
                                    if (!array_key_exists($list_id, $listable_options)) {
                                        if(in_array($element['params']['tag'], ['select','radio'])) {
                                            $listable_options[$list_id] =\Aboleon\Publisher\Models\Lists::where('list_id', $list_id)->orderBy('content')->pluck('content')->toArray();
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
                                                                    <span{!! ($count ? ' class="has"':'') !!}>{!! $value->content !!}</span> {{ ($count ? ' ('.$count.')' : '') }}
                                                                </label>
                                                                <span class="sublistable btn btn-xs btn-info float-end"><i class="fas fa-plus"></i></span>
                                                                <span class="edit btn btn-xs btn-warning float-end"><i class="fas fa-pen"></i></span>
                                                                @role('dev')
                                                                <span class="float-end me-1">{{ $value->id }}</span>
                                                                @endrole
                                                            </div>
                                                            {!! $value->printNestedTree($currentCategories, $name.'[values][]')!!}
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                        @break

                                        @case('radio')
                                        <strong>{{ $element->title }}</strong>
                                        <div class="listables">
                                            <x-aboleon.framework-bootstrap-radio name="{{ $name_loc }}" label="" :values="$listable_options[$list_id]" :affected="$value"/>
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
                                        <a target="_blank" href="{{ Storage::disk('publisher')->url($page->key().'/'.$dims[0]['width'].'_'.$media->content) }}">
                                            <img src="{{ Storage::disk('publisher')->url($page->key().'/'.$dims[count($dims)-1]['width'].'_'.$media->content) }}" alt=""/>
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
@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/jquery-ui/jquery-ui.min.css') }}">
@endpush
@push('js')
    <script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
      $(function () {
        $('.sortables').each(function () {
          let c = $(this);
          $(this).sortable({
            stop: function (event, ui) {
              c.find('.order').each(function (index) {
                $(this).val(index);
                console.log($(this).find('.order'), index);
              });
            },
          });
        });
      });
    </script>
@endpush