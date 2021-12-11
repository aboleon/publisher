$(function() {

    function replicator_delete() {
        //console.log('replicator_delete is declared');
        $('button.delete_replica').off().click(function(e) {
            $(this).closest('.replicate.row').remove();
            /*
            e.preventDefault();
            var target = $(this).attr('data-target');
            if (target == 'replicate') {
                $('.replicate.'+$(this).attr('data-code')).remove();
                //$(this).closest('.'+target).remove();
            } else {
                ($(this).closest('.replicate').find('.'+target).length > 2) ? $(this).closest('.'+target).remove() : $(this).closest('.'+target).find('input,textarea').val('');
            }
            */
        });
    }

    function replicator(element) {

        $('button.'+element).off().click(function(e) {
            e.preventDefault();
            var t = $(this).data('template'),
            fromTemplate = $('template.'+t).length;
            if (fromTemplate) {
                var replicated = $('<div class="replicate row">'+$('template.'+t).html()+'</div>');
            } else {
                var replicate = $(this).prev('div.'+element);
                var replicated = replicate.clone();
            }

            console.log(fromTemplate);

            var replicate_guid = guid();
            replicated.find('.replicate_id').val(replicate_guid);
            replicated.find('.replicate_fields:not(:first, button)').remove();

            replicated.find('input:not(.replicate_id), textarea').each(function(index) {
                $(this).attr('name', 'replica_content['+$(this).data('replicate_id')+']['+$(this).data('replicate_key')+'][]');
            });

            $('.replica_container.'+t).append(replicated);
            /*
            if (!fromTemplate) {
                console.log('Appennding 1', replicated, $('.replica_container').length);
                //$(replicated).insertAfter(replicate);
                $('.replica_container').append(replicated);
            } else {
                console.log('Appennding 2');
                replicated.find('.replicate_fields input').each(function() {
                    $(this).attr('name', $(this).attr('name')+'[]');
                });
                $('.replica_container').append(replicated);
            }
            */

            /*

            $('.replica_container').each(function() {
                var lg = $(this).closest('.tab-pane').attr('data-lang');
                $(this).find('.replicate.row').last().find('input:not(.replicate_id), textarea').each(function(index) {
                    var string = $(this).attr('name');
                    string = string.replace('['+lg+']','');
                    $(this).attr('name', string +'['+lg+']');
                });
            });

            $('div.replicate').last().find('.replicatable').each(function() {
                replicator($(this).attr('data-replica'));
            });
            */
            replicator_delete();

        });
    }

    replicator('replicate');
    replicator('replicate_fields');
    replicator_delete();

});