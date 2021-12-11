<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Publisher\Repositories\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = Tables::fetch('content');
        $this->timestamps = false;
    }

    public static function tag(array $element)
    {
        return array_key_exists('tag', $element) ? $element['tag'] : 'div';
    }

    public function translated(): HasMany
    {
        return $this->hasMany(ContentTranslated::class, 'content_id');
    }

    public function translatedFor(string $locale)
    {
        return $this->translated->where('locale', $locale);
    }
}