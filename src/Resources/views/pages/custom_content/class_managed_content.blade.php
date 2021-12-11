<?php
$class_managed = $data['class_managed'] = $sc['class_managed_content'];
$class_get_form = explode('@', $class_managed['get']);
$class_to_call = '\App\Models\ClassManagedCustomContent\\'.current($class_get_form);

if (!class_exists($class_to_call)) {
    echo ResponseRenderers::warning(trans('aboleon.framework::ui.errors.UncallableClass', ['class'=>$class_to_call]));
} else {

    $class_managed_object = new $class_to_call();
    $class_managed_method = end($class_get_form);
    if (!method_exists($class_managed_object, $class_managed_method)) {
        echo ResponseRenderers::warning(trans('aboleon.framework::ui.errors.inexistingMethod', ['method'=>$class_to_call.'\\<strong>'.$class_managed_method.'</strong>']));
    } else {

        echo $class_managed_object->{$class_managed_method}($data);
        ?>
        @if (array_key_exists('css', $class_managed))
            @push('css')
            {!! csscrush_inline(public_path('css/class_managed_'.$class_managed['css'].'.css'), ['minify'=>true]) !!}
            @endpush
        @endif
        @if (array_key_exists('js', $class_managed))
            @push('js')
            <script src="{!! asset('js/class_managed_'.$class_managed['js'].'.js') !!}"></script>
            @endpush
        @endif
        <?php
    }
}
