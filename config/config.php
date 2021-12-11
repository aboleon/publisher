<?php declare(strict_types = 1);

return [
    'route' => config('aboleon_framework.route').'/publisher/',
    'name' => 'Publisher',
    'filemanager_dir'=>env('FILEMANAGER_DIR', 'Publisher/media/upload/'),
    'text_color_map'=>env('TEXT_COLOR_MAP', false)
];
