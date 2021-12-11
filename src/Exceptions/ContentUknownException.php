<?php

namespace Aboleon\Publisher\Exceptions;

use Exception;

class ContentUknownException extends Exception {

    public function render($request)
    {
        return view('aboleon.publisher::errors.404')->with('message', "Ce type de contenu n'est pas défini");
    }
}