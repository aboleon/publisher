@php
    $resized_img = list($temp_width, $temp_height) = getimagesize(Project::upload_path().  $media_folder.'images/'  . $uploadedImage->content);
@endphp
<input type="hidden" class="resized_temp_w" value="{!! $temp_width !!}">
<input type="hidden" class="resized_temp_h" value="{!! $temp_height !!}">
<input type="hidden" class="x1" name="{!! $uploadedImage->varname !!}_x1image"/>
<input type="hidden" class="y1" name="{!! $uploadedImage->varname !!}_y1image"/>
<input type="hidden" class="w" name="{!! $uploadedImage->varname !!}_wimage"/>
<input type="hidden" class="h" name="{!! $uploadedImage->varname !!}_himage"/>
<input type="hidden" class="wi" name="{!! $uploadedImage->varname !!}_wiimage"
       value="{!! $image['size']['w'] !!}"/>
<input type="hidden" class="he" name="{!! $uploadedImage->varname !!}_heimage"
       value="{!! $image['size']['h'] !!}"/>
<div>
    <img alt="Image à recadrer"
         src="{!! Project::media($media_folder.'images/'  . $uploadedImage->content) !!}"
         class="jcrop_image"/>
</div>
<div class="form-group mt-20">
    <a href="{!! url('panel/Publisher/MediaManager/remove/' . $uploadedImage->id) !!}"
       class="btn btn-danger">
        <i class="white fas fa-trash-alt"></i> Supprimer la photo
    </a>
    <button class="btn btn-warning" name="{!! $image_key !!}_jcrop_confirm"
            type="submit"><i class="fas fa-ok bigger-110"></i> Valider le recadrage
    </button>
    <input type="hidden" name="media_id" value="{!! $uploadedImage->id !!}">
</div>