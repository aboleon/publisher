<?php

declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Aboleon\Framework\Facades\Helpers;
use \Aboleon\Framework\Traits\Responses;
use Project;

class InstantSearch extends Pages
{
    use Responses;

    /** Content types filter
     * @var mixed|string
     */
    private $content_type = null;

    /** Keywords as input
     * @var array
     */
    private $keywords;

    /** Parsed keywords
     * @var array
     */
    private $parsed_keywords = [];

    /** Selectable fields
     * @var array
     */
    private $selectables = [
        'publisher_pages.id',
        'publisher_pages.parent',
        'b.title',
        'publisher_pages.published',
        'b.url',
        'publisher_pages.updated_at',
        'publisher_pages.type'
    ];

    protected $searchable = 'b.url,b.title,b.meta_title,b.meta_description';

    public function __construct()
    {
        if (request()->filled('type')) {
            $this->content_type = request()->type;
        }
        $this->keywords = request()->keyword;
        $this->prepareKeywords();
    }

    /** The main search function
     * @return $this
     */
    public function search(): self
    {
        $results = $this->fetchResults();

        $this->response['items'] = $results;
        if (request()->has('callback')) {
            $this->response['callback'] = request()->callback;
        }
        if (!empty($this->content_type) && $this->content_type != 'all' && !strstr(',', $this->content_type)) {
            $this->response['hide_type'] = true;
        }
        return $this;
    }

    /**
     * Search results as json
     */
    public function jsonSearch(): \Illuminate\Http\JsonResponse
    {
        $this->search();
        return response()->json($this->response);
    }

    /**
     * The results collection queries
     */
    public function fetchResults()
    {
        $main_content = $this->mainContentQuery();
        $bloc_as_pages = $this->blocsAsPagesQuery();
        //de(Helpers::printSql($main_content->union($bloc_as_pages)));
        return $main_content->union($bloc_as_pages)->get();
    }

    /**
     * Filter based on main content types
     */
    public function scopeOfType(Builder $query): Builder
    {

        if (empty($this->content_type)) {
            return $query;
        }
        if ($this->content_type == 'all') {
            $query->whereIn('publisher_pages.type', Project::allReadableContentTypes());
        } elseif (strstr(',', $this->content_type)) {
            $query->whereIn('publisher_pages.type', explode(',', $this->content_type));
        } else {
            $query->where('publisher_pages.type', $this->content_type);
        }

        return $query;
    }

    /**
     * Filter on the parent content types (when fetching blocs)
     */
    public function scopeParentType(Builder $query): Builder
    {
        if (!empty($this->content_type) && $this->content_type != 'all') {
            $query->join('publisher_pages as e', function ($join) {
                $join->on('publisher_pages.parent', '=', 'e.id');
                if (strstr(',', $this->content_type)) {
                    $join->whereIn('e.type', explode(',', $this->content_type));
                } else {
                    $join->where('e.type', $this->content_type);
                }
            });
        }
        return $query;
    }

    /**
     * Filter only on published results
     */
    public function scopePublished($query)
    {
        if (request()->has('published')) {
            $query->whereNotNull('published');
        }
        return $query;
    }

    /**
     * Parsing of the keywords input
     */
    private function prepareKeywords(): void
    {
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $this->keywords = str_replace($reservedSymbols, '', $this->keywords);
        $this->parsed_keywords = array_filter(explode(' ', trim($this->keywords)), function ($item) {
            return strlen(trim($item, ' -')) > 2;
        });

        foreach ($this->parsed_keywords as $key => $word) {
            if (strlen($word) >= 3) {
                $this->parsed_keywords[$key] = '+' . $word . '*';
            }
        }
        $this->parsed_keywords = implode(' ', $this->parsed_keywords);
    }

    /**
     * Query on the main content types
     */
    private function mainContentQuery()
    {
        return self::query()
            ->select($this->selectables)
            ->selectRaw("MATCH ({$this->searchable}) AGAINST (? IN BOOLEAN MODE) AS relevance_score",
                [$this->parsed_keywords])
            ->published()
            ->ofType()
            ->join('publisher_pages_data as b', function ($join) {
                $join->on('publisher_pages.id', '=', 'b.pages_id')
                    ->whereRaw("MATCH ({$this->searchable}) AGAINST (? IN BOOLEAN MODE)", $this->parsed_keywords);
            })
            ->with(['hasParent', 'hasParent.meta'])
            ->orderByDesc('relevance_score')
            ->orderBy('title')
            ->take(15);
    }

    /**
     * Query on the bloc content types set up to show as individual pages
     */
    private function blocsAsPagesQuery()
    {
        return self::query()
            ->select($this->selectables)
            ->selectRaw("MATCH ({$this->searchable}) AGAINST (? IN BOOLEAN MODE) AS relevance_score", [$this->parsed_keywords])
            ->where('publisher_pages.type', 'bloc')
            ->published()
            ->parentType()
            ->join('publisher_pages_data as b', function ($join) {
                $join->on('publisher_pages.id', '=', 'b.pages_id')
                    ->whereRaw("MATCH ({$this->searchable}) AGAINST (? IN BOOLEAN MODE)", $this->parsed_keywords);
            })
            ->join('publisher_custom_content as d', function ($join) {
                $join->on('publisher_pages.id', '=', 'd.pages_id')
                    ->whereRaw("MATCH ({$this->searchable}) AGAINST (? IN BOOLEAN MODE)", $this->parsed_keywords);
            })
            ->with(['hasParent', 'hasParent.meta'])
            ->orderByDesc('relevance_score')
            ->orderBy('title')
            ->take(15);
    }
}
