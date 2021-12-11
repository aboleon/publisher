<?php declare(strict_types=1);

namespace Aboleon\Publisher\Http\Controllers;

use App;
use Route;
use Illuminate\Routing\Controller;

class PanelController extends Controller
{
    protected $object;
    protected $method;
    protected $publisher = 'Publisher';

    public function ajax()
    {
        return $this->distribute(request()->ajax_object, request()->ajax_action);
    }

    public function distribute(string $object = '', string $method = '')
    {
        $class = 'Aboleon\Publisher\Models\\' . ucfirst($object);

        if (!class_exists($class)) {
            return view('core::errors.error')->with('error', trans('core.errors.UncallableClass', ['class' => $class]));
        }
        if (!method_exists($class, $method) or !(new \ReflectionMethod($class, $method))->isPublic()) {
            return view('core::errors.error')->with('error', trans('core::errors.UnknownMethodCallException', ['class' => $class, 'method' => $method]));
        }

        return (new $class)->{$method}();
    }


    public function exports($tunnel, $export_class)
    {

        $error = false;

        $tunnel = ($tunnel == 'project' ? 'Projects\\' . config('app.project') : 'Aboleon\Publisher') . '\\Http\\';
        $export = $tunnel . 'Exports\\' . $export_class;
        $final_export_class = $tunnel . 'Models\\Export' . $export_class;

        if (!class_exists($export) or !class_exists($final_export_class)) {
            $error = trans('core::ui.errors.UncallableClass', ['class' => $export . ' ou ' . $final_export_class]);
            return view('core::panel.error')->with('error', $error);
        }

        try {
            $is_callable = (new \ReflectionMethod($final_export_class, 'export'));
            if (!$is_callable->isPublic()) {
                $error = "La méthode export() sur " . $final_export_class . " ne peut pas être appelée";
            }
        } catch (\ReflectionException $e) {
            $error = $e->getMessage();
        }

        if ($error) {
            return view('core::panel.error')->with('error', $error);
        }

        return $final_export_class::export();
    }

    public function list(string $type)
    {
        $config = config('project.content.' . $type);

        if (is_null($config)) {
            return view('aboleon.publisher::errors.404')->with('message', "Ce type de contenu n'est pas défini");
        }

        $data = Publisher::whereType($type)->paginate(20);

        if (!array_key_exists('has', $config)) {
            return view('aboleon.publisher::errors.404')->with('message', trans('errors.has_no_list'));
        }

        /*
        $lists = count($config['has']);

        if (request()->isMethod('post')) {

            $position = (int)Publisher::where('type', $type)->max('position');

            $content = new Publisher();
            $content->title = $data->title . ' ' . ($position + 1);
            $content->type = $type;
            $content->parent = $data->id;
            if (array_key_exists('taxonomy', $config['has'][$type])) {
                $content->taxonomy = $config['has'][$type]['taxonomy'];
            }
            if (array_key_exists('access_key', $config['has'][$type])) {
                $content->access_key = Str::random($config['has'][$type]['access_key']);
            }
            $content->position = $position + 1;
            $content->published = null;
            $content->save();

            $this->baseContentSetup();

            return redirect()->to('panel/Publisher/pages/edit/' . $content->id);
        }
        */

        $pages = self::where('parent', $data->id)->type($type)->archives()->orderBy('position')->paginate(15);

        return view()->first([
            'listings.' . $type,
            'aboleon.publisher::pages.list'
        ])->with([
            'data' => $data,
            'pages' => $pages,
            'items_count' => $pages->count(),
            'config' => $config,
            'response' => $this->response,
            'listConfig' => $config['has'][$type],
            'archives' => request()->has('archives'),
            'list_type' => $type,
            'typeConfig' => $this->typeConfig
        ]);
    }

}
