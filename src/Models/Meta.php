<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Traits\Responses;
use Aboleon\Framework\Traits\Translation;
use Aboleon\Publisher\Repositories\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Meta extends Model
{
    use Responses;
    use Translation;

    public $timestamps = false;
    public array $translatable = [
        'title',
        'abstract',
        'm_title',
        'm_desc',
        'nav_title',
        'url'
    ];
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = Tables::fetch('meta');
    }

    public static function make(Publisher $page): void
    {
        if (request()->has('meta.config')) {
            $page->config = request('meta.config');
        }
        foreach ($page->translatable as $value) {
            foreach (config('translatable.locales') as $locale) {
                $data = request('meta.' . $value . '.' . $locale);
                if ($value == 'url') {
                    $data = Str::slug(request('meta.url.' . $locale) ?: request('meta.title.' . $locale));
                }
                $page->setTranslation($value, $locale, $data);
            }
        }
        $page->save();
    }
}
