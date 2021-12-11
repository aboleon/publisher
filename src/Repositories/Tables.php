<?php

namespace Aboleon\Publisher\Repositories;

class Tables {

    private static array $tables = [
        'publisher' => 'publisher',
        'meta' => 'publisher_meta',
        'configs' => 'publisher_configs',
        'nodes' => 'publisher_nodes',
        'nodes_media' => 'publisher_media',
        'nodes_media_content' => 'publisher_media_content',
        'content' => 'publisher_content',
        'content_translated' => 'publisher_content_translated',
        'lists' => 'publisher_lists',
        'lists_translated' => 'publisher_lists_translated'
    ];

    public static function fetch(string $table)
    {
        return self::$tables[$table];
    }

}