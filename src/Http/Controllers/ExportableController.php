<?php declare(strict_types=1);

namespace Aboleon\Publisher\Http\Controllers;

use Illuminate\Support\Arr;
use Publisher;
use Aboleon\Publisher\Exceptions\ContentUknownException;
use Aboleon\Publisher\Models\PagesCreateContent;
use Illuminate\Routing\Controller;

class ExportableController extends Controller
{

    public function exports($tunnel, $export_class)
    {

        $error = false;

        $tunnel = '\App\\' . ($tunnel == 'project' ? config('app.project') : 'Modules\Publisher'). '\\';
        $export = $tunnel  . 'Exports\\' . $export_class;
        $final_export_class = $tunnel . 'Models\\Export'. $export_class;


        if (!class_exists($export) or !class_exists($final_export_class) ) {
            $error = trans('publisher.errors.UncallableClass', ['class' => $export.' ou '.$final_export_class]);
            return view('publisher.panel.error')->with('error', $error);
        }

        try {
            $is_callable = (new \ReflectionMethod($final_export_class, 'export'));
            if(!$is_callable->isPublic()) {
                $error = "La méthode export() sur " . $final_export_class ." ne peut pas être appelée";
            }
        }
        catch (\ReflectionException $e) {
            $error = $e->getMessage();
        }

        if ($error) {
            return view('publisher.panel.error')->with('error', $error);
        }


        return $final_export_class::export();
    }
}
