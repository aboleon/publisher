@php
    global $call_fileupload;
    $call_fileupload = true;
@endphp
<form method="post" action="{!! $_SERVER['REQUEST_URI'] !!}" id="fileupload" enctype="multipart/form-data">
    <div class="media_choice_holder">
        @foreach($v['types'] as $media_type=>$content)
            <div class="radio" style="display: inline-block;">
                <label>
                    <input type="radio" name="custom_content[{{ $k }}]" class="ace {{ $k }}-selectable {{ $k }}_type_{{ $media_type }}"
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

    @include('aboleon.framework::lib.fileUpload')
    <div id="publisher_uploaded_images">
        @include('aboleon.publisher::pages.inc.media_dispatcher')
    </div>
