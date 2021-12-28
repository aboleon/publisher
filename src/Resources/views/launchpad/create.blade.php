<x-aboleon.publisher-layout title="Créer une page">

    @push('css')
        {!! csscrush_tag(public_path('aboleon/publisher/js/jquery-ui-1.13.0.custom/jquery-ui.css')) !!}
        {!! csscrush_tag(public_path('aboleon/publisher/css/organizer.css')) !!}
    @endpush

    <x-aboleon.framework-response-messages/>

    <form method="post" action="{{ $route }}" autocomplete="off" data-ajax="{{ route('aboleon.publisher.ajax') }}" id="form-launchpad">
        @csrf
        @if($data->id)
            @method('put')
        @endif
        <div class="bloc-editable p-5">
            <fieldset>
                <legend>
                    <i class="fas fa-th-large"></i> Paramètres
                </legend>
                <div class="row">
                    <div class="col-sm-6">
                        <x-aboleon.framework-bootstrap-input name="title" label="Nom de la page" :value="old('title') ?? $data->title"/>
                    </div>

                    <div class="col-sm-3">
                        <x-aboleon.framework-bootstrap-input name="type" label="Type" :value="old('type') ?? $data->type"/>
                    </div>

                    <div class="col-sm-3">
                        <x-aboleon.framework-bootstrap-input name="group" label="Groupe" :value="old('group') ?? $data->group"/>
                    </div>
                    <div class="col-2 mt-3">
                        <x-aboleon.framework-bootstrap-radio name="configs[meta][tags]" label="Meta tags" :values="[1=>'Oui', 0=>'Non']" :affected="old('configs.meta.tags') ?? ( empty($data['configs']['meta']['tags']) ? 0 : 1 ) "/>
                    </div>
                    <div class="col-2 mt-3">
                        <x-aboleon.framework-bootstrap-radio name="configs[replicable]" label="Réplicable" :values="[0=>'Non',1=>'Oui']" :affected="old('configs.replicable') ?? ( $data['configs']['replicable'] ?? 0 ) "/>
                    </div>
                    <div class="col-8 mt-3">
                        <x-aboleon.framework-bootstrap-input name="configs[meta][img]" label="Formats des images" :value="old('configs.meta.img') ??  ($data['configs']['meta']['img'] ?? null)" placeholder="Dimensions des images. Ex: 1920,1080;680,320;  Par défaut: 1920,1080;400,auto"/>
                    </div>
                </div>

            </fieldset>

            <fieldset class="mt-5">
                <legend>
                    <i class="fas fa-th-large"></i> Configuration du contenu
                </legend>
                <div id="organizer" class="d-flex justify-content-between">
                    <div class="organizer" id="organizer_left">
                        <x-aboleon.publisher-organizer-launchpad :data="(array)$data?->nodes->toArray()"/>
                    </div>
                    <div class="organizer" id="organizer_right">
                        <x-aboleon.publisher-organizer-launchpad :data="(array)$data?->nodes->toArray()" section="right"/>
                    </div>
                </div>
                <div id="organizer_elements" class="d-flex">
                    @foreach($elements as $type)
                        <div {!! array_key_exists('tags', $type) ? ' data-tags="'.$type['tags'].'"' :'' !!} {!! array_key_exists('replicable', $type) ? ' data-replicable' :'' !!} id="{{ $type['type'] }}">
                            {{ $type['label'] }}
                        </div>
                    @endforeach
                </div>
            </fieldset>

        </div>
        <x-aboleon.framework-btn-save/>
    </form>
    <template id="listables">
        @forelse($listables as $key => $title)
            <option value="{{ $key }}">{{ $title }}</option>
        @empty
        @endforelse
    </template>
    <template id="associatables">
        @forelse($associatables as $key => $title)
            <option value="{{ $key }}">{{ $title }}</option>
        @empty
        @endforelse
    </template>
    <template id="formables">
        @forelse($formables as $form)
            <option value="{{ $form['name'] }}">{{ __('forms.labels.'.$form['name']) }}</option>
        @empty
        @endforelse
    </template>
    @push('callbacks')
        <script src="{{ asset('aboleon/publisher/js/organizer-callbacks.js') }}"></script>
    @endpush
    @push('js')
        <script src="{{ asset('aboleon/publisher/js/jquery-ui-1.13.0.custom/jquery-ui.min.js') }}"></script>
        <script src="{{ asset('aboleon/publisher/js/organizer.js') }}"></script>
    @endpush
</x-aboleon.publisher-layout>