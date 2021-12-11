<p>
    @foreach($locales as $locale)
    <input type="radio" class="lang" name="{{ $item_key }}_type_{{ $media_type }}_lang" value="{{ $locale }}" {!! $locale == config('app.fallback_locale') ? 'checked' : null !!}> {{ trans('core::lang.'.$locale.'.label') }}
    @endforeach
</p>