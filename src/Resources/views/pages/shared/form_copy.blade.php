<div class="tabbable {!! $config->type !!}">
    <input type="hidden" name="id" id="page_id" value="{!! $data->id !!}">
    @include('aboleon.publisher::pages.inc.image')

    <h1 class="hyper open">
        <img src="{{ asset('aboleon/publisher/icons/double-right-arrows-angles.svg') }}" height="50" alt="">
        Contenus
    </h1>
    <div>
        <input type="hidden" name="id" value="{!! $data->id !!}">

        <?php $single_layout = $config->contains('use_single_layout');?>

        <div class="row content {{ $data->type }}">
            <div class="col-xl-7" id="main-editable">
                <div class="tabbable">

                    @if (!$config->has( 'no_lang_tabs'))
                        @include('aboleon.framework::lib.language_tabs')
                    @endif

                    @include('aboleon.publisher::pages.inc.subpages_tabs')

                    <div id="custom_dashboard">
                        @includeIf('panel.dashboards.'.$data->type)
                    </div>


                    <div class="tab-content <?= $data->type;?>">
                        @foreach($locales as $l)
                            @php
                                $content = $data->translations->filter(function($item, $key) use($l) {
                                    return $item->lg == $l;
                                })->first();
                            @endphp
                            <input type="hidden" name="page_data_id[{{ $l }}]"
                                   value="{{ $content->id ?? null }}"/>

                            <div class="tab-pane fade {{ $l == app()->getLocale() ? 'active show' : null }}"
                                 data-lang="{{ $l }}"
                                 id="lang{{ $l }}">
                                @if(AboleonPublisherHelpers::authorized($config, 'title'))
                                    <div class="bloc-editable">
                                        @include('aboleon.publisher::pages.edit.title')
                                        <br>
                                        @include('aboleon.publisher::pages.edit.subtitle')
                                    </div>
                                @endif
                                @php
                                    $has_intro = AboleonPublisherHelpers::is_enabled($config, 'intro');
                                    $has_text = AboleonPublisherHelpers::authorized($config, 'text');
                                @endphp
                                @if ($has_intro or $has_text)
                                    <div class="bloc-editable">
                                        @if ($has_intro)
                                            @include('aboleon.publisher::pages.edit.intro')
                                        @endif
                                        @if ($has_text)
                                            @include('aboleon.publisher::pages.edit.text')
                                        @endif
                                    </div>
                                @endif

                                @include('aboleon.publisher::pages.edit.text_extended')
                                @if ($multiLangCustomContent && $multiLangCustomContent->isNotEmpty())
                                    <div class="bloc-editable">
                                    <!--Single Layout Custom Blocs {{ $l }} -->
                                        <div>
                                            <div class="space-4"></div>
                                            @include('aboleon.publisher::pages.inc.custom_content_multilang')
                                        </div>
                                    </div>
                                @endif

                            <!--Meta {{ $l }} -->
                                @include('aboleon.publisher::pages.edit.meta')

                            </div>
                            <!-- tab-pane -->
                        @endforeach

                        {{-- Replicate unique group ids --}}
                        @php
                            global $replicate_tags
                        @endphp
                        @if ($replicate_tags)
                            @foreach($replicate_tags as $key_rep=>$key_rep_val)
                                <input type="hidden" class="hidden_{!! $key_rep!!}" name="replica_group_id[]"
                                       value="{!! $key_rep!!}"/>
                            @endforeach
                        @endif
                    </div>
                    <!-- tab-content -->

                </div>
                <!-- tabbable -->


                @include('aboleon.publisher::pages.edit.publisher_config')
            </div>
            <!-- col-sm-12 -->

            <div class="col-xl-5 left-tab-content">
                <?php
                $page_custom_data = $data->customContent->pluck('value', 'field')->toArray();
                ?>
                @if (isset($noLangCustomContent))
                    @include('aboleon.publisher::pages.inc.custom_content')
                @endif

                @include('aboleon.publisher::pages.custom_content.subpages_lists')

            </div>
        </div>
        <!-- row -->
    </div>
</div>