<?php
global $select_values;
$custom_content_key = $item_key;

if(array_key_exists('query', $item_field)) {
    if(array_key_exists('values', $item_field['query']) && array_key_exists($item_field['query']['values'], $select_values)) {
        $select_values[$custom_content_key] = $select_values[$item_field['query']['values']];
    } else {
        $select_values[$custom_content_key] = \Aboleon\Publisher\Callables\CustomContentCallable::{$item_field['query']['method']}($item_field['query']['arguments']);
    }
}
$query_with_children = array_key_exists('with_children', $item_field['query']);
