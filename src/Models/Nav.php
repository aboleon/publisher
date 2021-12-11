<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Illuminate\Support\{
    Facades\Artisan,
    Str
};
use Aboleon\Framework\Traits\{
    Helper,
    Responses
};
use Helpers;
use Aboleon\Publisher\Traits\Navigation;

class Nav extends \Aboleon\Framework\Models\Nav
{
    protected $table = 'publisher_nav';
    protected $fillable = ['pages_id', 'is_primary', 'pull_children', 'position'];

    use Helper {
        Helper::__construct as private Helper__construct;
    }
    use Navigation;
    use Responses;

    public function __construct()
    {
        parent::__construct();
        $this->Helper__construct();
        $this->timestamps = false;
    }

    public static function linkFromArray(array $array, string $type)
    {
        $link = current(array_filter($array, function ($item) use ($type) {
            return $item['type'] == $type;
        }));
        if ($link) {
            return '<a href="' . $link['url'] . '">' . $link['title'] . '</a>';
        }
    }

    public static function listLink($id, string $titre, $icon = 'list-alt'): string
    {
        return '<li class="listable" data-id="' . $id . '"><a href="' . url('panel/Publisher/pages/list/' . $id) . '"><i class="menu-icon fas fa-' . $icon . '"></i> ' . $titre . '</a></li>';
    }

    public static function pageLink($id, string $titre, string $icon = ''): string
    {
        if (!is_int($id)) {
            $id = Pages::whereType($id)->value('id');
        }
        return '<li class="editable" data-id="' . $id . '"><a href="' . url('panel/Publisher/pages/edit/' . $id) . '">' .
            ($icon ? '<i class="menu-icon fas fa-' . $icon . '"></i> ' : '') .
            '<span class="menu-text">' . $titre . '</span></a></li>';
    }

    public static function withChildren(string $type, string $icon = 'home', bool $base_page = true)
    {
        $page = Pages::where('type', $type)->with([
            'meta',
            'children'
        ])->first();

        if (is_null($page)) {
            return null;
        }

        $nav = '';
        if (!$page->children->isEmpty()) {
            $nav = '<li>
            <a href="#">
            <i class="fas fa-info-circle"></i>
            <span class="menu-text">' . $page->meta->nav_title . '</span>
            <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="nav child_menu">';
            if ($base_page) {
                $nav .= self::pageLink($page->id, is_string($base_page) ? $base_page : 'Présentation');
            }
            foreach ($page->children as $val) {
                $nav .= strstr((string)$val->type, '_list') ? listLink($val->id, $val->meta->nav_title) : self::pageLink($val->id, $val->meta->nav_title);
            }
            $nav .= '</ul></li>';
        } else {
            $nav .= self::pageLink($page->id, $page->meta->nav_title);
        }

        return $nav;
    }

    public function add()
    {
        if (request()->isMethod('post')) {
            return $this->addEntry();
        }
        $page = null;
        $exclude = config('project.config.nav_exclude') ?? [];
        $include = config('project.config.nav_include') ?? [];
        $query = Pages::query();
        $query->whereRaw('`type` not like "%\_%"');
        if ($exclude) {
            $query->where(function ($where) use ($exclude) {
                $where->whereNotIn('type', $exclude);
            });
        }
        $selectables = $query
            ->whereNotIn('id', self::select('pages_id')->whereNotNull('pages_id')->pluck('pages_id'))
            ->with('meta')
            ->get();

        if ($this->object_id) {
            $attach_to = Nav::whereId($this->object_id)->with(['meta', 'customLinks'])->first();
            $page = [
                'id' => $attach_to->id
            ];
            if (empty($attach_to->pages_id)) {
                $page['title'] = $attach_to->customLinks->first()->title;
                $page['url'] = $attach_to->customLinks->first()->url;
            } else {
                $page['title'] = $attach_to->meta->meta->nav_title;
                $page['url'] = $attach_to->meta->meta->url;
            }
        }
        return view('aboleon.publisher::nav.add')->with([
            'page' => $page,
            'selectables' => $selectables,
            'subnav' => request()->has('subnav')
        ]);
    }


