<div class="tabbable">
    @include('aboleon.framework::lib.language_tabs')
    <div class="tab-content">
        @foreach($locales as $locale)
            <div class="tab-pane fade {{  $locale == app()->getLocale() ? 'active show' : null }}"
                 data-lang="{{ $locale }}"
                 id="lang{{ $locale }}">
                <div class="row">
                    <div class="col-xl-7" id="main-editable">
                        <x-aboleon.publisher-organizer-page :data="collect($config?->nodes)" :page="($page)" :locale="$locale"/>
                    </div>
                    <div class="col-xl-5 left-tab-content">
                        <x-aboleon.publisher-organizer-page :data="collect($config?->nodes)" :page="($page)" :locale="$locale" section="right" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@include('aboleon.framework::lib.tinymce')
