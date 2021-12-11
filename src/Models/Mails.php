<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Models;

class Mails extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'publisher_mails';
    protected $fillable = ['pages_id','is_primary','pull_children','position'];

    use \Aboleon\Framework\Traits\Helper {
        \Aboleon\Framework\Traits\Helper::__construct as private Helper__construct;
    }

    public function __construct()
    {
        parent::__construct();
        $this->timestamps = false;
        $this->Helper__construct();
    }

    public function index(): callable
    {
        return view()->first(['panel.emails.index', 'aboleon.publisher::mails.index'])->with('data', self::orderBy('id','desc')->get());
    }

    public function remove(): callable
    {
        self::find($this->object_id)->delete();
        return redirect()->to('panel/Publisher/mails/index');
    }

}
