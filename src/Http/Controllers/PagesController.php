<?php declare(strict_types=1);

namespace Aboleon\Publisher\Http\Controllers;

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
    Pages,
    PagesCreateContent,
    PagesEditContent};

use Throwable;

class PagesController extends \Aboleon\Publisher\Http\Controllers\Controller
{
    private $type;
    private $editable;

    use Locale;
    use Responses;
    use Validation;

    public function index(): Renderable
    {
        return view('aboleon.publisher::pages.index')->with('pages', Pages::orderBy('title')->paginate(25));
    }

    public function create(Configs $launchpad): Renderable
    {
        return view('aboleon.publisher::pages.create')->with([
            'config' => $launchpad->load('nodes.children')
        ]);
    }

    public function show(Publisher $page)
    {
        $page->configs->generateView($page);
    }

    public function edit(Publisher $page): Renderable
    {
        /*
        d($page->configs->load('nodes'));
        d(collect($page->configs->nodes)->pluck('children.*.id')->flatten());

        exit; */
        //de( collect(Content::whereIn('node_id', $page->configs->fetchNodes())->with('translated')->get()));
        return view('aboleon.publisher::pages.edit')->with([
            'page' => $page->load('configs.nodes','content.translated'),
            'config' => $page->configs
        ]);
    }

    public function update(Pages $page): RedirectResponse
    {
        //  de(request());

        try {
            if (request('elements')) {
                foreach (request('elements') as $section) {
                    foreach ($section['elements'] as $key => $content) {
                        foreach ($this->locales() as $locale) {
                            Content::where([
                                'node_id' => (string)$key,
                                'locale' => $locale
                            ])->update(['content' => $content[$locale]]);
                        }
                    }
                }
            }
            $this->responseSuccess("La page a été mise à jour.");
            $this->redirect_to = route('aboleon.publisher.pages.edit', $page->id);
        } catch (Throwable $e) {
            $this->responseException($e);
        } finally {
            return $this->sendResponse();
        }
    }

    public function store(): RedirectResponse
    {
        //  $this->requestValidation();

        d(request()->input());
     //   try {

            $access_key = Pages::generateAccessKey();

            $page = Pages::create([
                'type' => (int)request('type'),
                'access_key' => $access_key
            ]);

            if (request('elements')) {
                foreach (request('elements') as $section => $elements) {

                    foreach ($elements['children'] as $key => $content) {
                        if (is_array($content)) {
                          //  de($content);
                            foreach ($this->locales() as $locale) {
                                $page->content()->save(new Content([
                                    'node_id' => $key
                                ]))->translated()->save(new ContentTranslated([
                                    'content' => $content[$locale],
                                    'locale' => $locale
                                ]));
                            }
                        } else {
                            $page->content()->save(new Content([
                                'node_id' => $key,
                                'content' => $content
                            ]));
                        }

                    }
                }

                $this->responseSuccess("La page a été créée.");
               // $this->pushMessages(Configs::generateView($page));
            }

           echo $this->redirect_to = route('aboleon.publisher.pages.edit', $page->id);
exit;
  /*      } catch (Throwable $e) {
            $this->responseException($e);
        } finally {
            return $this->sendResponse();
        } */

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
