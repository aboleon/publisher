<div class="media_choice_holder">
    @foreach($item_field as $media_type=>$content)
    <div class="radio" style="display: inline-block;">
        <label>
            <input type="radio" name="custom_content[{{ $item_key }}]" class="{{ $item_key }}-selectable {{ $item_key }}_type_{{ $media_type }}" data-{{ $item_key }}="{{ $media_type }}"
            @if ($page_custom_data && array_key_exists($item_key, $page_custom_data) &&
            ($page_custom_data[$item_key] == $media_type))
            checked="checked"
            @endif
            data-type="{!! (Arr::has($content,'type') ? $content['type'] : $media_type)  !!}"
            {!! Arr::has($content, 'acceptable') ? 'data-acceptable="'.$content['acceptable'].'"' : null!!}
            @if($media_type == 'image')
            @php
            $img_config = AboleonPublisherHelpers::getkeypath($config->toArray(), 'media');
            array_push($img_config, $data->type);
            @endphp
            data-config="{!! implode('.', array_reverse($img_config)) !!}"
            @endif
            />
            <span class="lbl"> {{ $content['label'] ?? '' }}</span>
        </label>

    </div>
    @endforeach
</div>
{{-- Media containers except fileupload --}}
@foreach($item_field as $media_type=>$content)
    @if (!Arr::has($content,'type') or (Arr::has($content,'type') && ($content['type'] != 'fileupload')))

    <div class="{{ $item_key }}_type_{{ $media_type }} media-containers hidden form" data-url="panel/Publisher/ajax">
        @if (in_array('multi_lang',$content))
            @include('aboleon.publisher::pages.inc.media_lang_selector')
        @endif

        <input placeholder="{!! Arr::has($content, 'placeholder') ? $content['placeholder'] : null !!}"
        name="content" class="form-control"/><br>
        <textarea style="margin: 20px 0 10px 0;" name="description" class="form-control" placeholder="Description"></textarea>

        @if (Arr::has($content, 'info'))
            <div class="alert alert-info" style="font-size: 13px">{!! $content['info'] !!}</div>
        @endif
        <button class="upload_media_type btn btn-sm btn-warning">
            <span>{{ trans('aboleon.framework::ui.buttons.add') }}</span>
        </button>
        <div style="clear: both;"></div>
    </div>
    @endif
@endforeach