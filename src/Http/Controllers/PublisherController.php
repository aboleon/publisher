<?php declare(strict_types=1);

namespace Aboleon\Publisher\Http\Controllers;

use Aboleon\Framework\Models\Accesskeys;
use Aboleon\Framework\Traits\Locale;
use Aboleon\Framework\Traits\Responses;
use Aboleon\Framework\Traits\Validation;
use Aboleon\Publisher\Repositories\Tables;
use App\Http\Publisher\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Aboleon\Publisher\Exceptions\ContentUknownException;
use Aboleon\Publisher\Models\{
    Configs,
    Content,
    ContentTranslated,
    Meta,
    Publisher
};

use Throwable;

class PublisherController extends \Aboleon\Publisher\Http\Controllers\Controller
{
    private $type;
    private $editable;

    use Locale;
    use Responses;
    use Validation;

    public function index(?string $oftype=null): Renderable
    {
        $type = !empty($oftype) ? Configs::query()->where('type', $oftype)->first() : null;
        return view('aboleon.publisher::pages.index')->with([
            'pages' => Publisher::ofType($type)->with('configs')->paginate(25),
            'type' => $type
        ]);
    }

    public function create(Configs $launchpad): RedirectResponse
    {
        $page = Publisher::create([
            'type' => $launchpad->id,
        ]);

        Accesskeys::create([
            'accessible_id' => $page->id,
            'accessible_type' => Publisher::class,
            'access_key' => Accesskeys::generateAccessKey()
        ]);

        return redirect()->route('aboleon.publisher.pages.edit', $page->id);

    }

    public function show(Publisher $page)
    {
        $page->configs->generateView($page);
    }

    public function edit(Publisher $page): Renderable
    {
        return view('aboleon.publisher::pages.editor')->with([
            'page' => $page->load('configs.nodes', 'content.translated'),
            'config' => $page->configs,
            'current_locale' => $this->locale()
        ]);
    }

    public function update(Publisher $page): RedirectResponse
    {

     //  de(request()->input());

        // try {

        Meta::make($page);
        Publisher::updateElements($page);

        $this->responseSuccess("La page a été mise à jour.");
        $this->redirect_to = route('aboleon.publisher.pages.edit', $page->id);
        /*      } catch (Throwable $e) {
                  $this->responseException($e);
              } finally {
                  return $this->sendResponse();
              } */
        return $this->sendResponse();
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $page = Publisher::withTrashed()->findOrFail($id);
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
            'type' => 'required',
            'elements' => 'required'
        ];

        $this->validation_messages = [
            'title.required' => trans('aboleon.framework::validations.required', ['param' => 'Le titre']),
            'type.required' => trans('aboleon.framework::validations.required', ['param' => 'Le type de contenu']),
            'elements.required' => trans('aboleon.framework::validations.required', ['param' => 'La configuration du contenu'])
        ];

        $this->validation();
    }
}
