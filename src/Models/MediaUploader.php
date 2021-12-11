<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use File, Image, Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use \Aboleon\Framework\Traits\Responses;
use Project;

class MediaUploader extends Model
{

    protected $randomName;
    protected $jcrop_selection_ratio = 1.5;
    protected $retina_factor = 2;
    protected $x;
    private $img_object;
    private $mime_type;
    private $width;
    private $height;
    private $prefix;
    private $resize_by_w;
    private $resize_by_h;
    private $varname;

    use Responses;
    use \Aboleon\Publisher\Traits\Media {
        \Aboleon\Publisher\Traits\Media::__construct as private Media__construct;
    }

    public function __construct()
    {
        parent::__construct();
        $this->Media__construct();

        ini_set('memory_limit', '256M');
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '10M');

        $this->randomName = Str::random(6);
    }

    public function newUploadedFile($filename)
    {
        $this->uploaded_file = request()->file($filename);
        $this->uploaded_filename = $filename;
    }

    public function upload()
    {
        $page = Pages::find(request()->page_id);
        $this->to_page_id = $page->id;
        $this->varname = 'fileupload';


        if (request()->filled('uploadable_type')) {

            $this->uploaded_type = $this->response['uploadable_type'] = request()->uploadable_type;

            if (method_exists(self::class, request()->uploadable_type)) {

                $this->accessKey($page);
                if (request()->filled('uploadable_config')) {
                    $this->uploaded_file_config = config('project.content.' . request()->uploadable_config);
                }

                $this->{request()->uploadable_type}();
            }

        } else {
            $this->responseError("Type de media inconnu");
        }
        return $this->response;
    }

    public function staticUpload(Pages $page)
    {
        $this->accessKey($page);
        $this->to_page_id = $page->id;

        if (strstr($this->uploaded_filename, 'image')) {
            $this->uploaded_file_config = config('project.content.' . $page->type . '.images.' . $this->uploaded_filename);
            $this->uploaded_type = 'image';
            $this->varname = $this->uploaded_filename;
            $this->image();
        }
    }

    protected function image()
    {
        if (request()->has('files')) {
            $this->uploaded_file = request()->file('files')[0];
        }
        $this->checkMakeDir($this->withAccessKey() . 'images');
        $this->uploadable_type = 'image';

        if (!is_null($this->uploaded_file) && !request()->filled($this->uploaded_filename . '_upload_errors')) {

            $default_config = ['size' => ['w' => 1920, 'h' => null]];

            if (!$this->uploaded_file_config) {
                $this->uploaded_file_config = $default_config;
            }

            $this->prefix = request()->filled($this->uploaded_filename . '_jcrop') ? 'jcrop_' : null;

            $this->setWidthHeight();

            if (in_array('selection', $this->uploaded_file_config)) {
                $this->width *= $this->jcrop_selection_ratio;
                $this->height *= $this->jcrop_selection_ratio;
            }

            $this->img_object = Image::make($this->uploaded_file);
            $this->mime_type = (str_replace('image/', '', $this->img_object->mime()) == 'png' ? 'png' : 'jpg');

            $this->resize_by_w = $this->width;
            $this->resize_by_h = $this->height;

            // Jcrop preparation
            $this->jcropPrepare();

            // Division by zero problem
            if ($this->resize_by_w == 0) {
                $this->resize_by_w = null;
            }
            if ($this->resize_by_h == 0) {
                $this->resize_by_h = null;
            }

            $this->img_object->encode('jpg');
            if (!in_array('no_resize', $this->uploaded_file_config)) {
                $this->img_object->resize($this->resize_by_w, $this->resize_by_h, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $this->processSingleSizeImage();

            if (is_null($this->prefix))
                $this->processSizes();
        }

        // Thumb
        $this->generateThumbnail();
        $this->insertRecord();

        $this->editable->content = $this->prefix . $this->randomName . '.' . $this->mime_type;
        $this->editable->save();

        $this->response['uploaded_image_thumb'] = Project::media($this->withAccessKey() . 'images/th_' .
            $this->prefix
            . $this->randomName . '.' . $this->mime_type);
        $this->response['uploaded_image'] = Project::media($this->withAccessKey() . 'images/' . $this->prefix .
            $this->randomName . '.' . $this->mime_type);

    }

    protected function document(): void
    {
        $this->uploadable_type = 'document';

        if (!is_null($this->uploaded_file)) {
            $path = $this->uploaded_file->store($this->withAccessKey() . 'documents', 'public');

            $this->insertRecord();

            $extension = explode('/', $path);
            $this->editable->content = end($extension);
            $this->editable->save();

            $newfilename = explode('/', $path);

            $this->response['path'] = $path;
            $this->response['newfilename'] = end($newfilename);
            $this->response['http'] = Project::media($path);
        }
    }

    protected function video(): void
    {
        $this->insertRecord();
        $this->editable->content = request('content');
        $this->editable->save();
        $this->response['http'] = request('content');
        $this->response['callback'] = request()->callback;
    }

    protected function insertRecord(): void
    {
        $this->editable = new MediaManager();
        $this->editable->pages_id = $this->to_page_id;
        $this->editable->type = $this->uploaded_type;
        $this->editable->varname = $this->varname;
        $this->editable->save();

        if (request()->filled('description')) {
            $description = [];
            parse_str(request()->description, $description);
            MediaDescription::setMediaDescription($this->editable, current($description));
        }

        $this->response['uploaded_id'] = $this->editable->id;
    }

    private function generateThumbnail()
    {
        if (!is_object($this->img_object)) {
            return;
        }

        $this->img_object->encode('jpg')->resize(null, 110, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($this->uploadPath . '/' . $this->withAccessKey() . 'images/th_' . $this->prefix .
            $this->randomName .
            '.' . $this->mime_type, 75);
    }

    public function jcrop(): void
    {
        if (!request()->filled('media_id')) {
            return;
        }

        $image = Media::find(request()->media_id);

        if (is_null($image)) {
            return;
        }

        $media_folder = Media::getAccessKeyWithSeparator(Pages::find($image->pages_id));
        $clean_name = str_replace('jcrop_', '', $image->content);
        $composants = explode('.', $clean_name);

        $this->img_object = Image::make($this->uploadPath . '/' . $media_folder . 'images/' . $image->content)->crop(
            (int)request()->{$image->varname . '_wimage'},
            (int)request()->{$image->varname . '_himage'},
            (int)request()->{$image->varname . '_x1image'},
            (int)request()->{$image->varname . '_y1image'}
        )->resize(
            request()->{$image->varname . '_wiimage'} * $this->retina_factor,
            request()->{$image->varname . '_heimage'} * $this->retina_factor,
            function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        );

        if (!is_null(Project::watermark())) {
            $this->img_object->insert(Project::watermark(), 'bottom-right', 20, 20);
        }

        if ($this->img_object->save($this->uploadPath . '/' . $media_folder . 'images/' . $composants[0] . '@2x.' .
            $composants[1], 75)) {

            $img2 = Image::make($this->uploadPath . '/' . $media_folder . 'images/' . $image->content)->crop(
                (int)request()->{$image->varname . '_wimage'},
                (int)request()->{$image->varname . '_himage'},
                (int)request()->{$image->varname . '_x1image'},
                (int)request()->{$image->varname . '_y1image'}
            )->resize(
                request()->{$image->varname . '_wiimage'},
                request()->{$image->varname . '_heimage'},
                function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );

            if (!is_null(Project::watermark())) {
                $img2->insert(Project::watermark(), 'bottom-right', 20, 20);
            }

            $img2->save($this->uploadPath . '/' . $media_folder . 'images/' . $clean_name, 75);

            Image::make($this->uploadPath . '/' . $media_folder . 'images/' . $clean_name)->resize(null, 110, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($this->uploadPath . '/' . $media_folder . 'images/th_' . $clean_name, 80);

            File::delete(File::glob($this->uploadPath . '/' . $media_folder . 'images/*' . str_replace(['.jpg', '.png'], '', $image->content) . '*'));
        }

        $image->content = $clean_name;
        $image->save();
    }

    private function jcropPrepare()
    {
        if (!is_null($this->prefix)) {

            $original_width = $this->img_object->width();
            $original_height = $this->img_object->height();
            $ratio = $original_width / $original_height;

            $resized_height = floor($this->width / $ratio);

            $this->resize_by_w = null;
            $this->resize_by_h = null;

            if ($resized_height < $this->height) {
                $this->resize_by_h = $this->height;
            } else {
                $this->resize_by_w = $this->width;
            }

            // Bypass Jcrop if same size image
            if (($original_width == $this->width) && ($original_height == $this->height)) {
                $this->prefix = null;
            }
        }
    }

    private function processSingleSizeImage()
    {
        if (!array_key_exists('sizes', $this->uploaded_file_config)) {
            /* Image retina */

            $this->img_object->encode('jpg')->save($this->uploadPath . '/' . $this->withAccessKey() . 'images/' .
                $this->prefix .
                $this->randomName . '@2x.' . $this->mime_type, 75);

            if ($this->resize_by_w) {
                $this->resize_by_w /= $this->retina_factor;
            }
            if ($this->resize_by_h) {
                $this->resize_by_h /= $this->retina_factor;
            }

            if (!is_null(Project::watermark())) {
                $this->img_object->insert(Project::watermark(), 'bottom-right', 20, 20);
            }

            /* Image standard */
            $this->img_object->resize($this->resize_by_w, $this->resize_by_h, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($this->uploadPath . '/' . $this->withAccessKey() . 'images/' . $this->prefix .
                $this->randomName
                . '.' . $this->mime_type, 75);
        }
    }

    private function processSizes()
    {
        if (array_key_exists('sizes', $this->uploaded_file_config)) {

            foreach ($this->uploaded_file_config['sizes'] as $isize) {

                $retina_w = !is_null($isize['w']) ? $isize['w'] * $this->retina_factor : null;
                $retina_h = !is_null($isize['h']) ? $isize['h'] * $this->retina_factor : null;

                $this->img_object->encode($this->mime_type)->resize($retina_w, $retina_h, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                if (!is_null(Project::watermark())) {
                    $this->img_object->insert(Project::watermark(), 'bottom-right', 20, 20);
                }

                $this->img_object->save($this->uploadPath . '/' . $this->withAccessKey() . 'images/' .
                    $this->randomName
                    . '-' . $isize['label'] . '@2x.' . $this->mime_type, 75);

                $this->img_object->encode($this->mime_type)->resize($isize['w'], $isize['h'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $this->img_object->save($this->uploadPath . '/' . $this->withAccessKey() . 'images/' .
                    $this->randomName
                    . '-' . $isize['label'] . '.' . $this->mime_type, 75);
            }
        }
    }

    private function setWidthHeight()
    {
        if (array_key_exists('sizes', $this->uploaded_file_config)) {
            $values = collect($this->uploaded_file_config['sizes'])->sortByDesc('w')->first();
            $this->width = (int)$values['w'] * $this->retina_factor;
            $this->height = (int)$values['h'] * $this->retina_factor;
            return $this;
        }
        if (array_key_exists('fields', $this->uploaded_file_config)) {
            $this->width = (int)$this->uploaded_file_config['fields']['image']['size']['w'] * $this->retina_factor;
            $this->height = (int)$this->uploaded_file_config['fields']['image']['size']['h'] * $this->retina_factor;
            return $this;
        }

        $this->width = (int)$this->uploaded_file_config['size']['w'] * $this->retina_factor;
        $this->height = (int)$this->uploaded_file_config['size']['h'] * $this->retina_factor;
    }
}
