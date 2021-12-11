<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Facades\Helpers;
use Aboleon\Framework\Traits\Locale;

class CustomContent extends \Illuminate\Database\Eloquent\Model
{

    public $publisher_page;
    protected $config = [];
    protected $table = 'publisher_custom_content';
    protected $guarded = [];
    private $record;

    use Locale;

    public function __construct()
    {
        $this->timestamps = false;
    }

    public function lang(): object
    {
        return $this->hasOne(CustomContentData::class)->where('lg', app()->getLocale());
    }

    public function langs(): object
    {
        return $this->hasMany(CustomContentData::class);
    }

    public function processCustomContent(Pages $page)
    {
        $customContent = new self();
        $customContent->publisher_page = $page;
        self::where('pages_id', $customContent->publisher_page->id)->delete();

        # Replicated custom content
        # ---------------------
        $customContent->updateReplicatedContent();

        # Custom simple content
        # ---------------------
        $customContent->updateContent();

        # Custom multilanguage content
        # ---------------------
        $customContent->updateMultiLanguageContent();
    }

    public function updateReplicatedContent(): void
    {

        $input = request()->input();
        if (!array_key_exists('replica_content', $input)) {
            return;
        }

        $this->replicateGroups($input);

        if (!empty($input['replica_content'])) {
            $data = [];
            foreach ($input['replica_content'] as $item) {
                foreach ($item as $key_item => $array) {
                    foreach ($array as $value) {
                        $data[] = ['field' => $key_item, 'pages_id' => $this->publisher_page->id, 'value' => $value];
                    }
                }
            }
            CustomContent::insert($data);
        }
    }

    public function updateContent(): void
    {
        $this->config = (array)config('project.content.' . $this->publisher_page->type);

        if (!request()->filled('custom_content')) {
            return;
        }
        foreach (request()->custom_content as $key => $value) {
            $this->insertArray($key, $value);
            $this->insertSingle($key, $value);
        }
    }

    private function updateMultiLanguageContent(): void
    {
        if (!request()->filled('multilang_custom_content')) {
            return;
        }
        foreach (request()->multilang_custom_content as $key => $value) {
            $this->insertSingle($key, 'multilang');
            foreach ($this->locales() as $locale) {
                if (!empty($value[$locale])) {
                    CustomContentData::insert([
                        'custom_content_id' => $this->record->id,
                        'lg' => $locale,
                        'content' => $value[$locale]
                    ]);
                }
            }
        }
    }

    public function page(): object
    {
        return $this->belongsTo(Pages::class, 'pages_id');
    }

    public function content(): object
    {
        return $this->hasOne(CustomContentData::class, 'custom_content_id')->where('lg', app()->getLocale());
    }


    private function insertArray($key, $value): void
    {
        if (is_array($value)) {
            $insert = [];
            foreach ($value as $item) {
                $insert[] = [
                    'field' => $key,
                    'pages_id' => request()->id,
                    'value' => $item
                ];
            }
            CustomContent::insert($insert);
        }
    }

    private function insertSingle($key, $value): void
    {
        if (!is_array($value) && !empty($value)) {
            $this->record = new self();
            $this->record->field = $key;
            $this->record->pages_id = request()->id;
            $value = $this->castAsInteger($this->config, $key, $value);
            $this->record->value = $value;
            $this->record->save();
        }
    }

    private function castAsInteger($config, $key, $value)
    {
        $configs = current(array_filter($config['custom_content'], function ($item) use ($key) {
            return array_key_exists('fields', $item) && array_key_exists($key, $item['fields']);
        }));

        if ($config && $configs && array_key_exists('cast', $configs['fields'][$key]) && $configs['fields'][$key]['cast'] == 'integer') {
            $value = floatval(str_replace(',', '.', $value)) * 100;
        }

        return $value;
    }

    private function replicateGroups(array $input): void
    {
        if (array_key_exists('replicate_group', $input)) {
            $data = [];
            foreach ($input['replicate_group'] as $replicate_group) {
                $data[] = [
                    'field' => 'replicate_group',
                    'value' => $replicate_group,
                    'pages_id' => $this->publisher_page->id
                ];

                for ($i = 0; $i < count($input['replicate_' . $replicate_group]); ++$i) {
                    $code = $input['replicate_' . $replicate_group][$i];
                    $data[] = [
                        'field' => $replicate_group,
                        'value' => $code,
                        'pages_id' => $this->publisher_page->id
                    ];

                    foreach ($input['replica_content'][$replicate_group] as $key => $item) {

                        $data[] = [
                            'field' => $key . '_' . $code,
                            'value' => $input['replica_content'][$replicate_group][$key][$i],
                            'pages_id' => $this->publisher_page->id
                        ];
                    }

                }
                unset($input['replica_content'][$replicate_group]);
            }

            CustomContent::insert($data);
        }
    }
}