    public function edit()
    {
        // TODO: Permettre l'édition de la nav
        $this->editable = self::findOrFail($this->object_id);

        if (request()->isMethod('post')) {

            $this->editable->is_primary = request()->has('is_primary') ? 1 : null;
            $this->editable->logged = request()->has('logged') ? 1 : null;
            $this->editable->pull_children = request()->has('pull_children') ? 1 : null;
            $this->editable->save();

            foreach (Project::locales() as $locale) {
                $this->editable->meta->translations()->where('lg', $locale)->update([
                    'meta_title' => request()->{$locale}['meta_title'],
                    'meta_description' => request()->{$locale}['meta_description'],
                    'nav_title' => request()->{$locale}['nav_title'],
                    'url' => strlen(trim(request()->{$locale}['url'])) < 1 ? Str::slug(strlen(trim(request()->{$locale}['nav_title'])) < 1 ? request()->{$locale}['meta_title'] : request()->{$locale}['nav_title']) : request()->{$locale}['url'],
                ]);
            }

            Artisan::call('cache:clear');
        }

        return view('aboleon.publisher::nav.edit')->with('data', $this->editable);
    }

    public function index()
    {
        // TODO: Modification des propritées de la NAV déjà enregistré et de la Page, séparément
        $this->composesNavs();

        return view()->first(['panel.nav.index', 'aboleon.publisher::nav.index'])->with([
            'primary_nav' => $this->navigation['primary'],
            'secondary_nav' => $this->navigation['secondary'],
            'children' => $this->navigation['children']
        ]);
    }

    public function panel_list_nav(string $type, $icon = 'home', bool $base_page = true): ?string
    {
        $config = collect(config('project.content.' . $type));
        $lists = (bool)$config->has('has');

        $pages = Pages::where('type', $type)->with([
            'meta',
            'children'
        ])->get();

        $nav = '';

        if ($pages->isEmpty()) {
            return null;
        }
        foreach ($pages as $page) {
            $nav .= '<li data-id="' . $page->id . '">
            <a href="#">
            <i class="menu-icon fas fa-' . $icon . '"></i> <span class="menu-text">' . $page->meta->nav_title . '</span>
            <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="nav child_menu">';
            if ($base_page) {
                $nav .= self::pageLink($page->id, is_string($base_page) ? $base_page : 'Présentation');
            }
            $nav .= $this->panel_nav_sublists($lists, $page, $config);
            $nav .= '</ul></li>';
        }
        return $nav;
    }

    public function remove()
    {
        self::where('id', request()->object_id)->delete();
        Artisan::call('cache:clear');
        session()->flash('session_message', "L'entrée a été supprimée");
        return redirect()->to('/panel/Publisher/nav/index');
    }

    public function sortable()
    {
        foreach (request()->position as $k => $v) {
            static::where('id', $k)->update(['position' => $v]);
        }
        Artisan::call('cache:clear');
        $this->responseSuccess("L'ordre a été mis à jour");
        return $this->response;
    }

    private function addEntry()
    {
        switch (request()->choose_link) {
            case 'page':
                if (request()->page == 'none') {
                    session()->flash('session_message', "Vous n'avez sélectionné aucune page");
                    return redirect()->to('panel/Publisher/nav/add');
                }
                $nav_id = Nav::insertGetId([
                    'pages_id' => request()->page,
                    'parent' => request()->attach_to ?? null,
                    'position' => Nav::max('position') + 1,
                    'is_primary' => (request()->attach_to != 'subnav' ? 1 : null)
                ]);
                if (request()->has('pull_children')) {
                    $children = Pages::whereParent(request()->page)->first()->load('children')->children->sortBy('position')->pluck('id');
                    if ($children->isNotEmpty()) {
                        foreach ($children as $item) {
                            Nav::insert([
                                'pages_id' => $item,
                                'parent' => $nav_id,
                                'position' => Nav::max('position') + 1,
                                'is_primary' => (request()->attach_to != 'subnav' ? 1 : null)
                            ]);
                        }
                    }
                }
                break;
            case 'custom':
                $nav = new Nav();
                $nav->parent = request()->attach_to ?? null;
                $nav->position = Nav::max('position') + 1;
                $nav->is_primary = (request()->attach_to != 'subnav' ? 1 : null);
                $nav->save();

                foreach (request()->nav_links as $lang => $array) {
                    NavLinks::insert([
                        'lg' => $lang,
                        'title' => $array['title'],
                        'url' => $array['url'],
                        'nav_id' => $nav->id
                    ]);
                }
                break;
        }
        Artisan::call('cache:clear');
        session()->flash('session_message', "L'entrée a été ajoutée");
        return redirect()->to('panel/Publisher/nav/index');
    }

}

