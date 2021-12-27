<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use Aboleon\Framework\Traits\Responses;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class ConfigsElements
{
    use Responses;

    private array $elements = [
        [
            'type' => 'input',
            'label' => 'Texte',
            'tags' => 'h1,h2,h3,h4,h5,h6,p',
            'replicable' => true
        ],
        [
            'type' => 'editor_lite',
            'label' => 'TinyMce Lite',
            'replicable' => true
        ],
        [
            'type' => 'editor',
            'label' => 'TinyMce',
            'replicable' => true
        ],
        [
            'type' => 'textarea',
            'label' => 'Textarea',
            'replicable' => true
        ],
        [
            'type' => 'link',
            'label' => 'Lien',
            'replicable' => true
        ],
        [
            'type' => 'image',
            'label' => 'Image'
        ],
        [
            'type' => 'gallery',
            'label' => 'Galerie'
        ],
        [
            'type' => 'email',
            'label' => 'e-mail',
            'replicable' => true
        ],
        [
            'type' => 'list',
            'label' => 'Liste',
            'tags' => 'select,checkbox,radio'
        ],
        [
            'type' => 'associated',
            'label' => 'Contenu associé'
        ],
        [
            'type' => 'form',
            'label' => 'Formulaire'
        ],
    ];

    public function only(string|array $elements): array
    {
        if (is_string($elements)) {
            $elements = [$elements];
        }

        return array_filter($this->elements, function ($item) use ($elements) {
            return in_array($item['type'], $elements);
        });
    }

    public function except(string|array $elements): array
    {
        if (is_string($elements)) {
            $elements = [$elements];
        }

        return array_filter($this->elements, function ($item) use ($elements) {
            return !in_array($item['type'], $elements);
        });
    }


    public function all(): array
    {
        return $this->elements;
    }

    public function element(string $element): array|bool
    {
        return current(array_filter($this->elements, function ($item) use ($element) {
            return $item['type'] == $element;
        }));
    }

    public function forGroup(string $group): array
    {
        if (method_exists($this, 'filter' . ucfirst($group))) {
            return $this->{'filter' . ucfirst($group)}();
        }
        return $this->all();
    }

    protected function filterLists(): array
    {
        return $this->only('selectable');
    }

    protected function filterPages(): array
    {
        return $this->except('selectable');
    }

}
