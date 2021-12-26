<?php declare(strict_types=1);

namespace Aboleon\Publisher\Http\Controllers;

use Aboleon\Framework\Traits\Locale;
use Aboleon\Framework\Traits\Responses;
use Aboleon\Framework\Traits\Validation;
use Aboleon\Publisher\Models\{
    Configs,
    ConfigsElements,
    Nodes,
    Pages
};
use Aboleon\Publisher\Repositories\Tables;
use App\Http\Publisher\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class LaunchpadController extends \Aboleon\Publisher\Http\Controllers\Controller
{
    private $type;
    private $editable;

    use Locale;
    use Responses;
    use Validation;

    public function index(): Renderable
    {
        return view('aboleon.publisher::launchpad.index')->with('pages', Configs::orderBy('title')->paginate(25));
    }

    public function create(): Renderable
    {
        return view('aboleon.publisher::launchpad.create')->with([
            'data' => new Configs,
            'route' => route('aboleon.publisher.launchpad.store'),
            'elements' => (new ConfigsElements)->all(),
            'listables' => Configs::listables(),
            'associatables' => Configs::associatables(),
        ]);
    }

    public function edit($id): Renderable
    {
        $data = Configs::withTrashed()->findOrFail($id)->load('nodes');//.children

        return view('aboleon.publisher::launchpad.create')->with([
            'data' => $data,
            'route' => route('aboleon.publisher.launchpad.update', $data->id),
            'elements' => (new ConfigsElements)->forGroup($data->group),
            'listables' => Configs::listables(),
            'associatables' => Configs::associatables(),
        ]);
    }

    public function update(Configs $launchpad): RedirectResponse
    {
        //  de(request());
        $this->requestValidation();
        $this->validation_rules['type'] = 'required|unique:' . Tables::fetch('configs') . ',type,' . $launchpad->id;
        $this->validation();
        //de(request('config'));

        //  try {
        $launchpad->update($this->validated_data);

        if (request()->has('config.elements')) {
            foreach (request('config.elements') as $section) {
                if (!array_key_exists('id', $section)) {
                    unset($section['is_deleted'], $section['uuid']);
                    Nodes::makeSet($launchpad, $section);
                } else {
                    Nodes::updateNode($section);
                    $elements = $section['elements'] ?? [];
                    if ($elements) {
                        foreach ($elements as $element) {
                            if (!array_key_exists('id', $element)) {
                                unset($element['is_deleted'], $element['uuid']);
                                Nodes::find($section['id'])->children()->save(new Nodes($element));
                            } else {
                                Nodes::updateNode($element, (int)$section['id']);
                            }
                        }
                    }
                }
            }
        }
        Artisan::call('cache:clear');
        $this->responseSuccess("Le type de contenu a été mis à jour.");
        $this->redirect_to = route('aboleon.publisher.launchpad.edit', $launchpad->id);
        /*   } catch (Throwable $e) {
               $this->responseException($e);
           } finally { */
        return $this->sendResponse();
        //   }
    }

    public function store(): RedirectResponse
    {
        //d(request());
        $this->requestValidation();
        $this->validation_rules['type'] = 'required|unique:' . Tables::fetch('configs');
        $this->validation();

        try {

            $config = Configs::create($this->validated_data);
            if (request()->has('config.elements')) {
                foreach (request('config.elements') as $section) {
                    Nodes::makeSet($config, $section);
                }
            }

            $this->responseSuccess("Le type de contenu a été créé.");
            $this->redirect_to = route('aboleon.publisher.launchpad.index');

            Artisan::call('cache:clear');

        } catch (Throwable $e) {
            $this->responseException($e);
        } finally {
            return $this->sendResponse();
        }

    }

    public function destroy($id): RedirectResponse
    {
        try {
            $page = Pages::withTrashed()->findOrFail($id);
            $config = collect(config('project.content.' . $page->type));
            /*
                        if (!$config->contains('archive')) {
                            $page->enableForceDelete();
                        }
             */
            // Children
            $page->removeChildren();

            if ($page->forceDelete) {
                //Media::removeAttachedMedia($page);
                $page->forceDelete();
                $this->responseNotice("Le contenu a été définitivement supprimé.");
            } else {
                $page->delete();
                $this->responseNotice("Le contenu a été placé dans la corbeille.");
            }
        } catch (Throwable $e) {
            $this->standardResponseError($e);

        } finally {
            return $this->sendResponse();
        }
    }

    private function requestValidation()
    {
        $this->validation_rules = [
            'title' => 'required',
            //  'config' => 'required|array',
            'configs' => 'required|array'
        ];

        $this->validation_messages = [
            'title.required' => trans('aboleon.framework::validations.required', ['param' => 'Le titre']),
            'type.required' => trans('aboleon.framework::validations.required', ['param' => 'Le type de contenu']),
            'type.unique' => trans('aboleon.framework::validations.unique', ['param' => 'Le type de contenu <em>' . request('type') . '</em>']),
            //   'config.required' => trans('aboleon.framework::validations.required', ['param' => 'La configuration du contenu']),
            //  'config.array' => "La configuration des contenus est obligatoire.",
        ];

    }
}
