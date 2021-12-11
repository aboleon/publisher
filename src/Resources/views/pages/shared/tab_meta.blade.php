<div class="row">
    <div class="mb-3 meta col-xxl-7">
        <div class="mb-3">
            <label class="form-label">{!! trans('aboleon.framework::ui.form_labels.title') !!}</label>
            <input type="text" name="meta[title]" value="{{ $page->meta->title ?? null }}" class="form-control toslug" data-slug="url"/>
        </div>
        <div class="mb-3">
            <x-aboleon.framework-bootstrap-textarea name="meta[abstract]" :value="$page->meta?->abstract" :label="trans('aboleon.framework::ui.form_labels.meta_resume')"/>
        </div>
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
                        <a target="_blank" href="{{ asset(Storage::disk('publisher')->url($file)) }}">
                            <img class="img-fluid" src="{{ asset(Storage::disk('publisher')->url($file)) }}" alt=""/>
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
    <div class="mb-3 meta col-xxl-5">
        <div>
            <div class="mb-3">
                <x-aboleon.framework-bootstrap-input name="meta[url]" :value="$page->meta?->url" label="URL" className="slug"/>
            </div>
            <div class="mb-3">
                <label>{!! trans('aboleon.framework::ui.form_labels.meta_title') !!}</label>
                <input type="text" name="meta[m_title]" value="{{ $page->meta->m_title ?? null }}" class="form-control"/>
            </div>
            <div class="mb-3">
                <label>{!! trans('aboleon.framework::ui.form_labels.meta_desc') !!}</label>
                <textarea rows="3" name="meta[m_desc]" class="form-control mceNonEditable">{{ $page->meta->m_desc ?? null }}</textarea>
            </div>
        </div>
    </div>
</div>