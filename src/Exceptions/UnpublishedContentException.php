<?php

namespace Aboleon\Publisher\Exceptions;

use Exception;

class UnpublishedContentException extends Exception {

    public function render($request)
    {
        return view('errors.missing')->with('data',trans('project.PageIsNotPublished'));
    }
}
