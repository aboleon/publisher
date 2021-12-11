<?php

declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Aboleon\Framework\Traits\Locale;
use Aboleon\Framework\Traits\Responses;


class PagesCreateContent extends Model
{
    use Locale;
    use Responses;

    protected $casts = [
        'type' => 'string'
    ];
    protected $editable;

    public function addAjaxPage()
    {
        if (request()->filled('ajax_title')) {
            request()->merge([
                'title' => request()->ajax_title,
            ]);
        }

        if (request()->has('ajax_parent')) {
            request()->merge([
                'parent' => request()->ajax_parent
            ]);
        }

        if (request()->has('ajax_type')) {
            request()->merge([
                'type' => request()->ajax_type
            ]);
        }

        if (request()->filled('ajax_arguments')) {

            $arguments = explode('&', request()->ajax_arguments);

            foreach ($arguments as $val) {
                $var = explode('=', $val);
                request()->merge([
                    current($var) => end($var)
                ]);
            }
        }

        $addPage = true;

        $config = config('project.content.' . request()->ajax_type . '_list')['is_listable'] ?? (config('project.content.' . request()->ajax_type)['is_listable'] ?? null);

        $has_limit = is_array($config) && array_key_exists('limit', $config) ? intval($config['limit']) : null;

        if ($has_limit) {
            $count_q = Pages::where('type', request('ajax_type'));
            if (request()->has('ajax_parent')) {
                $count_q->where('parent', request('ajax_parent'));
            }
            $items_count = $count_q->count();
            if ($items_count >= $has_limit) {
                $addPage = false;
                $this->responseWarning("Vous avez atteint la limite autorisée.");
            }
        }

        if ($addPage) {
            $this->response = $this->setupPage();

            /*
                    if (request()->has('ajax_with_redirect')) {
                        $this->response['redirect_to'] = url('panel/Publisher/pages/edit/' . $page->id);
                    }
            */
        }

        $this->response['input'] = request()->input();

        return $this->response;
    }

    public function createContent()
    {
        $type = request()->type;

        $position = (int)Pages::where('type', $type)->max('position');

        $this->editable = new Pages();
        $this->editable->title = request()->{app()->getLocale()}['title'];
        $this->editable->type = $type;
        $this->editable->position = $position + 1;
        $this->editable->parent = request()->parent;
        $this->editable->taxonomy = request()->taxonomy;
        $this->editable->published = request()->published ?? null;
        $this->editable->save();

        $this->baseContentSetup();
        $this->reset();

        $this->responseSuccess('Le contenu a été créé');
        $this->flash_to_session();
    }

    public function createContentFromListAction()
    {
        $type = request()->type;
        $position = (int)self::where('type', $type)->max('position');

        $this->editable = new self();
        $this->editable->title = $data->title . ' ' . ($position + 1);
        $this->editable->type = $type;
        $this->editable->parent = $data->id;
        if (array_key_exists('taxonomy', $config['has'][$type])) {
            $this->editable->taxonomy = $config['has'][$type]['taxonomy'];
        }
        if (array_key_exists('access_key', $config['has'][$type])) {
            $this->editable->access_key = Str::random($config['has'][$type]['access_key']);
        }
        $this->editable->position = $position + 1;
        $this->editable->published = null;
        $this->editable->save();

        $this->baseContentSetup();
        $this->reset();

        return $this->editable->id;
    }

    public function createNullContent()
    {
        $type = request()->type;

        $position = (int)Pages::where('type', $type)->max('position');

        $page = new Pages;
        $page->title = "";
        $page->type = $type;
        $page->position = $position + 1;
        $page->parent = request()->parent;
        $page->save();

        foreach ($this->locales() as $lang) {
            PagesData::insert([
                'lg' => $lang,
                'pages_id' => $page->id
            ]);
        }
        $this->responseSuccess('Une nouvelle page a été créé.');

        return $page;
    }

    public function setupPage($stdClass = null)
    {
        $data = $stdClass ?? request()->input();
        $data = json_decode(json_encode($data));
        $position = (int)Pages::max('position');

        $this->editable = new Pages();
        $this->editable->title = $data->title ?? 'Sans titre';
        $this->editable->type = $data->type;
        $this->editable->parent = $data->parent ?? null;
        $this->editable->position = $position + 1;
        $this->editable->published = property_exists($data, 'published') ? 1 : null;
        if (property_exists($data, 'taxonomy')) {
            $this->editable->taxonomy = Str::slug($data->taxonomy, '_');
        }
        if (property_exists($data, 'access_key')) {
            $this->editable->access_key = Str::random($data->access_key);
        }

        $this->editable->save();

        if ($data->type != '') {
            $this->typeConfig = config('project.content.' . $data->type);
        }

        $this->baseContentSetup();

        if (property_exists($data, 'is_nav')) {
            Nav::insert([
                'pages_id' => $this->editable->id,
                'logged' => property_exists($data, 'logged') ? 1 : null,
                'is_primary' => property_exists($data, 'is_primary') ? 1 : null,
                'position' => (int)Nav::max('position') + 1
            ]);
            Cache::forget('nav');
        }
        $this->responseNotice("Le contenu " . ($this->editable->title != 'Sans titre' ? "<em>" . $this->editable->title . "</em>" : '')
            . " a été créé. ID : " . $this->editable->id . " <a href='" . url('panel/Publisher/pages/edit/' . $this->editable->id) . "'>Éditer</a>");

        return $this->response;
    }

    protected function baseContentSetup()
    {
        foreach ($this->locales() as $lang) {

            $url = Str::slug($this->editable->title);

            if (Pages::checkDuplicateUrl($url, $lang, $this->editable->id)) {
                $url = $this->editable->id . '-' . $url;
            }

            PagesData::insert([
                'lg' => $lang,
                'pages_id' => $this->editable->id,
                'title' => $this->editable->title,
                'nav_title' => $this->editable->title,
                'url' => $url
            ]);
        }
    }


    public function addcategories()
    {

        if (strlen(trim((string)request()->ajax_title)) < 1) {
            $this->responseError('Veuillez specifier un titre');
            return $this->response;
        }

        $this->addAjaxPage();

        if ($this->editable->id) {
            $this->response['callback'] = 'pushCategory';
            $this->response['data'] = [
                'title' => $this->editable->title,
                'id' => $this->editable->id
            ];
        }
        return $this->response;
    }

    private function reset()
    {
        Artisan::call('cache:clear', []);
    }

}
