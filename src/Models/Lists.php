<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Traits\Translation;
use Aboleon\Publisher\Repositories\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lists extends Model
{
    use Translation;

    public array $translatable = [
      'content'
    ];
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = Tables::fetch('lists');
        $this->timestamps = false;
    }

    public static function tag(array $element)
    {
        return array_key_exists('tag', $element) ? $element['tag'] : 'div';
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent')->with(['children']);
    }

    public function printNestedTree(array $currentCategories, $field_name): string
    {
        $html ='';
        if ($this->children->isNotEmpty()) {
            $html.= "<ul>";
            foreach ($this->children as $val) {
                $count = $val->children->count();
                $html.= "<li><div class='form-check'>
                    <input class='form-check-input' id='ch_".$val->id."' type='checkbox' name='" . $field_name . "' value='" . $val->id . "' " . (in_array($val->id, $currentCategories) ? "checked='checked'" : null) . " />
                    <label class='form-check-label' for='ch_".$val->id."'>
                    <span" . ($count ? ' class="has"' : '') . ">" . $val->content . " " . ($count ? ' (' . $count . ')' : '') . "</span>";
                $html.= $val->printNestedTree($currentCategories, $field_name);
                $html.= "</label><span class='sublistable btn btn-xs btn-info'><i class='fas fa-plus'></i></span></div></li>";
            }
            $html.= "</ul>";
        }
        return $html;
    }
}