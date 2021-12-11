$(function() {

    $('.jcrop_image').each(function() {

        var jcroppable = $(this).closest('.jcroppable');

        var wi_w = jcroppable.find('.wi').val();
        var wi_h = jcroppable.find('.he').val();

        var temp_w = jcroppable.find('.resized_temp_w').val();
        var temp_h = jcroppable.find('.resized_temp_h').val();

        var api;
        var ratio = parseFloat(wi_w/wi_h).toFixed(3);

        $(this).Jcrop({
            onChange: function (c)
            {
                jcroppable.find('.x1').val(c.x);
                jcroppable.find('.y1').val(c.y);
                jcroppable.find('.w').val(c.w);
                jcroppable.find('.h').val(c.h);
            },
            onSelect: function (c)
            {
                jcroppable.find('.x1').val(c.x);
                jcroppable.find('.y1').val(c.y);
                jcroppable.find('.w').val(c.w);
                jcroppable.find('.h').val(c.h);
            },
            aspectRatio: ratio,
            minSize:[wi_w,wi_h],
            boxWidth: 1100,
            boxHeight: 800

        },function(){
            api = this;
            api.setSelect([130,65,130+350,65+285]);
            api.setOptions({ bgFade: true, allowResize: true, trueSize: [temp_w, temp_h] });
            api.ui.selection.addClass('jcrop-selection');
        });

    });

    /*
    Jcrop checkup functions
    */

    $(".input-file").change(function() {
        var jcroppable = $(this).closest('.jcroppable'), upload_errors = jcroppable.find('.upload_errors');
        jcroppable.find('.errors').html('');
        upload_errors.val('');

        if (parseInt($(this).attr('data-jcroppable')) == 1) {
            checkImageConstraints(this);
        }
    });

    function checkImageConstraints(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function() {
                var image = new Image();
                image.src = reader.result;

                var jcroppable = $('input[name="'+$(input).attr('name')+'"').closest('.jcroppable');

                image.onload = function() {
                    if (image.width < parseInt($(input).attr('data-w'))) {
                        jcroppable.find('.errors').append('<p class="text-danger">La largeur de l\'image ('+ image.width +'px) est en dessous de la largeur minimale requise</p>');
                        jcroppable.find('.upload_errors').val(1);
                    }
                    if (image.height < parseInt($(input).attr('data-h'))) {
                        jcroppable.find('.errors').append('<p class="text-danger">La hauteur de l\'image ('+ image.height +'px) est en dessous de la hauteur minimale requise</p>');
                        jcroppable.find('.upload_errors').val(1);
                    }
                };
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

});