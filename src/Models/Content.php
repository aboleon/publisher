<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Traits\Translation;
use Aboleon\Publisher\Repositories\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    use Translation;

    public array $translatable = [
        'content'
    ];
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

}