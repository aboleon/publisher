@if ($data)
    @foreach($data as $sec)
        @php
            $s = 'config[elements]['. $sec['id'] . ']';
        @endphp
        <section id="section_{{ $sec['id'] }}" data-id="{{ $sec['id'] }}" class="dropped">
            <i class="fa fa-plus-circle"></i>
            <div class="section_title dropped">
                <label class="form-label">Section</label>

                <input type="text" name="{{ $s }}[title]" class="form-control mt-2" value="{{ $sec['title'] }}"/>
                <input type="hidden" name="{{ $s }}[params][belongs]" value="{{ $sec['params']['belongs'] }}"/>

                <x-aboleon.publisher-organizer-node-params :type="false" :name="$s" :node="$sec"/>
                <x-aboleon.publisher-organizer-replicable :name="$s" :node="$sec"/>
                <x-aboleon.publisher-organizer-node-fields :name="$s" :node="$sec"/>
            </div>
            <div class="droppables">
                @if (array_key_exists('children', $sec))
                    @foreach($sec['children'] as $el)
                        @php
                            $type = (new \Aboleon\Publisher\Models\ConfigsElements)->element($el['type']);
                            $e = $s.'[elements]['. $el['id']. ']';
                        @endphp
                        <div id="node_{{ $el['id'] }}" data-id="{{ $el['id'] }}" data-belongs="{{ $sec['id'] }}" class="dropped">
                            <i class="fa fa-plus-circle"></i>
                            <label class="form-label"><span class="badge">{{ $type['label'] ?? ''}}</span></label>
                            <input name="{{$e}}[title]" class="form-control" value="{{ $el['title'] }}"/>

                            <x-aboleon.publisher-organizer-node-params :type="$type" :name="$e" :node="$el"/>
                            <x-aboleon.publisher-organizer-replicable :name="$e" :node="$el"/>

                            @if (in_array($el['type'], ['image','gallery']))
                                <div class="d-flex mt-2 params">
                                    <input name="{{$e}}[params][dim]" class="form-control" value="{{ $el['params']['dim'] }}" placeholder="Dimensions des images. Ex: 1920,1080;680,320"/>
                                </div>
                            @endif

                            @if ($el['type'] == 'list')
                                <div class="d-flex mt-2 params">
                                    <select class="form-control me-2" name="{{$e}}[params][list_id]">
                                        <option>-- Liste --</option>
                                        @forelse($listables as $list_id => $list_title)
                                            <option value="{{ $list_id }}"{{$el['params']['list_id'] == $list_id ? ' selected' :''}}>{{ $list_title }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                    <span class="btn btn-info btn-sm pt-2 create-list">Créer</span>
                                </div>
                            @endif

                            @if ($el['type'] == 'form')
                                <div class="d-flex mt-2 params">
                                    <select class="form-control me-2" name="{{$e}}[params][form_id]">
                                        <option>-- Formulaire --</option>
                                        @forelse($formables as $form)
                                            <option value="{{ $form['name'] }}"{{$el['params']['form_id'] == $form['name'] ? ' selected' :''}}>
                                                {{ __('forms.labels.'.$form['name']) }}
                                            </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    <span class="btn btn-info btn-sm pt-2 create-list">Créer</span>
                                </div>
                            @endif
                            <x-aboleon.publisher-organizer-node-fields :name="$e" :node="$el"/>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>
    @endforeach
@endif

<div id="dev_env"></div>