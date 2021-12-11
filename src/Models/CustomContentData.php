<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Models;

class CustomContentData extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'publisher_custom_content_data';
    protected $fillable = ['custom_content_id','lg','content'];

    public function __construct()
    {
        $this->timestamps = false;
    }

}