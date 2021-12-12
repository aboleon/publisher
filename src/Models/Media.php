<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Traits\Translation;
use Aboleon\Publisher\Repositories\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use Translation;

    public array $translatable = [
      'description'
    ];
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Tables::fetch('nodes_media');
        $this->timestamps = false;
    }
}
