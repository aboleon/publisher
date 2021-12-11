<x-aboleon.publisher-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Navigation
        </h2>
    </x-slot>

@section('ariane')
<li>Édition de {{ $data->meta->meta->title }}</li>
@stop


<form name="main_f" method="post" action="{!! $_SERVER['REQUEST_URI'] !!}" enctype="multipart/form-data" autocomplete="off">
    @csrf
    <div class="tabbable">
        <div>
            <input type="hidden" name="object_id" value="{!! $data->id !!}">

            <div class="row">
                <div class="col-sm-7" style="margin-top:20px;z-index:0;">

                    <div class="row">
                        <div class="col-sm-12" id="save-buttons" style="padding: 0 20px 15px">
                            <button id="btn_save_quit" class="btn btn-warning" name="redirect_to" value="panel/Publisher/nav/index" type="submit"><i class="fas fa-ok bigger-110"></i>{{ trans('aboleon.framework::ui.buttons.save_and_go') }}</button>
                            <button id="single-save-btn" class="btn btn-danger" type="submit"><i class="fas fa-ok bigger-110"></i>{{ trans('aboleon.framework::ui.buttons.save') }}</button>
                        </div>
                    </div>
                    <div class="tabbable">
                        {{-- @include('aboleon.framework::lib.language_tabs') --}}

                        <div class="tab-content">
                            @foreach($locales as $l)
                            <div class="tab-pane fade <?=  $l==app()->getLocale() ? 'active in': null;?>" data-lang="{{ $l }}" id="lang_{{ $l }}">
                                <input type="hidden" name="page_id" value="{!! $data->meta->id !!}">
                                <?php $content = $data->meta->translations->where('lg', $l)->first()?>
                               {{-- @include('aboleon.publisher::pages.edit.meta') --}}
                            </div>
                            @endforeach
                            <div class="space-4"></div>

                                <div class="bloc-editable">
                                    <h2>Navigation</h2>
                                    <div class="row">
                                        <div class=col-sm-3>
                                            <input type="checkbox" name="is_primary"{{ !is_null($data->is_primary) ? 'checked' : null }}> Dans le menu principal
                                        </div>
                                        {{--
                                        <div class=col-sm-3>
                                            <input type="checkbox" name="logged"{{ !is_null($data->logged) ? 'checked' : null }}> Affiché si authentifié
                                        </div>
                                        --}}
                                        <div class=col-sm-3>
                                            <input type="checkbox" name="pull_children"{{ !is_null($data->pull_children) ? 'checked' : null }}> Afficher le sous-menu
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

</x-aboleon.publisher-layout>