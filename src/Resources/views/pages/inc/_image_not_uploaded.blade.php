<p>
    @php
    $img_size = !array_key_exists('sizes', $image) ? $image['size'] : $image['sizes'][0];
    @endphp


        @if (!is_null($img_size['w']))
            <i>Largeur {{ $jcrop ? 'min.' : 'recommandée' }} : {{ $img_size['w'] }}px</i>
        @endif

        @if (!is_null($img_size['h']))
            <i>Hauteur {{ $jcrop ? 'min.' : 'recommandée' }} : {{ $img_size['h'] }}px</i>
        @endif
</p>
<input data-jcroppable="{!! $jcrop !!}" data-w="{!! $img_size['w'] !!}"
       data-h="{!! $img_size['h'] !!}" class="input-file" type="file"
       name="{!! $image_key !!}"/>
@if($jcrop)
    <p>Cliquez sur Enregistrer puis validez le recadrage</p>
@endif
<div class="errors"></div>
<input type="hidden" class="jcrop" name="{!! $image_key !!}_jcrop" value="{!! $jcrop !!}"/>
<input type="hidden" name="{!! $image_key !!}_upload_errors" class="upload_errors"
       value=""/>
