<?php

namespace Aboleon\Publisher\Formatters;

use Aboleon\Publisher\Models\Lists as Listables;
use Aboleon\Publisher\Models\Publisher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ListableContent
{

    public string $locale;
    protected $listable;
    protected $listable_query;
    protected $categories;
    protected string $type;


    public function __construct(object $listable, string $type)
    {
        $this->locale = app()->getLocale();
        $this->listable = $listable;
        $this->type = $type;
        $this->setCategories();
    }


    public function exclude(int|array $exclude): static
    {
        if (is_int($exclude)) {
            $exclude = [$exclude];
        }
        $this->listable_query = $this->listable_query->exclude($exclude);
        return $this;
    }

    public function setCategories(): static
    {
        $this->categories = (new Lists)
            ->prefix('rubriques')
            ->getListable($this->type, $this->listable->nodes);
        return $this;
    }

    public function ofCategory(array $categories): static
    {
        $this->listable_query = Publisher::select('publisher.id as id', 'publisher.*')
            ->where('type', $this->listable->config->id)
            ->join('publisher_content as b', function ($join) use ($categories) {
                $join->on('b.pages_id', '=', 'publisher.id')->where('b.node_id', $this->categories->param('node_id'))->whereIn('value', $categories);
            })->distinct()->with('content', 'author', 'accesskey');

        return $this;
    }

    public function orderBy(string $order='')
    {
        $this->listable_query = match($order) {
            'random' => $this->listable_query->inRandomOrder(),
            default => $this->listable_query->orderBy('id', 'desc')
        };
        return $this;
    }

    public function take(int $number=null)
    {
        if ($number) {
            $this->listable_query = $this->listable_query->take($number);
        }
        return $this;
    }

    public function get()
    {
        return $this->listable_query->get();
    }

    public function paginate(int $number=15)
    {
        return $this->listable_query->paginate($number);
    }

    public function categories()
    {
        return $this->categories;
    }



}