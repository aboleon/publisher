@if ($config->has('images') or $config->has('media'))
    @php
        $is_not_cropped = false;
            $media_folder = Media::getAccessKeyWithSeparator($data);
            $jcrop_array = [];
    @endphp
    <h1 class="hyper collapsible {!! session()->has('processed_images') ? 'open':null !!}" data-target="admin_images">
        <img src="{{ asset('aboleon/publisher/icons/double-right-arrows-angles.svg') }}" height="50" alt="">
        Medias
    </h1>
    <div id="admin_images"
         class="row admin_images"{!! session()->has('processed_images') ? ' style="display:block"' : '' !!}>
        @if($config->has('images'))
            @foreach($config['images'] as $image_key => $image)
                @php
                    $uploadedImage = null;
                    $jcrop = in_array('jcrop',$image);
                    if ($jcrop) {
                        $jcrop_array[] = 1;
                    }
                    $uploadedImage = $data->mediaContent->filter(function($item) use ($image_key) {
                        return $item->varname == $image_key;
                    })->first();
                    $is_not_cropped = $jcrop && strstr($uploadedImage, 'jcrop_') && is_file(Project::upload_path(). $media_folder.'images/'  . $uploadedImage->content);
                @endphp
                <div class="col-sm-{{ $is_not_cropped ? 12 : ($config->has('media') ? 3 : 6) }}">
                    <div class="bloc-editable">
                        <div class="form-group jcroppable">
                            <h2>
                                <i class="fas fa-picture-o smaller-90"></i>
                                <i class="fas fa-zoom-in"></i> {!! array_key_exists('label', $image) ? $image['label'] : 'Photo principale' !!}
                            </h2>
                            @if (is_null($uploadedImage))
                                @include('aboleon.publisher::pages.inc._image_not_uploaded')
                            @else
                                @if($is_not_cropped)
                                    @include('aboleon.publisher::pages.inc._image_to_crop')
                                @else
                                    @include('aboleon.publisher::pages.inc._image_show')
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            @if (count($jcrop_array) > 0)
                @push('css')
                    <link rel="stylesheet" href="{!! asset('vendor/jcrop/jquery.Jcrop.css') !!}" type="text/css"/>
                @endpush
                @push('js')
                    <script src="{!! asset('vendor/jcrop/jquery.Jcrop.js') !!}"></script>
                    <script src="{!! asset('aboleon/publisher/js/jcrop.js') !!}"></script>
                @endpush
            @endif

        @endif
        @if ($config->has('media'))
            <div class="col-sm-{{ $is_not_cropped ? 12 : 9 }} custom-content form">
                <div class="bloc-editable">
                    @php
                        $medias = $config['media'];
                        $has_label = array_key_exists('label', $medias);
                        $medias_col = array_key_exists('fields', $medias) ? 12/count($medias['fields']) : 12;
                        $is_grid = (array_key_exists('grid', $medias) && $sc['grid'] == 'custom');
                        $page_custom_data = $data->customContent->pluck('value', 'field')->toArray();
                        $item_key = 'media';
                        $item_field = $medias['fields'];
                    @endphp
                    @if ($has_label)
                        <h2>
                            {{ translatable($medias['label']) }}
                        </h2>
                    @endif
                    @include('aboleon.publisher::pages.inc.__custom_content_media')
                    @include('aboleon.publisher::pages.inc.__custom_content_media_upload')
                </div>
            </div>
        @endif
    </div>

    @push('js')
        <script>
            $(function () {
                $('.collapsible').click(function () {
                    $('#' + $(this).data('target')).slideToggle();
                    $(this).toggleClass('open');
                });
            });
        </script>
    @endpush
@endif
