<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Traits\Responses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Configs extends Model
{
    use Responses;
    use SoftDeletes;

    protected $table = 'publisher_configs';
    protected $fillable = [
        'title',
        'type',
        'group',
        'configs'
    ];

    protected $casts = [
        'configs' => 'array',
    ];

    public static function hasAlready(string $type): bool
    {
        return static::where('type', $type)->exists();
    }

    public function fetchNodes(): Collection
    {
        return collect($this->nodes)->pluck('children.*.id')->flatten();
    }

    public function generateView(Pages $page): static
    {
        $path = resource_path('views' . DIRECTORY_SEPARATOR . 'panel');
        $content = Content::where(function ($q) {
            $q->whereIn('node_id', $this->fetchNodes())->where('locale', app()->getLocale());
        })->get()->toArray();

        File::ensureDirectoryExists($path);
        File::put($path . DIRECTORY_SEPARATOR . $this->type . '.blade.php', BladeGenerator::render($this, $content));

        return $this;
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(Nodes::class, 'config_id')->whereNull('parent')->with('children')->orderBy('position');
    }

    public static function listables(): Collection
    {
        return self::where('group', 'lists')->pluck('title', 'id');
    }

    public static function associatables(): Collection
    {
        return self::where('group', '!=', 'lists')->pluck('title', 'id');
    }
}
