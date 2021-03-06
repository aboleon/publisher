<?php

namespace Aboleon\Publisher\Http\Controllers;

use Aboleon\Publisher\Models\{
    FileUploadImages,
    Configs,
    Lists,
    Publisher};
use Aboleon\Framework\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AjaxController
{
    use Responses;

    public function distribute(Request $request)
    {
        $this->responseElement('input', request()->input());

        if (!$request->filled('action')) {
            $this->responseError('Cette requête ne peut pas être interprêtée.');
            return response()->json($this->response, 400);
        }

        if (!method_exists(self::class, $request->action)) {
            $this->responseError('Cette requête ne peut pas être traitée.');
            return response()->json($this->response, 405);
        }

        return $this->{$request->action}($request);
    }

    private function LaunchpadCreateList(): JsonResponse
    {
        if (!request()->filled('create_list_title') or !request()->filled('create_list_type')) {
            $this->responseAbort("Tous les champs ne sont pas remplis.");
        }
        if ($this->canContinue()) {

            $config = Configs::create([
                'title' => request('create_list_title'),
                'type' => request('create_list_type'),
                'group' => 'lists'
            ]);
            $this->responseElement('created_list', $config->toArray());
            $this->responseElement('callback', 'organizer_appendCreatedList');
            $this->responseSuccess("La liste a été créé.");
        }
        return response()->json($this->response);
    }

    protected function AddEntryToList(): array
    {
        if (!request()->filled('entry')) {
            $this->responseAbort("Veuillez indiquer un intitulé.");
        }
        if ($this->canContinue()) {
            try {
                $el = Lists::create([
                    'list_id' => request('list_id'),
                    'parent' => request('parent')
                ]);
                $el->setTranslation('content', app()->getLocale(), request('entry'))->save();

                $this->response['last_id'] = $el->id;
                $this->response['parent'] = request('parent');
                $this->response['callback'] = 'listables_callback';
                $this->response['selectable'] = request('selectable');
            } catch(\Throwable $e) {
                $this->responseException($e);
            }
        }
        return $this->response;

    }
    protected function editListableItem(): array
    {
        if (!request()->filled('name')) {
            $this->responseAbort("Veuillez saisir un intitulé.");
        }
        if (!request()->filled('id')) {
            $this->responseAbort("L'id de la catégorie est absent.");
        }
        if ($this->canContinue()) {
            try {
                $el = Lists::find(request('id'));
                $el->setTranslation('content', app()->getLocale(), request('name'))->save();
                $this->response['callback'] = 'editListableItem';
            } catch(\Throwable $e) {
                $this->responseException($e);
            }
        }
        return $this->response;

    }


    protected function ajaxFileUploads($request): array
    {
        return (new FileUploadImages())->ajax($request)->fetchResponse();
    }

    protected function publishedStatus(): array
    {
        Publisher::where('id',request('id'))->update(['published' => (request('published') == 'true' ? 1 : null)]);
        $this->responseNotice(trans('aboleon.framework::ui.statusChange', [
            'status' => trans('aboleon.framework::ui.' . (request()->published == 'true' ? 'online' : 'offline'))
        ]));
        //Artisan::call('cache:clear');
        return $this->response;
    }


}
