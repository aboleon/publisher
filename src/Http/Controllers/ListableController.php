<?php declare(strict_types=1);

namespace Aboleon\Publisher\Http\Controllers;

use Illuminate\Support\Arr;
use Aboleon\Publisher\Exceptions\ContentUknownException;
use Aboleon\Publisher\Models\PagesCreateContent;
use Illuminate\Routing\Controller;

class ListableController extends Controller
{

    private $config;
    private $config_type;
    private $owner = null;
    private $type;
    private $parent_type = null;
    private $result;

    public function basic(string $type, string $parent = '')
    {
        $this->type = $type;
        $this->parent_type = $parent;
        $this->assertListable();
        $parent_id = null;

        if ($this->parent_type) {
            if (strstr($this->parent_type, '__')) {
                $str = explode('__', $this->parent_type);
                $parent_id = end($str);
                $this->owner = Publisher::where('type', $this->type)->whereParent($parent_id)->value('id');
            } else {
                if (is_numeric($this->parent_type)) {
                    $this->owner = $this->parent_type;
                } else {
                    $this->owner = Publisher::where('type', $this->type)->whereParent(Publisher::whereType($this->parent_type)->value('id'))->value('id');
                }
            }
        }
        $this->result = Publisher::whereType(str_replace('_list', '', $this->type));
        if ($parent_id) {
            $this->result->whereParent($this->owner);
        }
        $this->result->with(['meta', 'customContent'])->withCount('children');
        $this->ofParent();
        $this->defineOrder();
        $items = $this->result->paginate(20);

        return view()->first([
            'panel.listings.' . $this->type,
            'aboleon.publisher::pages.lists.basic'
        ])->with([
            'pages' => $items,
            'items_count' => $this->result->count(),
            'config' => $this->config,
            'config_type' => $this->config_type,
            'type' => $this->type,
            'parent' => $this->owner,
            'parent_type' => $this->parent_type,
            'list_title' => $this->config['label'] ?? 'Enregistrements',
            'is_sortable' => in_array('sortable', $this->config['is_listable']),
            'can_be_deleted' => !in_array('no_delete', $this->config['is_listable']),
            'can_add' => !in_array('no_add', $this->config),
            'with_image' => in_array('with_image', $this->config),
            'has_limit' => $this->config['is_listable']['limit'] ?? null,
            'has_links' => isset($this->config['no_link']),
            'url_prefix' => ''
        ]);
    }

    public function createBasic(string $type, string $parent = '')
    {
        $this->type = $type;
        $this->assertListable();
        $this->owner = $parent;


        return view()->first([
            'listings.' . $this->type . '_create',
            'aboleon.publisher::pages.lists.basic_create'
        ])->with([
            'config' => $this->config,
            'type' => $this->type,
            'content' => new \stdClass,
            'parent' => $this->owner
        ]);
    }

    public function storeBasic()
    {
        $type = strstr(request('type'), '_list') ? request('type') : request('type').'_list';

        if (is_null(config('project.content.'.$type)))  {
            $type = request('type');
        }

        (new PagesCreateContent)->createContent();

        if (request()->has('save_and_add')) {
            return redirect()->back();
        }
        if (request()->filled('parent_type')) {
            return redirect()->to('panel/Publisher/pages/list/' . $type . '/parent/' . request('parent_type').(request()->filled('parent_id') ? '__'.request('parent_id') : ''));
        }
        return redirect()->route('publisher.list_basic', ['var' => $type]);
    }

    private function assertListable()
    {
        $is_listable = true;

        $this->config = config('project.content.' . $this->type);
        $this->config_type = 'final';
        if (is_null($this->config) or !isset($this->config['is_listable'])) {
            $is_listable = false;
        }

        if (!$is_listable) {
            throw new ContentUknownException();
        }
    }

    private function defineOrder()
    {
        if (Arr::has($this->config, 'is_listable.order')) {
            $this->result->orderBy($this->config['is_listable']['order'][0], $this->config['is_listable']['order'][1]);
        } else {
            $this->result = $this->result->orderBy('position');
        }
    }

    private function ofParent()
    {
        if ($this->owner) {
            $this->result = $this->result->whereParent($this->owner);
        }
    }
}
