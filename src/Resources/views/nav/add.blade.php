<x-aboleon.publisher-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Navigation / Ajouter une entrée
        </h2>
    </x-slot>
    <br>
    @if ($selectables->isEmpty())
        {!! ResponseRenderers::warning('Désolé mais il semble que toutes les pages ont été affectées à la navigation.','warning') !!}
    @else
        <div class="row">
            <div class="col-12">

                <div class="bloc-editable tabbable">
                    <h2>Ajout d'un élément de navigation</h2>
                    {!! AboleonPublisherHelpers::printSessionMessage() !!}
                    <h4>Cette entrée sera ajoutée comme
                        @if (!is_null($page))
                            sous-menu de
                            <span style="background:#62a8d1;color:white;padding: 3px 10px 6px;border-radius: 3px">{{ $page['title'] }}</span>
                        @else
                            principale
                        @endif
                    </h4>

                    <form method="post" action="/panel/Publisher/nav/add" autocomplete="off">
                        <input type="hidden" name="attach_to" value="{{ $page['id'] ?? ($subnav ? 'subnav' : null) }}">
                        @csrf
                        <div class="tabbable">
                            <div>

                                <div class="row">
                                    <div class="col-sm-6" style="margin-top:20px;z-index:0;">

                                        <div class="form-group" style="display: flex;">
                                            <label class="pr-3">
                                                <input type="radio" name="choose_link" value="page" checked data-bs-toggle="#page_selector"> Une page existante
                                            </label>

                                            <label>
                                                <input type="radio" name="choose_link" value="custom" data-bs-toggle="#link_selector"> Un lien sur mesure
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="tabbable py-3">
                                            <div class="toggable" id="page_selector">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-sm-12 pb-2">
                                                            <select name="page" class="form-control">
                                                                <option value="none">-- Sélectionner une page --</option>
                                                                @foreach($selectables as $item)
                                                                    <option data-subpages="{{$item->subpages_count}}" value="{{ $item->id }}">
                                                                        {{ (($item->meta->nav_title == 'Sans titre') or empty($item->meta->nav_title)) ? $item->meta->title : $item->meta->nav_title .
                                                                        (is_null($item->published) ? ' (Hors ligne)':'') .
                                                                        ($item->subpages_count ? ' ('.$item->subpages_count.' sous-pages)':'')}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="with_supages">
                                                    <div class="row">
                                                        <div class=col-sm-12>
                                                            <label>
                                                                <input type="checkbox" name="pull_children"> Afficher les sous-pages comme un sous-menu
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="toggable hidden" id="link_selector">
                                                @include('aboleon.framework::lib.language_tabs')
                                                <div class="tab-content">
                                                    @foreach($locales as $k=>$v)
                                                        <div id="lang{{$v}}"class="tab-pane fade {{ $v == app()->getLocale() ? 'show active':'' }}">
                                                            <h5>Titre</h5>
                                                            <input type="text" name="nav_links[{{$v}}][title]" class="form-control"><br>
                                                            <h5>Url</h5>
                                                            <input type="text" name="nav_links[{{$v}}][url]" class="form-control">
                                                        </div>
                                                    @endforeach
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <button id="single-save-btn" class="btn btn-danger" type="submit">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</x-aboleon.publisher-layout>

@push('js')
    <script>
        $(function () {
            $('input[name=choose_link]').click(function () {
                $('.toggable').toggleClass(('hidden'));
            });
        });
    </script>
@endpush
