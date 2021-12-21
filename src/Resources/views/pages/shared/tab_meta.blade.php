<div class="row">
    <div class="mb-3 meta col-xxl-7">
        <x-aboleon.framework-language-tabs id="lang_meta"/>

        <div class="tab-content">
            @foreach($locales as $locale)
                <div class="tab-pane fade {{  $locale == app()->getLocale() ? 'active show' : null }}"
                     data-lang="{{ $locale }}"
                     id="lang{{ $locale }}">
                    <div class="mb-3">
                        <label class="form-label">{!! trans('aboleon.framework::ui.form_labels.title') !!}</label>
                        <input type="text" name="meta[title][{{$locale}}]" value="{{ $page->translation('title',$locale) }}" class="form-control toslug" data-slug="url"/>
                    </div>
                    <div class="mb-3">
                        <x-aboleon.framework-bootstrap-textarea name="meta[abstract][{{$locale}}]" :value="$page->translation('abstract',$locale)" :label="trans('aboleon.framework::ui.form_labels.meta_resume')"/>
                    </div>
                    <div class="mb-3">
                        <x-aboleon.framework-bootstrap-input name="meta[url][{{$locale}}]" :value="$page->translation('url',$locale)" label="URL" className="slug"/>
                    </div>
                    <div class="mb-5">
                        <x-aboleon.framework-bootstrap-input name="meta[nav_title][{{$locale}}]" :value="$page->translation('nav_title',$locale)" label="Titre dans la navigation"/>
                    </div>
                    <div class="mb-3">
                        <label>{!! trans('aboleon.framework::ui.form_labels.meta_title') !!}</label>
                        <input type="text" name="meta[m_title][{{$locale}}]" value="{{ $page->translation('m_title',$locale) }}" class="form-control"/>
                    </div>
                    <div class="mb-3">
                        <label>{!! trans('aboleon.framework::ui.form_labels.meta_desc') !!}</label>
                        <textarea rows="3" name="meta[m_desc][{{$locale}}]" class="form-control mceNonEditable">{{ $page->translation('m_desc',$locale) }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="mb-3 meta col-xxl-5">
        <div class="mb-3 uploadable" data-identifier="meta" data-config-id="{{$config->id}}">
            <div class="controls">
                <strong><i class="fa fa-image"></i> {{trans('aboleon.framework::ui.form_labels.image')}}
                </strong>
                <span class="subcontrol uploader"><i class="fa fa-download"></i> Télécharger</span>
            </div>
            <div id="uploader-meta"></div>

            @php
                $dims = (new \Aboleon\Publisher\Models\FileUploadImages)->setWidthHeight($config['configs']['meta']['img']);
                $file = $page->key() . '/meta_' . $dims[0]['width'] . '.jpg';
            @endphp
            <div class="uploaded">
                @if (Storage::disk('publisher')->exists($file))
                    <div class="unlinkable uploaded-image">
                        <a target="_blank" href="{{ Storage::disk('publisher')->url($file) }}">
                            <img class="img-fluid" src="{{ Storage::disk('publisher')->url($file) }}" alt=""/>
                        </a>
                        <div>
                            @foreach($dims as $dim)
                                <span>{{ $dim['width'] .' x '. $dim['height'] }}</span>
                            @endforeach
                        </div>
                        <span class="btn btn-sm btn-danger unlink"><i class="fas fa-times"></i></span>
                    </div>
                @endif
            </div>
            {{--
                                @foreach($dim as $k=>$v)

                                        <?php [$width, $height] = getimagesize(Storage::disk('publisher')->path($file)); ?>
                                        <div class="croppable">
                                            <img id="image-{{$k}}" src="{!! asset(Storage::disk('publisher')->url($file)) !!}" alt=""/>
                                            @if($height > $v['height'] )
                                                <a class="crop" data-toggle="modal" data-target="#crop_modal" href="{!! url('kvasir/cms/images/crop?object_id=meta&page_id='.$page->id.'&size='.$k) !!}"><i class="fa fa-crop"></i></a>
                                            @endif
                                        </div>
                                @endforeach
                                --}}
        </div>
    </div>
</div>