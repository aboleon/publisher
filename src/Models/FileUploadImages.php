<?php

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Interfaces\FileUploadImageInterface;
use Aboleon\Framework\Traits\Locale;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class FileUploadImages extends \Aboleon\Framework\Models\FileUploadImages implements FileUploadImageInterface
{
    use Locale;

    private Nodes $node;

    public function __construct()
    {
        parent::__construct();
        $this->path = request('aboleon_accesskey') . '/';
    }

    public function crop()
    {
        $file = request('object_id');
        $size = request('size');
        $id = request('page_id');
        if (!$file) {
            return abort(404, trans('errors.no_image_to_crop'));
        }
        if ($file == 'meta') {
            $case = 'meta';
            $data = 'media/meta/' . request('page_id') . '/' . request('size') . ".jpg";
        } else {
            $data = self::whereId($file)->with('config')->first();
            $case = 'content';
        }
        return view('kvasir.cms.images.cropper')->with([
            'data' => $data,
            'case' => $case,
            'size' => $size,
            'id' => $id
        ]);
    }

    public function setWidthHeight(string $dimensions = null): array
    {
        if ($dimensions) {
            $params = explode(';', rtrim($dimensions, ';'));
            $node_dims = [];
            foreach ($params as $dims) {
                $dim = explode(',', $dims);
                $node_dims[] = [
                    'width' => current($dim),
                    'height' => end($dim)
                ];
            }
            $this->dims = $node_dims;
        }
        return $this->dims;
    }

    public function processUpload()
    {
        //de(request());

        $file = current(request()->file('files'));

        if (strstr($file->getMimeType(), '/', true) != 'image') {
            $this->responseAbort(trans('errors.mustBeImage'));

        }

        if ($this->canContinue()) {


            $this->image = Image::make($file);

            if (strstr(request('identifier'), 'node_')) {
                $this->processImage();
            }
            if (request('identifier') == 'meta') {
                $this->processMeta();
            }

        }
    }

    public function processCrop()
    {
        if (request('identifier') == 'meta') {
            $file = $this->path . request('object_id') . '/' . request('size') . '.jpg';
            $this->image = Storage::disk('publisher')->get($file);
            if ($this->image) {
                $this->image = Image::make($this->image);
                if (Storage::disk('publisher')->put($file,
                    $this->image->crop((int)request('wimage'), (int)request('himage'), (int)request('x1image'), (int)request('y1image'))->resize(request('wiimage'), request('heimage'), function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream('jpg', 80))) {
                    $this->response['callback'] = 'croppedHome';
                    $this->response['size'] = request('size');
                    $this->response['h'] = $this->image->height();
                    $this->response['image'] = Storage::disk('publisher')->url($this->path . request('size') . '.jpg?' . time());
                }
            }
        } else {
            $file = self::findOrFail(request('object_id'));
            $this->image = Storage::disk('publisher')->get($this->path . $file->filename);
            $filename = Str::random() . '.jpg';
            if ($this->image) {
                $this->image = Image::make($this->image);
                if (Storage::disk('publisher')->put($this->path . $filename,
                    $this->image->crop((int)request('wimage'), (int)request('himage'), (int)request('x1image'), (int)request('y1image'))->resize(request('wiimage'), request('heimage'), function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream('jpg', 80))) {
                    $w = $this->image->width();
                    $h = $this->image->height();
                    Storage::disk('publisher')->put(
                        $this->path . 'th_' . $filename,
                        $this->image->resize(null, 80, function ($constraint) {
                            $constraint->aspectRatio();
                        })->stream('jpg'));
                    Storage::disk('publisher')->delete([$this->path . $file->filename, $this->path . 'th_' . $file->filename]);
                    $file->w = $w;
                    $file->h = $h;
                    $file->filename = $filename;
                    $file->save();
                }
                $this->response['callback'] = 'cropped';
                $this->response['filename'] = $filename;
            }
        }
    }

    public function processDelete()
    {

        /*
                                case 'delete' :
                                    if (!is_numeric(request('object_id'))) {
                                        $dimensions = $this->dim['meta'];
                                        $page = explode('-', request('object_id'));
                                        $files = [];
                                        $path = 'projects/' . config('app.project') . '/' . current($page) . '/' . end($page) . '/';
                                        foreach ($dimensions as $k => $v) {
                                            $files[] = $path . $k . '.jpg';
                                        }
                                        Storage::disk('publisher')->delete($files);
                                    } else {
                                        $this->image = self::findOrFail(request('object_id'));
                                        $path = 'projects/' . config('app.project') . '/' . $this->image->elements_identifier . '/';
                                        Storage::disk('publisher')->delete([$path . $this->image->filename, $path . 'th_' . $this->image->filename]);
                                        $this->remove();
                                        if (self::where('elements_identifier', $this->image->elements_identifier)->count() < 1) {
                                            Storage::disk('publisher')->deleteDirectory($path);
                                        }
                                    }
                                    break;*/

    }

    private function processImage()
    {
        $this->mime_type = (str_replace('image/', '', $this->image->mime()) == 'png' ? 'png' : 'jpg');
        $ratio = ($this->image->width() / $this->image->height()) > 1 ? 'h' : 'v';

        $node_id = (int)str_replace('node_', '', request('identifier'));
        $this->node = Nodes::find($node_id);
        $this->setWidthHeight($this->node->params['dim']);

        $this->response['ratio'] = $ratio;

        $array = [];
        $descriptions = [];

        foreach ($this->dims as $k => $v) {

            $file = $this->path . $v['width'] . '_' . $this->random_filename . '.' . $this->mime_type;

            Storage::disk('publisher')->put($file,
                $this->image->resize($v['width'], $v['height'], function ($constraint) {
                    $constraint->aspectRatio();
                })->stream($this->mime_type, 80));

            $array[] = [
                'url' => Storage::disk('publisher')->url($file . '?' . time()),
                'width' => $v['width'],
                'height' => $v['height']
            ];
        }
        foreach ($this->locales() as $locale) {
            $descriptions[] = new MediaDescription(['locale' => $locale, 'content' => request('description')[$locale]]);
        }
        $this->node->media()->save(new Media(['content' => $this->random_filename . '.' . $this->mime_type]))->description()->saveMany($descriptions);
        $this->response['uploadedImage'] = $array;
    }

    private function processMeta()
    {
        $this->image->encode('jpg');

        $config = Configs::find(request('config_id'));
        $this->setWidthHeight($config['configs']['meta']['img']);

        $array = [];
        foreach ($this->dims as $k => $v) {

            $file = $this->path . 'meta_' . $v['width'] . '.jpg';

            Storage::disk('publisher')->put($file,
                $this->image->resize($v['width'], $v['height'], function ($constraint) {
                    $constraint->aspectRatio();
                })->stream('jpg', 80));

            $array[] = [
                'url' => Storage::disk('publisher')->url($file . '?' . time()),
                'width' => $v['width'],
                'height' => $v['height']
            ];
        }
        $this->response['uploadedImage'] = $array;
    }

}