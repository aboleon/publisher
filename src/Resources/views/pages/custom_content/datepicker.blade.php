<!-- datepicker -->
<input type="text" name="custom_content[{!! $item_key !!}]"
       value="{{ $page_custom_data && array_key_exists($item_key, $page_custom_data) ?
                           $page_custom_data[$item_key] : null }}"
       class="form-control col-sm-11 datepicker"
       data-config="{!! (Arr::has($item_field, 'config') ? AboleonPublisherHelpers::implodeWithKeys($item_field['config']) :
                           null)
                           !!}"/>
