<?php

declare(strict_types = 1);

namespace Aboleon\Publisher\Interfaces;

interface ClassManagedContent
{
    /* Generates the output in BO */
    public function get(\Aboleon\Publisher\Models\Pages $data):string;
}
