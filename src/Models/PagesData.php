<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Models;

class PagesData extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'publisher_pages_data';
    protected $guarded = [];

    use \Aboleon\Framework\Traits\Helper {
        \Aboleon\Framework\Traits\Helper::__construct as private Helper__construct;
    }

    public function __construct()
    {
        parent::__construct();
        $this->timestamps = false;
        $this->Helper__construct();
    }

    public function page()
    {
        return $this->belongsTo(Pages::class,'pages_id');
    }

}
