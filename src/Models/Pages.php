<?php

declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Traits\Responses;
use Cache, DB, File, Image, Media;
use Helpers;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Project;
use Aboleon\Framework\Models\{
    Geo
};
use Illuminate\Support\{
    Arr,
    Facades\Artisan,
    Str
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pages extends Model
{
    use SoftDeletes;

    use Responses;

    protected $table = 'publisher';
    protected $forceDelete = false;
    protected $cachable = ['contact', 'general'];
    protected $typeConfig;
    protected $position;
    protected $guarded = [];
    protected $randomName;
    protected $crop_ratio = 3;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->randomName = md5(time() . rand());
        $this->typeConfig = collect([]);
    }


    public function enableForceDelete(): static
    {
        $this->forceDelete = true;
        return $this;
    }

    public function add()
    {
        if (request()->isMethod('post')) {
            (new PagesCreateContent())->setupPage();
        }

    }

    public static function checkDuplicateUrl($url, $lang, ?int $id): bool
    {
        $query = PagesData::where([
            'lg' => $lang,
            'url' => $url
        ]);
        if ($id) {
            $query->where('pages_id', '!=', $id);
        }
        if ($query->count() > 0) {
            return true;
        }

        return false;
    }

    /** Get existing pages for listable types
     * @param array $config
     * @param int $owner
     * @return array
     */
    public static function listablePages(array $config, int $owner)
    {
        $listable_pages = array_filter($config, function ($item) {
            return strstr($item, '_list');
        }, ARRAY_FILTER_USE_KEY);

        if ($listable_pages) {
            $pages = Pages::query()->whereIn('type', array_keys($listable_pages))->whereParent($owner)->get(['type', 'title', 'id', 'published'])->keyBy('type')->toArray();
            $missing = array_diff_key($listable_pages, $pages);
            if ($missing) {
                foreach ($missing as $key => $value) {
                    $page = new Pages;
                    $page->type = $key;
                    $page->title = $value['label'];
                    $page->parent = $owner;
                    $page->position = Pages::max('position') + 1;
                    $page->save();
                    $pages[$key] = [
                        'type' => $page->type,
                        'published' => $page->published,
                        'title' => $page->title,
                        'id' => $page->id
                    ];
                }
            }
            return $pages;
        }
        return [];
    }

    /** Get existing pages for single listable types
     * @param array $config
     * @param int $owner
     * @return array
     */
    public static function listableSinglePages(array $config, int $owner)
    {
        $single_pages = array_filter($config, function ($item, $key) {
            return !strstr($key, '_list') && in_array('is_single', $item);
        }, ARRAY_FILTER_USE_BOTH);

        if ($single_pages) {
            $pages = Pages::query()->whereIn('type', array_keys($single_pages))->whereParent($owner)->get(['type', 'title', 'id', 'published'])->keyBy('type')->toArray();
            $missing = array_diff_key($single_pages, $pages);
            if ($missing) {
                foreach ($missing as $key => $value) {
                    $page = new Pages;
                    $page->type = $key;
                    $page->title = $value['label'];
                    $page->parent = $owner;
                    $page->save();
                    $pages[$key] = [
                        'type' => $page->type,
                        'published' => $page->published,
                        'title' => $page->title,
                        'id' => $page->id
                    ];
                }
            }
            return $pages;
        }
        return [];
    }

    public function duplicatedUrls(string $lg, ?string $url)
    {
        return PagesData::query()
            ->select('a.title', 'pages_id', 'a.type')
            ->where([
                'lg' => $lg,
                'url' => $url
            ])
            ->where('pages_id', '!=', $this->id)
            ->join('publisher_pages as a', function ($join) {
                $join->on('a.id', '=', 'pages_id');
            })
            ->get();
    }


    static function findPageByUrl(string $url, $type = null)
    {
        return self::type($type)->select('publisher_pages.*')->join('publisher_pages_data as a', function ($join) use ($url) {
            $join->on('a.pages_id', '=', 'publisher_pages.id')->where([
                'a.url' => $url,
                'a.lg' => app()->getLocale()
            ]);
        })->first();
    }

    public function listOfType(): object
    {
        $type = $this->object_id;

        if (is_null($type)) {
            return view('core::panel.error')->with('error', "Le type de contenu est inconnu.");
        }

        return view()->first([
            'listings.' . $type,
            'aboleon.publisher::pages.listOfType'
        ])->with([
            'config' => collect(config('project.content.' . $type)) ?? collect(),
            'type' => $type,
            'pages' => Pages::whereType($type)->with(['children', 'meta'])->orderBy('position')->orderBy('title')->paginate(15)
        ]);
    }

    public function list()
    {

        $data = self::find($this->object_id); // pattern publisher/pages/list/{id}

        // pattern publisher/pages/list/{string type}
        if (is_null($data)) {
            $data = self::whereType($this->object_id)->first();
        }

        if (is_null($data)) {
            return view('aboleon.publisher::errors.404')->with('message', trans('errors.404'));
        }

        $config = collect(array_merge_recursive((array)config('project.content.' . $data->type), (array)config('project.content.' . $data->type . '_' . $data->taxonomy)));
        if (!$config->has('has')) {
            return view('aboleon.publisher::errors.404')->with('message', trans('errors.has_no_list'));
        }

        $type = false;
        $lists = $config->has('has') ? count($config['has']) : false;

        $type = $lists
            ? ($lists > 1) && count(request()->segments()) > 5
                ? array_key_exists(request()->segment(6), $config['has'])
                    ? request()->segment(6)
                    : key($config['has'])
                : key($config['has'])
            : false;

        //de(self::where('parent', $data->id)->type($type)->archives()->get());

        if (!$type) {
            return view('aboleon.publisher::errors.404')->with('message', trans('errors.has_no_list'));
        }

        $this->typeConfig = collect(config('project.content.' . $type));

        if (request()->isMethod('post')) {
            $content_id = (new PagesCreateContent())->createContentFromListAction();
            return redirect()->route('publisher.edit', ['id' => $content_id]);
        }

        $pages = self::where('parent', $data->id)->type($type)->with(['meta'])->archives()->orderBy('position')->paginate(15);

        return view()->first([
            'listings.' . $type,
            'aboleon.publisher::pages.list'
        ])->with([
            'data' => $data,
            'pages' => $pages,
            'items_count' => $pages->count(),
            'config' => $config,
            'response' => $this->response,
            'listConfig' => $config['has'][$type],
            'archives' => request()->has('archives'),
            'list_type' => $type,
            'typeConfig' => $this->typeConfig
        ]);
    }

    public function restoreContent()
    {
        $this->editable = self::withTrashed()->find($this->object_id);
        $this->editable->restore();
        $this->restoreChildren($this->editable->id);
        return $this->pageEdit();
    }

    private function restoreChildren($id)
    {
        $children = self::withTrashed()->where('parent', $id);

        if ($children->count()) {
            $children->restore();
            foreach ($children->cursor() as $item) {
                $this->restoreChildren($item->id);
            }
        }
    }

    public function customContent()
    {
        return $this->hasMany(CustomContent::class, 'pages_id');
    }

    /* Get all media binded to Page */
    public function mediaContent()
    {
        return $this->hasMany(MediaManager::class, 'pages_id')->with('description')->orderBy('position')->orderBy('updated_at', 'desc');
    }

    /* Get first available image */
    public function image($varname = null)
    {
        if (is_null($varname)) {
            return $this->mediaContent->first();
        }
        return $this->mediaContent->where('varname', $varname)->first();
    }

    public function illustration()
    {
        return $this->hasOne(MediaManager::class, 'pages_id');
    }

    public function gallery()
    {
        return $this->mediaContent->where('type', 'image')->where('varname', 'fileupload')->sortBy('position');
    }

    public function hasParent()
    {
        return $this->belongsTo(self::class, 'parent')->withTrashed();
    }

    public function nested()
    {
        return $this->hasMany(self::class, 'parent')->with('nested');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent')->archives()->orderBy('position');
    }

    public function childrenOfType(string $type)
    {
        return $this->children->where('type', $type)->sortBy('position');
    }

    public function childrenPublished()
    {
        return $this->hasMany(self::class, 'parent')->published()->orderBy('position');
    }

    public function childrenPublishedOfType(string $type)
    {
        return $this->childrenPublished->where('type', $type);
    }

    public function meta()
    {
        return $this->hasOne(PagesData::class, 'pages_id')->select('pages_id', 'title', 'nav_title', 'url')->where('lg', $this->locale);
    }

    public function extendedMeta()
    {
        return $this->hasOne(PagesData::class, 'pages_id')->select('pages_id', 'title', 'nav_title', 'url', 'intro')->where('lg', $this->locale);
    }

    public function globalMeta()
    {
        return $this->hasMany(PagesData::class, 'pages_id')->select('pages_id', 'lg', 'title', 'nav_title', 'url');
    }

    public function content()
    {
        return $this->hasOne(PagesData::class, 'pages_id')->where('lg', $this->locale);
    }

    public function translations()
    {
        return $this->hasMany(PagesData::class, 'pages_id');
    }


    public function removeArchives()
    {
        $this->forceDelete = true;
        return $this->remove();
    }

    public function removeChildren(): static
    {
        if ($this->forceDelete) {
            $children = self::withTrashed()->where('parent', $id);
        } else {
            $children = self::where('parent', $id);
        }

        if ($children->count() > 1) {
            foreach ($children->cursor() as $item) {
                if ($this->forceDelete) {
                    Media::removeAttachedMedia($item);
                }
                $item->{$this->forceDelete ? 'forceDelete' : 'delete'}();
                $item->removeChildren();
                $this->pushMessages($item);
            }
        }
        return $this;
    }

    public function pageStatus()
    {
        self::whereId(request()->object_id)->update(['published' => (request()->published == 'true' ? 1 : null)]);
        $this->responseNotice(trans('core::ui.statusChange', ['status' => trans('core::ui.' . (request()->published == 'true' ? 'online' : 'offline'))]));
        Artisan::call('cache:clear');
        return $this->response;
    }

    public function scopeArchives($query)
    {
        if (request()->has('archives')) {
            return $query->onlyTrashed();
        }
    }

    public function scopeType($query, $type)
    {
        if ($type) {
            if (is_string($type)) {
                $query->whereType($type);
            }
            if (is_array($type)) {
                $query->whereIn('type', $type);
            }
            return $query;
        }
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published');
    }

    public static function categories(array $arguments)
    {
        return self::where($arguments)->orderBy('title')->get();
    }

    protected function addattached_subcontent()
    {
        return $this->addcategories();
    }

    public static function getLastUpdate()
    {
        $media_timestamp = DB::table('publisher_media_content')
            ->select('updated_at');

        return DB::table('publisher_pages')
            ->select('updated_at')
            ->union($media_timestamp)
            ->orderBy('updated_at', 'desc')
            ->limit(1)->value('updated_at');
    }

    public function geo()
    {
        return $this->hasOne(Geo::class);
    }

    public static function get_custom_value($data, $field)
    {
        return optional(optional($data->customContent)->where('field', $field)->first())->content;
    }

    /**
     * @param string $field
     * @param string $as values: array|string default: as it comes
     * @return array|mixed|null
     */
    public function fetchCustomValue(string $field, string $as = '')
    {
        if (is_null($this->customContent)) {
            return null;
        }
        $data = optional($this->customContent->where('field', $field));
        if ($data->count() > 1) {
            if ($as == 'string') {
                return optional($data->first())->value;
            }
            return $data->pluck('value')->toArray();
        }
        $value = optional($data->first())->value;
        if ($as == 'array') {
            return $value ? [$value] : [];
        }
        return $value;
    }

    public function fetchMultiLangCustomValue(string $field)
    {
        if (is_null($this->customContent)) {
            return null;
        }
        return $this->customContent->where('field', $field)->first()->content->content ?? '';
    }

    public function printImage(?string $varname = null, array $params = [])
    {
        $image = !is_null($varname) ? $this->mediaContent->where('varname', $varname)->first() : $this->image();

        if (!is_null($image)) {
            return '<img src="' . Project::media((config('project.config.store_media_by_key') ? $this->access_key . '/' : null) . 'images/' . $image->content) . '"
            alt="' . ($params['alt'] ?? ($image->description?->where('lg', $this->locale())->first()?->description ?? config('app.name'))) . '"'
                . (array_key_exists('class', $params) ? ' class="' . $params['class'] . '"' : '') . '>';
        }
    }

    public function imageUrl(?string $varname = null, array $params = [])
    {
        $image = !is_null($varname) ? $this->mediaContent->where('varname', $varname)->first() : $this->image();

        if (!is_null($image)) {
            return Project::media((config('project.config.store_media_by_key') ?
                    $this->access_key . '/' : null) . 'images/' . $image->content);
        }
    }

    public static function get_custom_content($data, $field): object
    {
        return optional($data->customContent)->where('field', $field);
    }
}
