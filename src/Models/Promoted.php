<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Illuminate\Database\Eloquent\Model;
use Aboleon\Framework\Traits\Ajax;
use \Aboleon\Framework\Traits\Responses;

class Promoted extends Model
{
    use Ajax, Responses;

    public $timestamps = false;
    protected $table = 'publisher_promoted';

    public static function fetchPromotedContent(int $limit=8)
    {
        return Pages::query()
            ->published()
            ->select('publisher_pages.id', 'title', 'type','parent','access_key', 'b.position')
            ->join('publisher_custom_content as a', function ($join) {
                $join->on('a.pages_id', '=', 'publisher_pages.id')->where('field', 'on_home');
            })
            ->leftJoin('publisher_promoted as b', function ($join) {
                $join->on('b.pages_id', '=', 'publisher_pages.id');
            })
            ->orderBy('b.position')
            ->orderBy('title')
            ->with(['extendedMeta', 'customContent','mediaContent:id,pages_id,varname,content'])
            ->take($limit)
            ->get();
    }

    public function sortable()
    {
        Promoted::query()->delete();
        foreach (request()->position as $key => $val) {
            Promoted::insert(['pages_id'=>$key, 'position' => $val]);
        }
        $this->responseSuccess("L'ordre a été mis à jour");
        return $this->response;
    }


}
