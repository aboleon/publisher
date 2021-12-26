<x-aboleon.publisher-layout title="{{ $config->title }}">


    <x-aboleon.framework-response-messages/>

    <div class="bloc-editable p-5">
        <form id="editor"
              method="post"
              action="{{ route('aboleon.publisher.pages.update', $page->id) }}"
              data-ajax="{{ route('aboleon.publisher.ajax') }}"
              data-id="{{ $page->id }}"
              data-accesskey="{{ $page->key() }}">
            @csrf
            @method('put')

            <fieldset>
                <legend id="topbar-editor">
                    <div>
                        <i class="fas fa-th-large"></i> {{ $page->title ?? $config->title }}
                        <div class="d-flex">

                            <div id="status-container" data-url="panel/Publisher/ajax" class="me-4">
                                <label id="status">
                                    <input type="checkbox" name="temp"
                                           class="ace ace-switch ace-switch-4 btn-rotate" {!! $page->published ? 'checked' : null !!} />
                                    <span class="lbl"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <x-aboleon.framework-btn-save className="topbar-save"/>

                </legend>

            </fieldset>
            <ul id="tabs" class="nav nav-tabs admintabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ empty($page->title) ? 'active' :'' }}" id="tab_meta_tab" data-bs-toggle="tab" data-bs-target="#tab_meta" type="button" role="tab" aria-controls="tab_meta" aria-selected="true">Méta</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ !empty($page->title) ? 'active' :'' }}" id="tab_content_tab" data-bs-toggle="tab" data-bs-target="#tab_content" type="button" role="tab" aria-controls="tab_content" aria-selected="true">Contenu</button>
                </li>
            </ul>

            <div class="tab-content base">
                <div class="tab-pane fade {{ !empty($page->title) ? 'show active' :'' }}" id="tab_content" role="tabpanel" aria-labelledby="tab_content_tab">
                    @include('aboleon.publisher::pages.shared.form')
                </div>
                <div class="tab-pane mb-4 fade {{ empty($page->title) ? 'show active' :'' }}" id="tab_meta" role="tabpanel" aria-labelledby="tab_meta_tab">
                    @include('aboleon.publisher::pages.shared.tab_meta')
                </div>
            </div>
        </form>
    </div>

    @include('aboleon.publisher::pages.shared.modal_crop')

    <template id="create-list-entry">
        <div class="create-list-box" data-ajax="{{ route('aboleon.publisher.ajax') }}">
            <div class="d-flex mt-2 params">
                <input name="entry" class="form-control me-2" placeholder="Intitulé"/>
                <input type="hidden" name="list_id"/>
                <input type="hidden" name="selectable"/>
                <input type="hidden" name="action" value="AddEntryToList"/>
                <span class="save btn btn-success btn-sm pt-2">Créer</span>
                <span class="cancel btn btn-secondary btn-sm pt-2 ms-2">Annuler</span>
            </div>
        </div>
    </template>

    @include('aboleon.framework::lib.fileupload_scripts')
    @include('aboleon.framework::lib.fileUpload')

    @push('js')
        <script src="{{ asset('vendor/garand-sticky/jquery.sticky.js') }}"></script>
        <script src="{{ asset('aboleon/publisher/js/editor.js') }}"></script>
        <script>
          $(function () {
            $('#topbar-editor').sticky({topSpacing: 0});
            $('#status input').click(function () {
              ajax('action=publishedStatus&id=' + $('#editor').data('id') + '&published=' + ($(this).is(':checked')), $('#status-container'));
            });
          });
        </script>
    @endpush
</x-aboleon.publisher-layout>