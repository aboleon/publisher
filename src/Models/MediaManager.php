<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use File;
use Image;
use Aboleon\Publisher\Traits\Media;
use Aboleon\Framework\Traits\{
    Helper,
    Responses};

class MediaManager extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'publisher_media_content';
    protected $editable;

    use Responses;
    use Helper {
        Helper::__construct as private Helper__construct;
    }
    use Media {
        Media::__construct as private Media__construct;
    }

    public function __construct()
    {
        parent::__construct();
        $this->Helper__construct();
        $this->Media__construct();
    }

    public function remove()
    {
        $media_id = request()->isMethod('get') ? $this->object_id : request()->object_id;

        $this->editable = self::find($media_id);

        if (!is_null($this->editable)) {
            if (method_exists($this, 'remove_' . $this->editable->type)) {
                $this->{'remove_' . $this->editable->type}();
            }

            $this->response['id'] = $this->editable->id;
            $this->response['callback'] = request()->callback;
            $this->editable->delete();

        } else {
            $this->responseError("Erreur : l'élément à supprimer n'est pas trouvable");
        }

        if (request()->isMethod('get')) {
            return redirect()->back();
        }

        return $this->response;
    }

    protected function remove_image(): void
    {
        $this->deleteImages();
    }

    protected function remove_document(): void
    {
        request()->merge([
            'page_id' => $this->editable->pages_id
        ]);
        $key = $this->getAccessKey(Pages::find($this->editable->pages_id));
        $this->response['page_id'] = $this->editable->pages_id;
        $this->response['accessKey'] = $key;
        $this->response['path_delete'] = $this->uploadPath . $key . '/documents/' . $this->editable->content;
        File::delete($this->uploadPath . $key . '/documents/' . $this->editable->content);
    }

    protected function deleteImages(): void
    {
        $page = Pages::find($this->editable->pages_id);
        File::delete(File::glob($this->uploadPath . $this->getAccessKeyWithSeparator($page) . 'images' . DIRECTORY_SEPARATOR . '*' .
            str_replace('.jpg', '*', $this->editable->content)));
    }

    public function description(string $lang = ''): object
    {
        if (empty($lang)) {
            $lang = app()->getLocale();
        }

        return $this->descriptions()->where('lg', $lang);
    }

    public function descriptions(): object
    {
        return $this->hasMany(MediaDescription::class, 'media_content_id');
    }

    public function http(string $resource): string
    {
        return asset('upload/' . $resource);
    }

    public function getAccessKey(Pages $page)
    {
        $this->accessKey($page);
        return $page->access_key;
    }

    public function getAccessKeyWithSeparator(Pages $page, $separator = null)
    {
        $this->accessKey($page);
        return $this->withAccessKey($separator);
    }

    public function removeAttachedMedia(Pages $page)
    {
        if (is_null($page)) {
            return;
        }

        $media = self::where('pages_id', $page->id)->get();

        $images = $media->filter(function ($val) {
            return $val->type == 'image';
        });

        $documents = $media->filter(function ($val) {
            return $val->type == 'document';
        });

        $accessKey = $this->getAccessKeyWithSeparator($page);

        if (!$images->isEmpty()) {
            foreach ($images as $image) {
                File::delete(File::glob($this->uploadPath . '/' . $accessKey . 'images' . DIRECTORY_SEPARATOR . '*' . str_replace
                    (['.jpg', '.png'], '',
                        $image->content) . '*'));
            }
        }

        if (!$documents->isEmpty()) {
            foreach ($documents as $document) {
                File::delete(File::glob($this->uploadPath . '/' . $accessKey . 'documents/' . $accessKey . '*' . str_replace(['.pdf'], '', $document->content) . '*'));
            }
        }

        if (str_replace('/', '', $accessKey) != '') {
            File::deleteDirectory($this->uploadPath . '/' . $accessKey);
        }

    }

    public function sortable()
    {
        $positions = json_decode(request()->positions);
        if ($positions) {
            foreach ($positions as $pos) {
                static::where('id', $pos->key)->update(['position' => $pos->position]);
            }
        }
        $this->responseSuccess("L'ordre est mis à jour");
        return $this->response;
    }

    protected function editDescription()
    {
        MediaDescription::where(['media_content_id' => request()->id, 'lg' => request()->lg])->update(['description' => request()->text]);
        $this->response['callback'] = 'zone_edit_remove';
        $this->responseSuccess("Mise à jour effectuée");

        return $this->response;
    }

    public static function retina($img)
    {
        return is_file(public_path('upload/images/' . str_replace('.', '@2x.', $img))) ? asset('upload/images/' . str_replace('.', '@2x.', $img)) : null;
    }

    public function scopeVarname($query, $varname = null)
    {

        if (!is_null($varname)) {
            return $query->where('varname', $varname);
        }
    }

}
