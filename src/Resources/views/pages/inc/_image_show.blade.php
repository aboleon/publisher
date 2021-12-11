@php

    $img_to_show = $img_zoom = $media_folder . 'images/' . $uploadedImage->content;

    if (array_key_exists('sizes', $image)) {
        $images_values = collect($image['sizes'])->sortBy('w');
        $img_zoom = str_replace('.jpg', '-'.$images_values->last()['label'].'.jpg', $img_to_show);
        $img_to_show = str_replace('.jpg', '-'.$images_values->first()['label'].'.jpg', $img_to_show);

    }

@endphp
@if (is_file(Project::upload_path($img_to_show)))
    @php
        list($img_to_show_width, $img_to_show_height) = getimagesize(Project::upload_path($img_to_show))
    @endphp
    <div class="media-gallery">
        <div class="img-holder" {!! array_key_exists('background', $image) ? 'style="background-color:'.$image['background']
    .'"' : null !!}>
            <img alt="" src="{!! Project::media($img_to_show) !!}"
                 class="img-fluid" {!! $img_to_show_height > 400 ? "style='max-height:230px;'" : null !!} />
            <div class="text">
                <a href="{!! Project::media($img_to_show) !!}" class="zoom" data-index="0"
                   data-src="{!! Project::media($img_zoom) !!}">
                    <i class="white fas fa-search-plus"></i>
                </a>
                <a href="{!! url('panel/Publisher/MediaManager/remove/' . $uploadedImage->id) !!}">
                    <i class="white fas fa-trash-alt"></i>
                </a>
            </div>
        </div>
    </div>
    @foreach(Project::locales() as $locale)
        <div>
            <p class="mb-0 mt-2">
                <img src="{!! asset('vendor/flags/4x3/'.$locale.'.svg') !!}" alt="{{ trans('core::lang.'.$locale.'.label') }}" width="20" style="margin:-3px 5px 0 0;"/> Légende
            </p>
            <input class="form-control" type="text" name="media_description[{{ $image_key }}][{{ $locale }}]" value="{{ $uploadedImage->descriptions->where('lg', $locale)->first()->description ?? '' }}">
        </div>
    @endforeach
@else

    <a class="btn btn-danger"
       href="{!! url('panel/Publisher/MediaManager/remove/' . $uploadedImage->id) !!}">
        <i class="white fas fa-trash-alt"></i> Effacer la référence</a>
@endif
@push('js')
    <script>

    </script>
@endpush
