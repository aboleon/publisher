<x-aboleon.publisher-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Navigation
        </h2>
    </x-slot>

<?php
    include ('functions.php');
//include(base_path('Modules/Publisher/Resources/views/nav/functions.php'));
?>
    <br>
    <x-aboleon.framework-response-messages/>
    <table class="table table-striped table-bordered dt-responsive nowrap table-hover sortable" cellspacing="0" width="100%">
        <caption data-url="panel/Publisher/ajax">
            <a class="btn btn-success float-end" href="/panel/Publisher/nav/add">
                Ajouter une entrée principale
            </a>
            <h3 class="float-start">Navigation principale</h3>
        </caption>
        <thead>
        <tr>
            <th width="100" class="text-center">#</th>
            <th style="width: 50%">{!! trans('aboleon.framework::ui.title')!!}</th>
            <th width="200">{!! trans('aboleon.framework::ui.meta.status')!!}</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>


        @foreach($primary_nav as $val)
            {!! printRow($val, 0, '') !!}
            {!! recursiveNavRow($val, $children, 0, '') !!}
        @endforeach
        </tbody>
    </table>


    <table class="table table-striped table-bordered dt-responsive nowrap table-hover sortable" cellspacing="0" width="100%">
        <caption data-url="panel/Publisher/ajax">
            <a class="btn btn-success pull-right" href="/panel/Publisher/nav/add?subnav">
                Ajouter une entrée à la navigation secondaire
            </a>
            <h3>Navigation secondaire</h3>
        </caption>
        <thead>
        <tr>
            <th width="100" class="text-center">#</th>
            <th style="width: 50%">{!! trans('aboleon.framework::ui.title')!!}</th>
            <th width="200">{!! trans('aboleon.framework::ui.meta.status')!!}</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($secondary_nav as $val)
            {!! printRow($val, 0, '') !!}
        @endforeach
        </tbody>
    </table>

</x-aboleon.publisher-layout>
@push('js')
    <script>
        $(function () {

            // Return a helper with preserved width of cells
            var fixHelper = function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            };

            $(".sortable tbody").each(function () {
                var toSort = $(this);
                toSort.sortable({
                    helper: fixHelper,
                    stop: function (event, ui) {
                        toSort.find('input.order').each(function (index) {
                            $(this).val(index);
                            $(this).parents('tr').find('.sort_order').text(index + 1);
                        });
                        ajax("ajax_object=Nav&ajax_action=sortable&" + (toSort.find('input.order').serialize()), $(this).parent('table').find('caption'));

                    }
                }).disableSelection();
            });

        });
    </script>
@endpush
