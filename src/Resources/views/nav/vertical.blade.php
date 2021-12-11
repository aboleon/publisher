@can('dev')
<li class="header" style="background-color: #e4e4e4;color: #737373;text-indent: 2px;}"><span>Publisher</span></li>
<x-aboleon.framework-nav-link :route="route('aboleon.publisher.launchpad.index')" icon="fas fa-file" title="Contenus configurés"/>
<x-aboleon.framework-nav-link :route="route('aboleon.publisher.pages.index')" icon="fas fa-file" title="Tous les contenus"/>
{{--
<li>
    <x-aboleon.framework-nav-opening-link icon="fas fa-file-text-o" :title="ucfirst(trans_choice('aboleon.publisher::ui.pages.pages',2))"/>

    <ul class="nav child_menu">

        <li>
            <a href="panel/Publisher/pages/add">
                <span>{!! trans('aboleon.framework::ui.create.page') !!}</span>
            </a>
        </li>
    </ul>
</li>

--}}
@endcan