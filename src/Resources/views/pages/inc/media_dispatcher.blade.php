<?php $media_folder = Media::getAccessKeyWithSeparator($data);?>
@foreach($item_field as $media_type=>$option_content)
<?php $media_items = $data->mediaContent->where('type', $media_type)->where('varname', 'fileupload');?>

<div class="{{ $media_type }}-bloc media-gallery form" id="sortable_{{ $media_type }}" data-url="panel/Publisher/ajax">
    <h5>
        <span class="counter">{{ $media_items->count() }}</span> {{ trans_choice('aboleon.framework::ui.'.$item_key.'.'.$media_type,2) }}
    </h5>
    @if (!$data->mediaContent->isEmpty())
    @switch($media_type)
    @case('image')
    @foreach($media_items as $key => $media_item)
    <div class="img-holder media_item_{!! $media_item->id !!}" data-id="{!! $media_item->id !!}">
        <img src="{!! Project::media($media_folder.'images/th_' . $media_item->content) !!}"/>
        <div class="text">
            <a class="zoom" data-index="{{ $key }}" data-src="{!! Project::media($media_folder.'images/' .
            $media_item->content)
            !!}">
                <i class="white fas fa-search-plus"></i>
            </a>
            <a href="#" class="delete_media" data-id="{!! $media_item->id !!}" data-media="image">
                <i class="white fas fa-trash-alt"></i>
            </a>
        </div>
    </div>
    @endforeach
    @break
    @case('document')
    @foreach($media_items as $media_item)
    <div class="line media_item_{!! $media_item->id !!}" data-id="{!! $media_item->id !!}" data-lg="{{ $media_item->description->lg }}">
        <a target="_blank" href="{!! Project::media($media_folder.'documents/' . $media_item->content) !!}">
            {!! $media_item->description->description =='' ? $media_item->content : $media_item->description->description !!}
        </a>
        <a href="#" class="btn btn-warning btn-sm media_edit">
            <i class="fas fa-pen"></i>
        </a>
        <a href="#" class="btn btn-danger btn-sm delete_media" data-media="document" data-id="{!! $media_item->id !!}">
            <i class="fas fa-trash-alt"></i>
        </a>
    </div>
    @endforeach
    @break
    @case('video')
    @foreach($media_items as $media_item)
    <div class="line media_item_{!! $media_item->id !!}"
         data-id="{!! $media_item->id !!}"
         data-lg="{{ $media_item->description->isNotEmpty() ? $media_item->description->lg : app()->getLocale() }}">
        <code>
            <a target="_blank" href="{!! $media_item->content !!}">
                {!! $media_item->description->isNotEmpty() ?$media_item->description->description : $media_item->content !!}
            </a>
        </code>
        <a href="#" class="btn btn-warning btn-sm media_edit">
            <i class="fas fa-pen"></i>
        </a>
        <a href="#" class="btn btn-danger btn-sm delete_media" data-media="document" data-id="{!! $media_item->id !!}">
            <i class="fas fa-trash-alt"></i>
        </a>
    </div>
    @endforeach
    @break
    @endswitch
    @endif
</div>
@endforeach
@push('js')

    {{-- @include('aboleon.framework::lib.light-gallery') --}}

<script>
    $(function () {

        //pushLightGallery();
        media_edit();
        $("#sortable_image").sortable({
            update: function () {
                var data = [];
                $("#sortable_image .img-holder").each(function (index) {
                    data.push({
                        key: $(this).attr('data-id'),
                        position: index
                    });
                });
                console.log(data);
                ajax('ajax_object=MediaManager&ajax_action=sortable&positions=' + JSON.stringify(data), $("#sortable_image"));
            }
        });
        $("#sortable_image").disableSelection();
    });
</script>
@endpush
