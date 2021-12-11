<!-- Google Places -->
<div class="clearfix gmapsbar">
    <div class="locationField">
        <input type="text" name="custom_content[{!! $item_key !!}]"
               value="{{ $page_custom_data && array_key_exists($item_key, $page_custom_data) ?
                           $page_custom_data[$item_key] : null }}"
               class="g_autocomplete form-control" placeholder="">
        <input type="hidden" name="custom_content[{!! $item_key !!}_lat]"
               class="wa_geo_lat"
               value="{{ $page_custom_data && array_key_exists($item_key.'_lat', $page_custom_data) ?
                           $page_custom_data[$item_key.'_lat'] : null }}"/>
        <input type="hidden"
               name="custom_content[{!! $item_key !!}_lon]"
               class="wa_geo_lon"
               value="{{ $page_custom_data && array_key_exists($item_key.'_lon', $page_custom_data) ?
                           $page_custom_data[$item_key.'_lon'] : null }}"/>
        <input type="hidden"
               name="custom_content[{!! $item_key !!}_locality]"
               class="locality"
               value="{{ $page_custom_data && array_key_exists($item_key.'_country', $page_custom_data) ?
                           $page_custom_data[$item_key.'_locality'] : null }}"/>
        <input type="hidden"
               class="field country"
               name="custom_content[{!! $item_key !!}_country]"
               value="{{ $page_custom_data && array_key_exists($item_key.'_country', $page_custom_data) ?
                           $page_custom_data[$item_key.'_country'] : null }}"/>
        <input type="hidden"
               class="field country_code"
               name="custom_content[{!! $item_key !!}_country_code]"
               value="{{ $page_custom_data && array_key_exists($item_key.'_country_code', $page_custom_data) ?
                           $page_custom_data[$item_key.'_country_code'] : null }}"/>
        <br>
    </div>
</div>
