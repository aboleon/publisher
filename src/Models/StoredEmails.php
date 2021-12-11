<?php declare(strict_types = 1);

namespace Aboleon\Publisher\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use \Aboleon\Framework\Traits\Responses;

class StoredEmails extends Model
{

    protected $table = 'publisher_mails';
    protected $guarded = [];

    use \Aboleon\Framework\Traits\Helper {
        \Aboleon\Framework\Traits\Helper::__construct as private Helper__construct;
    }
    use Responses;

    public function __construct()
    {
        $this->Helper__construct();
    }

    public function index()
    {
        return view('panel.stored_emails.index', ['data' => self::orderBy('id', 'desc')->paginate(15)]);
    }

    public function edit()
    {
        return view('panel.stored_emails.edit', ['data' => self::where('id', $this->object_id)->first()]);
    }

}
