$(function() {
    // Navigation
    var page_id = $('#page_id').val(),
    list_type = $('#list_type').val(),
    panel_nav = $('#sidebar'),
    contentType = $('#contentType').val(),
    anchor;

    if (contentType == undefined) {
        contentType = 'editable';
        anchor = '.'+contentType+'[data-id="'+page_id+'"]';
    } else {
       // anchor = '.'+contentType+'[data-id="'+page_id+'_'+list_type+'"]';
        anchor = '.'+contentType+'[data-id="'+page_id+'"]';
    }

    target = panel_nav.find(anchor);

    console.log(anchor, contentType, target.length);

    if (target.length) {
        if (target.parent().hasClass('submenu')) {
            target.parent().addClass('nav-show').css('display','block');
            target.parent().parent().addClass('open');
        }
        target.addClass('active');
    } else {
        target = panel_nav.find('.listable[data-id="'+page_id+'"]');
        if (target.length) {
            target.addClass('active');
        }
    }
    $('.foldable').click(function(e) {
       e.preventDefault();
       $(this).toggleClass('toggled');
       $(this).next('.row').slideToggle(function() {
           $(this).toggleClass('folded')
       }).toggleClass('unfolded');
    });
});
