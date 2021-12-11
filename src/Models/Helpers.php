<?php declare(strict_types=1);

namespace Aboleon\Publisher\Models;

use \DateTime;

class Helpers
{
    public static function printSessionMessage($key = 'session_message'): string
    {
        if (session()->has($key) && !is_array(session()->get($key))) {
            return ResponseRenderers::notice((string)session()->get($key));
        }
        return '';
    }

    public static function select_list($list, $selected = null): string
    {
        $options = '';
        foreach ($list as $key => $virgo) {
            $options .= '<option value="' . $key . '"' . ($selected && $key == $selected ? ' selected' : null) . '>' . $virgo . "</option>\n";
        }
        return $options;
    }

    public static function checkbox_list($list, $name, $selected = null): string
    {
        $checkbox = '';
        foreach ($list as $key => $virgo) {
            $checkbox .= '<div class="checkbox"><label><input type="checkbox" class="flat" name="' . $name . '" value=' . $key . (in_array($key, $selected) ? ' checked="checked"' : null) . '> ' . $virgo . '</label></div>';
        }
        return $checkbox;
    }

    public static function radio($name, $value, $label, $data): string
    {
        return '<input type="radio" value="' . $value . '" name="' . $name . '"' . (!is_null($data) && $data === $value ? 'checked' : null) . '> ' . $label;
    }

    public static function checkbox($name, $value, $label, $data): string
    {
        return '<input type="checkbox" value="' . $value . '" name="' . $name . '"' . (is_array($data) && in_array($value, $data) ? 'checked' : (!is_null($data) && $data === $value ? 'checked' : null)) . '> ' . $label . "\n";
    }


    public static function decode($text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        return stripslashes($text);
    }

    public static function shorten_text($text, $max_length = 140, $cut_off = '...', $keyeep_word = false): string
    {
        $text = strip_tags($text);
        if (strlen($text) <= $max_length) {
            return $text;
        }
        if ($keyeep_word) {
            $text = substr($text, 0, $max_length + 1);
            if (strrpos($text, ' ')) {
                $text = substr($text, 0, ' ');
            }
        } else {
            $text = substr($text, 0, $max_length);
        }
        return trim($text, ' ') . $cut_off;
    }

    public static function inArrayString(string $needle, array $array)
    {
        return array_reduce($array, function ($isFound, $value) use ($needle) {
            return $isFound || strpos($needle, $value) !== false;
        }, false);
    }

    public static function implodeWithKeys($array): string
    {
        return implode(',', array_map(
            function ($val, $key) {
                return sprintf("%s=%s", $key, $val);
            },
            $array,
            array_keys($array)));
    }


    public static function getkeypath($arr, $lookup)
    {
        if (array_key_exists($lookup, $arr)) {
            return array($lookup);
        }
        $array = array_filter($arr, function ($item) {
            return is_array($item);
        });
        foreach ($array as $key => $subarr) {
            $ret = Helpers::getkeypath($subarr, $lookup);
            if ($ret) {
                $ret[] = $key;
                return $ret;
            }
        }
        return null;
    }

    public static function is_visible($config, $var): string
    {
        if (!Helpers::authorized($config, $var)) {
            return 'hidden';
        }
        return '';
    }

    public static function is_enabled($config, $var): bool
    {
        return $config->contains($var) or $config->has($var);
    }

    public static function authorized($config, $var): bool
    {
        return !isset($config['exclude']) or !in_array($var, $config['exclude']);
    }

    public static function isInParentConfig(object $parent_config, string $type, string $value)
    {
        return $parent_config->has('has') && array_key_exists($type, $parent_config['has']) && in_array($value, $parent_config['has'][$type]);
    }

    public static function get_custom_value($data, $field): string
    {
        if (!is_null($data)) {
            $content = $data->where('field', $field)->first();
            if (!is_null($content)) {
                return $content->content->content;
            }
        }
        return '';
    }

    public static function get_custom_content($data, $field)
    {
        return optional($data->customContent)->where('field', $field);
    }

    public static function castFromInteger($item, $value)
    {
        if (array_key_exists('cast', $item) && $item['cast'] == 'integer') {
            $value = (int)$value / 100;
        }
        return $value;
    }

    public static function printModal($id, $formUrl, $question): string
    {
        ob_start();
        ?>
        <div id="myModal<?= $id; ?>" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="<?= url($formUrl); ?>">
                        <?= csrf_field(); ?>
                        <div class="modal-header no-padding modal-info">
                            <div class="table-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    <span class="white">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <p><?= $question; ?></p>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="object_id" value="<?= $id; ?>"/>
                            <button class="btn" data-dismiss="modal" aria-hidden="true"><?= trans('core::ui.buttons.cancel'); ?></button>
                            <button type="submit" class="btn btn-primary"><?= trans('core::ui.buttons.confirm'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public static function form_with_locales(object $data, string $property, string $field_type = 'input', string $classes = ''): string
    {
        $html = '';
        foreach (config('ProjectBaseConfig_config . locales') as $lang) {
            $value = optional($data->translations->where('lg', $lang)->first())->{$property};
            $html .= '
    < div class="input-holder" > ';
            $html .= '<img src = "' . asset('Core/css/flags/' . $lang . '.png') . '" alt = "' . trans('core::ui.lg_' . $lang) . '" class="flag" > ';
            switch ($field_type) {
                case 'textarea':
                    $html .= ' < textarea id = "translatable_' . $lang . '_' . $property . '" name = "translatable[' . $lang . '][' . $property . ']" class="form-control ' . $classes . ' ' . ($lang != config('app.fallback_locale') ? ' use_alt ' : null) . $lang . '" > ' . $value . '</textarea > ';
                    break;
                default :
                    $html .= '<input placeholder = "' . $lang . '" name = "translatable[' . $lang . '][' . $property . ']" value = "' .
                        $value . '" class="form-control ' . ($lang != config('app.fallback_locale') ? ' use_alt ' : '') . $lang . '" />';
            }
            $html .= '
</div > ';
        }
        return $html;
    }

    public static function printSql($query)
    {
        d(\Illuminate\Support\Str::replaceArray(' ? ', $query->getBindings(), $query->toSql()));
    }

    public function printQuery($query)
    {
        return self::printSql($query);
    }

    public static function printNestedTree($item, $currentCategories, $field_name)
    {

        if ($item->children->isNotEmpty()) {
            echo "<ul>";
            foreach ($item->children as $val) {
                $count = $val->children->count();
                echo "<li>
                    <input type='checkbox' name='" . $field_name . "' value='" . $val->id . "' " . (in_array($val->id, $currentCategories) ? "checked='checked'" : null) . " />
                    <span" . ($count ? ' class="has"' : '') . ">" . $val['title'] . " " . ($count ? ' (' . $count . ')' : '') . "</span>";
                self::printNestedTree($val, $currentCategories, $field_name);
                echo "</li>";
            }
            echo "</ul>";
        }
    }

}
