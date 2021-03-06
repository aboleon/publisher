<?php declare(strict_types=1);

namespace Aboleon\Publisher\Http\Controllers;

use Aboleon\Framework\Models\Accesskeys;
use Aboleon\Framework\Traits\Locale;
use Aboleon\Framework\Traits\Responses;
use Aboleon\Framework\Traits\Validation;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Aboleon\Publisher\Models\{
    Configs,
    Content,
    Lists,
    ListsTranslated,
    Meta,
    Publisher
};

use Throwable;

class PublisherController extends Controller
{

    use Locale;
    use Responses;
    use Validation;

    public function index(?string $oftype = null): Renderable
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
            'author_id' => auth()->id()
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
            'page' => $page->load('configs.nodes', 'content'),
            'config' => $page->configs,
            'current_locale' => $this->locale()
        ]);
    }


    public function editable(string $type): Renderable
    {
        $page = Publisher::where('type', Configs::where('type', $type)->value('id'))->first();

        if (!$page) {
            return view('aboleon.publisher::pages.404');
        }

        return view('aboleon.publisher::pages.editor')->with([
            'page' => $page->load('configs.nodes', 'content'),
            'config' => $page->configs,
            'current_locale' => $this->locale()
        ]);
    }

    public function update(Publisher $page): RedirectResponse
    {
        // try {
        Meta::make($page);
        Publisher::updateElements($page);

        $this->responseSuccess("La page a ??t?? mise ?? jour.");
        $this->redirect_to = route('aboleon.publisher.pages.edit', $page->id);
        /*      } catch (Throwable $e) {
                  $this->responseException($e);
              } finally {
                  return $this->sendResponse();
              } */
        return $this->sendResponse();
    }

    public function destroy(Publisher $page): RedirectResponse
    {
        try {
            if ($page->forceDelete) {
                //Media::removeAttachedMedia($page);
                // $page->removeChildren
                $page->forceDelete();
                $this->responseNotice("Le contenu a ??t?? d??finitivement supprim??.");
            } else {
                $page->delete();
                $this->responseNotice("Le contenu a ??t?? plac?? dans la corbeille.");
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
