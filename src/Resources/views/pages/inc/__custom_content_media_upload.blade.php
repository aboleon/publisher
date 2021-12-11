@php
    global $call_fileupload;
    $call_fileupload = true;
@endphp
<div id="fileupload">
    {{-- Dynamic file uploader --}}
    @foreach($item_field as $media_type=>$content)
        @if (in_array('multi_lang',$content) && (array_key_exists('type', $content) && $content['type'] == 'fileupload'))
            <div class="upload_params params_{{ $item_key }}_type_{{ $media_type }} hidden">
                @include('aboleon.publisher::pages.inc.media_lang_selector')
            </div>
        @endif
    @endforeach
    @include('aboleon.framework::lib.fileUpload')
    <div id="publisher_uploaded_images">
        @include('aboleon.publisher::pages.inc.media_dispatcher')
    </div>
</div>