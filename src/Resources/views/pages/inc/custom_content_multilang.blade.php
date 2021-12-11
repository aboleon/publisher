@if (isset($multiLangCustomContent))
    @php
        global $replicate_tags;
        $sc_col = 'col-sm-10';
        $replicas = $multiLangCustomContent->filter(function($val) { return array_key_exists('replicate', $val); })->count();
    @endphp

    @if ($replicas>0)
        @push('css')
            {!! csscrush_inline(public_path('aboleon/publisher/css/replicator.css'), ['minify'=>true]) !!}
        @endpush
        @push('js')
            <script src="{!! asset('aboleon/publisher/js/replicate_custom_content.js') !!}"></script>
        @endpush
    @endif

    @php
        $replicate_tags = [];
        $replicate_ids = [];
    @endphp

    @foreach($multiLangCustomContent as $sc)

        @php
            $has_label = array_key_exists('label', $sc);
            $sc_col = 12/count($sc['fields']);
            $replicate = array_key_exists('replicate',$sc);
            $replicate_fields = array_key_exists('replicate_fields', $sc);
            $is_grid = (array_key_exists('cols', $sc) && $sc['cols'] == 'grid');
        @endphp

        <!-- Label Of Field Group -->
        @if ($has_label)
            <h2 class="foldable {{ $loop->iteration == 1 ? 'toggled':'' }}">
                {{ strstr($sc['label'], 'trans_') ? trans(str_replace('trans_','', $sc['label'])) : $sc['label'] }}
            </h2>
        @endif

        @if ($replicate)
            <!-- begin replicate -->
            <div class="replica_container">

                @php
                    $replicate_tag = $sc['replicate']['id'];
                    $replicate_tags[$replicate_tag] = '';
                    $replica_content = $data->customContent->filter(function($val) use($replicate_tag) { return strstr($val['field'], $replicate_tag); });
                @endphp

                @if (!$replica_content->isEmpty())
                    <?php $replicate_labels = $replica_content->filter(function ($value) use ($sc) {
                        return $value->field == 'replicate_' . $sc['replicate']['id'];
                    });?>

                    @foreach($replicate_labels as $label)
                        <?php $replicate_id = $label['value'];?>

                        <div class="replicate row {{ $replicate_id }}">
                            <input type="hidden" class="replicate_id" name="replicate_{!! $sc['replicate']['id'] !!}[]" value="{!! $replicate_id !!}"/>
                            @foreach($sc['fields'] as $k=>$v)

                                @php
                                    $custom_value_exists = $replica_content->where('field',$k.'_'.$replicate_id)->first();
                                    $custom_lang_value = !is_null($custom_value_exists) ? $custom_value_exists->langs->where('lg', $l)->first() : null;
                                    $custom_value = !is_null($custom_lang_value) ? $custom_lang_value->content : null;
                                    $field_type = $v['type'] ?? 'text';
                                @endphp

                                <div class="space-4"></div>
                                <div class="form {!! ($is_grid && Arr::has($v, 'grid')) ? $v['grid'] : 'col-sm-'.$sc_col !!}">

                                    <span class="content_key hidden">{!! $k !!}</span>
                                    @if (Arr::has($v, 'label'))
                                        <h{{ $has_label ? 5 : 3 }} class="header blue smaller">{{ strstr($v['label'], 'trans_') ? trans(str_replace('trans_','', $v['label'])) : $v['label'] }}</h{{ $has_label ? 5 : 3 }}>
                                    @endif

                                    @switch($field_type)

                                        @case('email')
                                        @case('number')
                                        @case('text')
                                        <?php $content = $data->customContent->where('field', $k . '_' . $replicate_id)->first();?>
                                        <input type="{!! $field_type !!}" name="replica_content[{!! $k !!}][{!! $replicate_id ?? null !!}][{{ $l }}]" value="{{ $custom_value }}" class="form-control col-sm-11">
                                        @break

                                        @case('textarea')
                                        <?php $content = $data->customContent->where('field', $k . '_' . $replicate_id)->first();?>
                                        <textarea name="replica_content[{!! $k !!}][{!! $replicate_id ?? null !!}][{{ $l }}]" class="form-control col-sm-11">{{ $custom_value }}</textarea>
                                        @break

                                        @case('radio')
                                        @foreach($v['options'] as $optionKey => $option)
                                            <?php $optionContent = $data->customContent->where('field', $k . '_' . $replicate_id)->first();?>
                                            <input type="radio" name="replica_content[{!! $k !!}][{!! $replicate_id ?? null !!}]" value="{!! $optionKey !!}" {!! !is_null($optionContent) && $optionContent->content == $optionKey ? 'checked="checked"' : (in_array('default', $option) ? 'checked="checked"' : null) !!}/> {!! $option['label'] !!}
                                            <br>
                                        @endforeach
                                        @break

                                    @endswitch
                                </div>
                            @endforeach
                        <!-- Begin replicated_fields -->
                            @if ($replicate_fields)
                                @if (Arr::has($sc['replicate_fields'], 'label'))
                                    <strong class="replicate_fields clear">{{ strstr($sc['replicate_fields']['label'], 'trans_') ? trans(str_replace('trans_','', $sc['replicate_fields']['label'])) : $sc['replicate_fields']['label'] }}</strong>
                                @endif

                                @php
                                    $base_dynamic_text_field = current(array_keys(array_filter($sc['replicate_fields']['fields'], function($val) { return $val['type'] == 'text'; })));
                                    $dynamic_count = $replica_content->filter(function($val) use($replicate_id, $base_dynamic_text_field) { return $val['field'] == $base_dynamic_text_field.'_'. $replicate_id; })->count();
                                    $dynamic = $replica_content->filter(function($val) use($replicate_id) { return strstr($val['field'], $replicate_id); });
                                    foreach($sc['replicate_fields']['fields'] as $k=>$v) {
                                        ${'dynamic_'.$k} = $replica_content->filter(function($val) use($replicate_id, $k) { return $val['field'] == $k.'_'.$replicate_id; })->values();
                                    }
                                @endphp

                                @if ($dynamic_count)
                                    @for($i=0;$i<$dynamic_count;++$i)
                                        @include('aboleon.publisher::pages.inc.replicate_dynamic_fields')
                                    @endfor
                                @else
                                    @include('aboleon.publisher::pages.inc.replicate_dynamic_fields')
                                @endif

                                <button data-replica="replicate_fields" class="btn btn-sm btn-info replicate_fields replicatable">Ajouter une ligne</button>

                        @endif
                        <!-- End of replicated_fields -->
                            <button class="btn btn-sm btn-danger delete_replica delete_replicate_section" data-code="{{ $replicate_id }}" data-target="replicate">{!! $sc['replicate']['delete'] ?? 'Supprimer' !!}</button>
                        </div>
                    @endforeach

                @else {{-- no replica content in DB --}}

                <template class="replicate" data-lang="{{ $l }}">
                    <input type="hidden" class="replicate_id" name="replicate_{!! $sc['replicate']['id'] !!}[]" value="{!! Str::random(8) !!}"/>
                    @foreach($sc['fields'] as $k=>$v)

                        <div class="space-4"></div>
                        <div class="form {!! ($is_grid && Arr::has($v, 'grid')) ? $v['grid'] : 'col-sm-'.$sc_col !!}">

                            <span class="content_key hidden">{!! $k !!}</span>
                            @if (Arr::has($v, 'label'))
                                <h{{ $has_label ? 5 : 3 }} class="header blue smaller">{{ strstr($v['label'], 'trans_') ? trans(str_replace('trans_','', $v['label'])) : $v['label'] }}</h{{ $has_label ? 5 : 3 }}>
                            @endif

                            @switch($v['type'])

                                @case('email')
                                @case('number')
                                @case('text')
                                <input type="{!! $v['type'] !!}" name="replica_content[{!! $k !!}][{!! $replicate_id ?? null !!}]" value="" class="form-control col-sm-11">
                                @break

                                @case('textarea')
                                <textarea name="replica_content[{!! $k !!}][{!! $replicate_id ?? null !!}]" class="form-control col-sm-11"></textarea>
                                @break

                                @case('radio')
                                @foreach($v['options'] as $optionKey => $option)
                                    <input type="radio" name="replica_content[{!! $k !!}][{!! $replicate_id ?? null !!}]" value="{!! $optionKey !!}" {!! in_array('default', $option) ? 'checked="checked"' : null !!}/> {!! $option['label'] !!}
                                    <br>
                                @endforeach
                                @break

                            @endswitch

                        </div>
                    @endforeach
                    @if ($config->has('replicate_fields'))
                        @include('aboleon.publisher::pages.inc.replicate_dynamic_fields')
                        <button data-replica="replicate_fields" class="btn btn-sm btn-info replicate_fields replicatable">Ajouter une ligne</button>
                    @else
                        <div style="clear:both"></div>
                    @endif
                    <button class="btn btn-sm btn-danger delete_replica delete_replicate_section" data-target="replicate">{!! $sc['replicate']['delete'] ?? 'Supprimer' !!}</button>
                </template>

                @endif

                <button class="btn btn-sm btn-success replicate">{!! $sc['replicate']['button'] ?? 'Ajouter' !!}</button>
            </div>

        @else
            <!-- begin custom content -->
            <div class="row custom-content {{ $loop->iteration == 1 ? 'unfolded':'folded' }}">
                <div class="form-group col-sm-12">

                    <?php $page_custom_data = $data->customContent->pluck('value', 'field')->toArray();?>

                    @foreach($sc['fields'] as $k=>$v)

                        @php
                            $custom_value_exists = $data->customContent->where('field',$k)->first();
                            $custom_value_lang = !is_null($custom_value_exists) ? $custom_value_exists->langs->where('lg', $l)->first() : null;
                            $custom_value = !is_null($custom_value_lang) ? $custom_value_lang->content : null;
                            $field_type = $v['type'] ?? 'text';
                        @endphp

                        <div data-multilang="true" class="form {{ $v['grid'] ?? 'col-sm-12' }}">
                            <span class="content_key hidden">{!! $k !!}</span>
                            @if (Arr::has($v, 'label'))
                                <h5>{{ strstr($v['label'], 'trans_') ? trans(str_replace('trans_','', $v['label'])) : $v['label'] }}</h{{ $has_label ? 5 : 3 }}>
                                    @endif

                                    @switch($field_type)

                                        @case('email')
                                        @case('number')
                                        @case('text')
                                        <input type="{!! $field_type !!}" name="multilang_custom_content[{!! $k !!}][{{ $l }}]" value="{{ $custom_value }}" class="form-control col-sm-11">
                                        @break

                                        @case('textarea')
                                        <textarea class="form-control {{ $v['class'] ?? '' }}" style="min-height: 100px" name="multilang_custom_content[{!! $k !!}][{{ $l }}]">{{ $custom_value }}</textarea>
                                        @break

                                        {{-- A FAIRE EN MULT LG --}}

                                        @case('datepicker')
                                        <input type="text" name="multilang_custom_content[{!! $k !!}][{{ $l }}]" value="{{ $page_custom_data && array_key_exists($k, $page_custom_data) ? $page_custom_data[$k] : null }}" class="form-control col-sm-11 datepicker" data-config="{!! (Arr::has($v, 'config') ? AboleonPublisherHelpers::implodeWithKeys($v['config']) : null) !!}"/>
                                        @break

                                        @case('select')

                                        @if (Arr::has($v,'values'))
                                            <?php $values = $v['values'][config('app.fallback_locale')];?>
                                        @elseif(Arr::has($v, 'query'))
                                            @if (array_key_exists('parent', ($v['query']['arguments'])))
                                                <?php $v['query']['arguments']['parent'] = $data->parent; ?>
                                            @endif
                                            <?php $values = Publisher::{$v['query']['method']}($v['query']['arguments']); ?>
                                        @endif
                                        <select class="form-control" id="custom_content_{{ $k }}" name="multilang_custom_content[{!! $k !!}]">
                                            @if (!$values)
                                                <option value=0>Aucun choix disponible</option>
                                            @endif
                                            @foreach($values as $option=>$value)
                                                <option value="{{ $option }}"{{ $page_custom_data && array_key_exists($k, $page_custom_data) && $page_custom_data[$k] == $option ? ' selected' : null }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @if (Arr::has($v, 'query'))
                                            <div class="ajax_subpages">
                                                <strong style="padding-bottom:10px;display: block;">Ajouter un élément</strong>
                                                <input name="ajax_title" type="text" style="min-width: 300px" placeholder="Titre">
                                                <button class="ajaxable btn btn-info btn-sm">Ajouter un élément</button>
                                                <br>
                                                <a href="{!! url('panel/Publisher/pages/list/'. $data->parent .'/category') !!}">Voir tous les éléments</a>
                                                <input type="hidden" name="ajax_object" value="pages">
                                                <input type="hidden" name="ajax_arguments" value="{!! http_build_query($v['query']['arguments']) !!}">
                                                <input type="hidden" name="ajax_action" value="add{!! $v['query']['method'] !!}">
                                                <input type="hidden" name="ajax_callback_target" value="#custom_content_{{ $k }}">
                                            </div>
                                        @endif
                                        @break

                                        @case('radio')

                                        @foreach($v['options'] as $optionKey => $option)

                                            <input type="radio" name="multilang_custom_content[{!! $k !!}]" value="{!! $optionKey !!}" {!! $page_custom_data && (array_key_exists($k, $page_custom_data) && $page_custom_data[$k] == $optionKey) ? 'checked="checked"' : (in_array('default', $option) ? 'checked="checked"' : null) !!}/> {!! $option['label'] !!}
                                            <br>
                                        @endforeach
                                        @break

                                        @case('media')
                                        <div class="media_choice_holder">
                                            @foreach($v['types'] as $media_type=>$content)
                                                <div class="radio" style="display: inline-block;">
                                                    <label>
                                                        <input type="radio" name="multilang_custom_content[{{ $k }}]" class="ace {{ $k }}-selectable {{ $k }}_type_{{ $media_type }}"
                                                               data-{{ $k }}="{{ $media_type }}"

                                                               @if ($page_custom_data && array_key_exists($k, $page_custom_data) && ($page_custom_data[$k] == $media_type))
                                                               checked="checked"
                                                               @endif
                                                               data-type="{!! (Arr::has($content,'type') ? $content['type'] : $media_type)  !!}"

                                                            {!! Arr::has($content, 'acceptable') ? 'data-acceptable="'.$content['acceptable'].'"' : null!!}
                                                            {!! $media_type == 'image' ? 'data-config="'.(implode('.', AboleonPublisherHelpers::getkeypath($config['custom_content'], 'image')).'.custom_content.'.$data->type).'"' : null!!}
                                                        />
                                                        <span class="lbl"> {{ $content['label'] }}</span>
                                                    </label>

                                                </div>
                                            @endforeach
                                        </div>
                                        {{-- Media containers except fileupload --}}
                                        @foreach($v['types'] as $media_type=>$content)
                                            @if (!Arr::has($content,'type') or (Arr::has($content,'type') && ($content['type'] != 'fileupload')))
                                                <div class="{{ $k }}_type_{{ $media_type }} hidden form">
                                                    <input placeholder="{!! Arr::has($content, 'placeholder') ? $content['placeholder'] : null !!}" name="content" class="form-control col-sm-11"/><br>
                                                    <textarea style="margin: 20px 0 10px 0;" name="description" class="form-control" placeholder="Description"></textarea>
                                                    @if (Arr::has($content, 'info'))
                                                        <div class="alert alert-info" style="font-size: 13px">{!! $content['info'] !!}</div>
                                                    @endif
                                                    <button class="upload_media_type btn btn-sm btn-warning">
                                                        <span>{{ trans('aboleon.framework::ui.add') }}</span>
                                                    </button>
                                                    <div class="space-8" style="clear: both;"></div>
                                                </div>
                                            @endif
                                        @endforeach
                                        @break
                                    @endswitch

                                    @switch($k)
                                        @case('media')
                                        @php
                                            global $call_fileupload;
                                            $call_fileupload = true;
                                        @endphp
                                        <div id="fileupload">
                                            {{-- Dynamic file uploader --}}
                                            @include('aboleon.framework::lib.fileUpload')
                                            <div id="publisher_uploaded_images">
                                                @include('aboleon.publisher::pages.inc.media_dispatcher')
                                            </div>
                                        </div>
                                @break
                                @endswitch
                        </div>

                    @endforeach

                </div>
            </div>

        @endif

    @endforeach
@endif
