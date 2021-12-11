<!-- nested_categories -->
<?php
include('query/prepare.php');
$currentCategories = $data->customContent->where('field', $custom_content_key)->pluck('value')->toArray();
$field_name = 'custom_content['.$item_key.'][]';
?>
@if($select_values[$custom_content_key]->isNotEmpty())
<div class="nested_categories">
<ul>
@foreach($select_values[$custom_content_key] as $value)
<?php
$count = $value->nested->count(); ?>
<li>
    <input type="checkbox" name='{!! $field_name !!}' value="{!! $value->id !!}" {!! (in_array($value->id, $currentCategories) ? "checked='checked'" : null) !!}/>
    <span{!! ($count ? ' class="has"':'') !!}>{!! $value->title . ($count ? ' ('.$count.')' : '') !!}</span>
    {!! AboleonPublisherHelpers::printNestedTree($value, $currentCategories, $field_name)!!}
</li>
@endforeach
</ul>
</div>
@endif
