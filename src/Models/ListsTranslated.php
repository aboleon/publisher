<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Publisher\Repositories\Tables;
use Illuminate\Database\Eloquent\Model;

class ListsTranslated extends Model
{

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = Tables::fetch('lists_translated');
        $this->timestamps = false;
    }

}