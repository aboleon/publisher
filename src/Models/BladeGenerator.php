<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

class BladeGenerator
{

    public static function render(Configs $config, array $content): string
    {
        ob_start();
        ?>
<x-front-layout>
    @if ($config)
    {{-- Page type: <?= $config->type; ?> --}}
        <article class="{{ $config->type }}">
            @foreach($config->config['elements'] as $key => $section)
            {{-- Section: $section['title'] --}}
            <section class="{{ $section['classes'] ?? '' }}">
            @if (array_key_exists('elements', $section))
                @foreach($section['elements'] as $subkey => $element)
                @php
                $tag = \Aboleon\Publisher\Models\Content::tag($element);
                @endphp
                @if(in_array($element['type'], ['input','email','intro','text']))
                {{ '<'.$tag.' class="'.($element['classes'] ?? '').'">'. \Aboleon\Publisher\Models\Content::value($content, $subkey) .'</'.$tag.'>' }}
                @endif
                @endforeach
            @endif
            </section>
            @endforeach
        </article>
   @endif
</x-front-layout>
        <?php
        return (string)ob_get_clean();
    }
}
