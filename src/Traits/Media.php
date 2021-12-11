<?php

declare(strict_types = 1);

namespace Aboleon\Publisher\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Pages, Project;

trait Media
{

    protected $uploadPath;
    protected $uploaded_file;
    protected $uploaded_type;
    protected $uploaded_file_config;
    protected $uploaded_filename;
    protected $accessKey = null;
    protected $to_page_id;

    public function __construct()
    {
        $this->uploadPath = Project::upload_path();
    }

    protected function checkMakeDir(string $directory)
    {
        if (!is_dir($this->uploadPath.$directory)) {
            mkdir($this->uploadPath.$directory, (int)0755, true);
        }
    }

    public function accessKey(\Aboleon\Publisher\Models\Pages $page)
    {
        if (config('project.config.store_media_by_key')) {
            if (is_null($page->access_key)) {
                $page->access_key = Str::random(8);
                $page->save();
            }
             $this->accessKey = $page->access_key;
        }
    }

    public function withAccessKey($separator=null)
    {
        if (!is_null($this->accessKey)) {
            if (is_null($separator)) {
                return $this->accessKey . '/';
            }
            return '/'.$this->accessKey;
        }
    }

}
