function nested_categories_slider()
{
    $('.nested_categories').find('input:checked').parents('li').find('ul:first').show();

    $('.nested_categories span.has').off().on('click', function() {
        $(this).parent().find('> ul').slideToggle();
        $(this).toggleClass('open');
    });
}
$(function() {
    $('.with-query button').click(function(e) {
        e.preventDefault();
        $(this).closest('.form').find('.ajax_subpages').slideToggle();
    });
    nested_categories_slider();

});