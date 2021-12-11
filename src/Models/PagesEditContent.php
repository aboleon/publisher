<?php

declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Illuminate\Support\{
    Arr,
    Facades\Artisan,
    Str
};
use Illuminate\Database\Eloquent\Model;
use Aboleon\Framework\Traits\{
    Locale,
    Responses
};

class PagesEditContent extends Model
{
    use Locale;
    use Responses;

    protected $casts = [
        'type' => 'string'
    ];
    private $config;
    private $editable;
    private $input;

    public function __construct(Pages $page)
    {
        $this->editable = $page;
        $this->input = request()->input();
    }

    public function process(): void
    {
        if (!isset($this->editable->id)) {
            return;
        }
        $this->processMainObject();
        $this->processContentObject();
        (new CustomContent())->processCustomContent($this->editable);
        $this->processClassManaged();
        $this->processMedia();
        $this->resetCache();
    }

    private function processClassManaged()
    {
        if (request()->has('class_managed_post')) {
            foreach(request('class_managed_post') as $value) {
                $class_to_call = '\App\\Models\ClassManagedCustomContent\\' . $value;
                (new $class_to_call())->post();
            }
        }
    }

    private function processContentObject()
    {
        # Page data content (by language)
        # ---------------------


        foreach ($this->locales() as $lang) {
            $this->processMeta($lang);
            $this->input[$lang]['pages_id'] = $this->editable->id;
            $this->input[$lang]['lg'] = $lang;
            PagesData::updateOrCreate(['pages_id' => $this->editable->id, 'lg' => $lang], $this->input[$lang]);
        }
    }

    private function processMainObject(): void
    {
        $this->editable->title = Arr::has($this->input, config('app.fallback_locale') . '.title') ? $this->input[config('app.fallback_locale')]['title'] : '';
        $this->editable->type = request()->type;
        $this->editable->parent = request()->parent;
        $this->editable->access_key = request()->access_key;
        $this->editable->taxonomy = Str::slug(request()->taxonomy, '_');
        $this->editable->save();
    }

    private function processMedia()
    {
        $config = (array)config('project.content.' . $this->editable->type);

        if (Arr::has($config, 'images')) {
            foreach (array_keys($config['images']) as $image_key) {
                $this->uploadMedia($image_key);
                if (request()->has('media_description.' . $image_key)) {
                    $media = $this->editable->mediaContent->where('varname', $image_key)->first();
                    MediaDescription::setMediaDescription($media, request()->media_description[$image_key]);
                }
            }
            session()->flash('processed_images', true);
        }
    }

    private function processMeta(string $lang): void
    {
        $meta_is_enabled = array_key_exists('url', $this->input[app()->getLocale()]);
        if ($meta_is_enabled) {
            # URL control
            $url = Str::slug($this->input[$lang]['url'] != '' ? $this->input[$lang]['url'] : ($this->input[$lang]['title'] ?? ''));

            if (Str::contains($url, 'sans-titre')) {
                $url = Str::slug($this->input[$lang]['title']);
                $this->input[$lang]['nav_title'] = $this->input[$lang]['title'];
            }

            $this->input[$lang]['url'] = $url;

            if (Pages::checkDuplicateUrl($url, $lang, $this->editable->id)) {
                $this->input[$lang]['url'] = $this->editable->id . '-' . $url;
            }

            # Meta control
            if (is_null($this->input[$lang]['meta_title'])) {
                $this->input[$lang]['meta_title'] = $this->input[$lang]['title'] ?? '';
            }

            if (is_null($this->input[$lang]['nav_title'])) {
                $this->input[$lang]['nav_title'] = $this->input[$lang]['title'] ?? '';
            }
        }
    }

    private function uploadMedia(string $filename): void
    {
        if (request()->hasFile($filename)) {
            $media = new MediaUploader();
            $media->newUploadedFile($filename);
            $media->staticUpload($this->editable);
        }
        if (request()->has($filename . '_jcrop_confirm')) {
            (new MediaUploader())->jcrop();
        }

    }

    private function resetCache()
    {
        Artisan::call('cache:clear', []);
    }
}
