<?php

namespace Aboleon\Publisher\Models;

use Illuminate\Database\Eloquent\Model;

class Forms extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'publisher_forms';

    public static function selectables(): array
    {
        $forms = [];
        $data = collect(config('forms'))->pluck('name')->toArray();

        foreach($data as $item) {
            $forms[$item] = __('forms.labels.'.$item);
        }
        return $forms;

    }

    public static function label($item): string
    {
        return __('forms.labels.'.$item);
    }
}
