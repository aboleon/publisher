<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Models\Accesskeys;
use Aboleon\Framework\Traits\{
    Locale,
    Responses
};
use Aboleon\Publisher\Repositories\Tables;
use Illuminate\Database\Eloquent\{
    Model,
    Relations\BelongsTo,
    Relations\HasMany,
    Relations\HasOne,
    Relations\MorphOne,
    SoftDeletes,
    Builder
};

class Publisher extends Model
{
    use Locale;
    use Responses;
    use SoftDeletes;


    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = Tables::fetch('publisher');
    }

    public function scopeOfType($query, ?Configs $type): Builder
    {
        if ($type) {
            $query->where('type', $type->id);
        }
        return $query;
    }

    public static function updateElements(Publisher $page)
    {
        if (request('elements')) {
            foreach (request('elements') as $elements) {

                foreach ($elements['children'] as $key => $content) {
                    $node = $page->content()->where('node_id', $key)->first();
                    if (is_array($content)) {
                        if (isset($content['arrayable'])) {

                            $page->content()->where('node_id', $key)->delete();
                            if (isset($content['values'])) {
                                $data = [];
                                foreach ($content['values'] as $value) {
                                    $data[] = new Content([
                                        'node_id' => $key,
                                        'content' => $value
                                    ]);
                                }
                                $page->content()->saveMany($data);
                            }
                        } else {

                            foreach ($page->locales() as $locale) {
                                $node
                                    ? $node->translated()->where('locale', $locale)->update(['content' => $content[$locale]])
                                    : $page->content()->save(new Content(['node_id' => $key]))->translated()->save(new ContentTranslated([
                                    'content' => $content[$locale],
                                    'locale' => $locale
                                ]));
                            }
                        }
                    } else {

                        $node
                            ? $node->update(['content' => $content])
                            : $page->content()->save(new Content([
                            'node_id' => $key,
                            'content' => $content
                        ]));

                    }

                }
            }
            // $this->pushMessages(Configs::generateView($page));
        }
    }

    public function configs(): BelongsTo
    {
        return $this->belongsTo(Configs::class, 'type');
    }

    public function content(): HasMany
    {
        return $this->hasMany(Content::class, 'pages_id')->with('translated');
    }

    public function meta(): HasOne
    {
        return $this->hasOne(Meta::class, 'publisher_id');
    }

    public function accesskey(): MorphOne
    {
        return $this->morphOne(Accesskeys::class, 'accessible');
    }

    public function key()
    {
        return $this->accesskey->access_key;
    }
}
