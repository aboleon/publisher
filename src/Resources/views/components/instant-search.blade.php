<div class="d-inline-block relative {{ $classes }}" id="instant-search">
    <input type="search" class="form-control" data-type="{{ $scope }}" placeholder="Recherche rapide"/>
</div>
@push('js')
    <script src="{!! asset('aboleon/publisher/js/instant_search.js') !!}"></script>
@endpush
