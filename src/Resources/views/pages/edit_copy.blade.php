<x-aboleon.publisher-layout :title="trans('aboleon.framework::ui.page.edit')">
    @php
        global $select_values;
        $select_values = [];
        $lang_custom_data = null;
        $multiLangCustomContent = false;
    @endphp

    {{ de($nodes) }}

    @section('ariane')
        @include('aboleon.publisher::pages.inc.ariane')
    @stop

    @if ($data->trashed())
        {!! ResponseRenderers::critical("<a style='margin-top: -5px;' class='float-end btn btn-danger btn-sm' href='".url('panel/Publisher/pages/restoreContent/'.$data->id)."'>Restaurer</a>Ce contenu est archivé") !!}
    @endif

    @if ($config->has( 'custom_content'))
        @php
            $custom_content = collect($config['custom_content']);
            $multiLangCustomContent = $custom_content->filter(function($item) { return $item['multi_lang'] === true;});
            $noLangCustomContent = $custom_content->filter(function($item) { return $item['multi_lang'] === false;});
        @endphp
    @endif

    @php
        $media_folder = ''; //Media::getAccessKeyWithSeparator($data);

        # Responsive File Manager Multi-User Support
        session_start();
        $_SESSION['Aboleon']['Publisher']["project"] = config('app.project');
        $_SESSION["RF"]["subfolder"] = str_replace('/','',$media_folder);
        $_SESSION["publisher_filemanager_dir"] = config('aboleon_publisher.filemanager_dir');

        $parent_config = collect([]);
        if ($data->parent && !strstr($data->type,'section_') && !is_null($data->hasParent)) {
            $parent_config = collect(config('project.content.'.$data->hasParent->type));
            $list_type = ($data->hasParent->type && strstr($data->hasParent->type, '_list') ? 'list' : ($parent_config->has('sublist') ? 'sublist': 'list'));
        }
        $pages_data_config = config('project.config._pages_data');

    @endphp

    <form id="publisher-editable" method="post" enctype="multipart/form-data" autocomplete="off" action="{{ route('aboleon.publisher.pages.update',  $data->id) }}">
        @csrf
        @method('put')
        <div class="row sticky-top action-bar">
            <div class="col-sm-6 flex val" id="status-container" data-url="panel/Publisher/ajax">
                @if (!$config->contains('hide_status') && !AboleonPublisherHelpers::isInParentConfig($parent_config, (string)$data->type, 'hide_status'))
                    <label id="status">
                        <input type="checkbox" name="temp"
                               class="ace ace-switch ace-switch-4 btn-rotate" {!! $data->published ? 'checked' : null !!} />
                        <span class="lbl"></span>
                    </label>
                @endif
                @if (isset($parent_config['is_listable']) && !in_array('no_links',$parent_config['is_listable']))
                    <a href="{{ ($parent_config['is_listable']['url_prefix'] ?? '') . $data->content->url ?? '#' }}" target="_blank" class="title">
                        {{ $data->title }}
                    </a>
                @endif
            </div>
            <div class="col-sm-6" id="save-buttons">

                @if (config('project.config.instantsearch'))
                    @include('aboleon.publisher::components.instant-search', ['scope'=>'all', 'classes'=>'w-50 mr-2'])
                @endif

                @php
                    $back_url = redirect()->back()->getTargetUrl();
                    if (url()->current() != $back_url) {
                        session()->put('redirect_to', redirect()->back()->getTargetUrl());
                    }

                $save_and_go = $data->hasParent && strstr($data->hasParent->type, '_list');

                @endphp


                <div class="btn-group {{ !$save_and_go ? 'standalone' : null }}">
                    <button id="single-save-btn" class="btn btn-danger" type="submit">
                        <i class="fas fa-ok bigger-110"></i>
                        {{ trans('aboleon.framework::ui.buttons.save') }}
                    </button>
                    @if ($save_and_go)
                        <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                    @endif

                    <div class="dropdown-menu dropdown-menu-right">
                        @if ($save_and_go)
                            <button id="btn_save_quit" class="btn" name="redirect_to"
                                    value="{!! session()->get('redirect_to') !!}" type="submit">
                                <i class="fas fa-ok bigger-110"></i>{{ trans('aboleon.framework::ui.buttons.save_and_go') }}
                            </button>
                        @endif
                        @if(isset($parent_config['actions']) && in_array('save_and_add', $parent_config['actions']))
                            @php
                                $can_save_and_and = true;
                                if(isset($parent_config['is_listable']['limit']) && $parent_config['is_listable']['limit'] < $total) {
                                    $can_save_and_add = false;
                                }
                            @endphp

                            @if ($can_save_and_and)
                                <button id="single-save-btn" class="btn" name="save_and_add" type="submit">
                                    <i class="fas fa-ok bigger-110"></i>
                                    {{ trans('aboleon.framework::ui.buttons.save_and_add') }}
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <x-aboleon.framework-response-messages/>

        @include('pages.shared.form')
    </form>

    @php global $call_fileupload @endphp


    @push('js')
        <script>
          $(function () {
            $('#status input').click(function () {
              ajax('ajax_object=pages&platform=Publisher&ajax_action=pageStatus&object_id=' + {{ $data->id
}} +
                '&published=' + ($(this).is(':checked')), $('#status-container'),
              )
              ;
            });
          });
        </script>

        @if (!$config->contains('no_edit') && !$config->contains('no_wysiwyg'))
            @include('aboleon.publisher::lib.tinymce')
        @endif
        @if ($config->has('images') or isset($call_fileupload))
            @include('aboleon.framework::lib.light-gallery')
            <script src="{!! asset('aboleon/publisher/page_has_image.js') !!}"></script>
        @endif
        @if (isset($call_fileupload))
            @push('callbacks')
                <script src="{!! asset('aboleon/publisher/media_uploader_callbacks.js') !!}"></script>
            @endpush

            @include('aboleon.framework::lib.fileupload_scripts')
            <script src="{!! asset('aboleon/publisher/media_uploader.js') !!}"></script>
        @endif

        @if ($config->contains('google_places'))
            <script src="aboleon/framework/js/google_places.js"></script>
            <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyDf64CyWpOBCEocXjocJL_wZiW82hNtbTA&libraries=places&callback=initialize"></script>
        @endif

    @endpush


    @include('aboleon.framework::lib.time')

    @push('callbacks')
        <script>
          $(function () {
            pushCategory = function (result) {
              if (!result.hasOwnProperty('error')) {
                let container = $(result.input.ajax_callback_target);
                if (container.closest('.form').hasClass('type-select')) {
                  $(container).find(':selected').prop('selected', 0);
                  $(container).find('input[name="ajax_title"]').val('');
                  if (result.input.hasOwnProperty('query_with_children')) {
                    container = $(container).find('optgroup[data-optgroup=' + result.input.ajax_parent + ']');
                  }
                  container.append('<option value="' + result.data.id + '" selected>' + result.data.title + '</option>');
                } else if (container.closest('.form').hasClass('type-checkbox')) {
                  container.append('<li>\n' +
                    '                <label class="form-check-label">\n' +
                    '                    <input type="checkbox" class="form-check-input" name="custom_content[audience][]" value="' + result.data.id + '">' + result.data.title + '\n' +
                    '                </label>\n' +
                    '            </li>');
                }
              }
            };
          });
        </script>
    @endpush

</x-aboleon.publisher-layout>