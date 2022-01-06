<?php

namespace Aboleon\Publisher\Formatters;

use Aboleon\Publisher\Models\Lists as Listables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Lists
{

    public array $rendered;
    public array $links;
    public string $locale;
    public array $listable = [];
    public string $prefix;
    protected $collection;
    protected string $assigned_pairs = '';


    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    public function assigned(): array
    {
        return $this->listable['assigned'];
    }

    public function buildCollection(int $list_id)
    {
        $this->collection = Listables::where('list_id', $list_id)->get();
        return $this;
    }

    public function crawl($item): static
    {
        $this->rendered[$item['id']] = $item['content'];
        $this->listable['ids'][] = $item['id'];
        if (!is_null($item['parent'])) {
            $this->loop($item, $this->collection);
        }
        return $this;
    }

    /**
     * @param \Illuminate\Support\Collection $nodes
     * Flattened collection of content type nodes
     * @return void
     */
    public function getListable(string $listable, Collection $nodes): static
    {
        $node = $nodes->where('params.id', $listable)->first();
        if ($node) {
            $this->listable = [
                'list_id' => $node->params['list_id'],
                'node_id' => $node->id
            ];
        }
        return $this;
    }

    public function fetchById($id)
    {
        $cat = Listables::where('id', $id)->first()->toArray();
        $this->buildCollection($cat['list_id']);
        $this->crawl($cat);
        return $this;
    }

    public function fetchIds()
    {
        return $this->listable['ids'];
    }

    public function fetchLinks(string $separator = ' / '): array
    {
        $this->links = [];
        if ($this->rendered) {
            foreach ($this->rendered as $key => $item) {
                $links = [];
                $href = '';
                foreach ($item as $link) {
                    $href .= Str::slug($link) . '/';
                    $links[] = '<a href="' . url($this->prefix . '/' . $key . '/' . $href) . '">' . $link . '</a>';
                }
                $this->links[] = [
                    'label' => current($item),
                    'link' => current($links),
                    'ariane' => implode($separator, array_reverse($links)),
                    'links' => $links
                ];
            }
        }
        return $this->links;
    }

    public function fetchRaw(): array
    {
        return $this->rendered;
    }

    public function filterAssigned(Collection $content): static
    {
        $this->listable['assigned'] = $content->where('node_id', $this->listable['node_id'])->pluck('value')->toArray();

        if ($this->listable['assigned']) {

            $this->assigned_pairs = implode('_', $this->listable['assigned']);

            if (!isset($this->listable['names'][$this->assigned_pairs])) {
                $this->listable['names'][$this->assigned_pairs] = $this->listable['assigned']
                    ? collect(Listables::whereIn('id', $this->listable['assigned'])->get()->toArray())
                    : null;
            }
        }
        return $this;
    }

    public function prefix(string $prefix = ''): static
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function param(string $param): string|int|null
    {
        return $this->params()[$param] ?? null;
    }

    public function params(): array
    {
        return [
            'list_id' => $this->listable['list_id'],
            'node_id' => $this->listable['node_id'],
        ];
    }

    public function links(): array
    {
        return $this->regroup($this->listable['names'][$this->assigned_pairs])->fetchLinks();
    }


    public function data()
    {
        return $this->listable;
    }

    public function regroup(?Collection $array): static
    {
        $this->rendered = [];
        if ($array) {
            foreach ($array as $item) {
                $this->rendered[$item['id']] = [
                    $item['content'][$this->locale]
                ];
                if (!is_null($item['parent'])) {
                    $this->loop($item, $array);
                }
            }
        }
        return $this;
    }

    protected function loop($item, $array)
    {
        $parent = $array->where('id', $item['parent'])->first();

        if ($parent) {
            $this->listable['ids'][] = $parent['id'];
            $this->rendered[$item['id']][] = $parent['content'][$this->locale] ?? $parent['content'];
            $this->loop($parent, $array);
        }
    }
}