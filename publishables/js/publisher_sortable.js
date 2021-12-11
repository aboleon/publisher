function publisherSortable(classObject='Pages') {

    // Return a helper with preserved width of cells
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    $(".sortable tbody").sortable({
        helper: fixHelper,
        stop: function(event, ui) {
            $('.sortable input.order').each(function(index) {
                console.log(index);
                $(this).val(index);
                $(this).parents('tr').find('.sort_order').text(index+1);
            });

            ajax('platform=Publisher&ajax_object='+classObject+'&_token=' + $('meta[name="csrf-token"]').attr('content') + '&ajax_action=sortable&' + ($('.sortable input').serialize()), $('.form'));
        }
    }).disableSelection();

}
