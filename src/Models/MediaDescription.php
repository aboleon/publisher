<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Publisher\Repositories\Tables;
use Illuminate\Database\Eloquent\Model;

class MediaDescription extends Model
{

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Tables::fetch('nodes_media_content');
        $this->timestamps = false;
    }
}
