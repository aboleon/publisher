<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Publisher\Repositories\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nodes extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent',
        'config_id',
        'title',
        'position',
        'type',
        'params'
    ];
    protected $casts = [
        'params' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Tables::fetch('nodes');
        $this->timestamps = false;
    }

    public static function makeSet(Configs $config, $section)
    {
        $elements = $section['elements'] ?? [];
        $nodes = [];
        unset($section['elements'],$section['is_deleted']);

        $new_section = $config->nodes()->save(new Nodes($section));
        if ($elements) {
            foreach ($elements as $element) {
                unset($element['is_deleted']);
                $nodes[] = new Nodes($element);
            }
            $new_section->children()->saveMany($nodes);
        }
    }

    public static function updateNode($node, ?int $section=null): void
    {
        Nodes::where('id', $node['id'])->update([
            'title' => $node['title'],
            'position' => $node['position'],
            'params' => $node['params'],
            'deleted_at' => !empty($node['is_deleted']) ? now() : null,
            'parent' => $section
        ]);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent')->with('media')->orderBy('position');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class,'node_id')->with('description');
    }

}
