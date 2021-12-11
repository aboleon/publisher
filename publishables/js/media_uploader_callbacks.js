function post_deleteAttachedMedia(result) {
    //console.log('Removing '+post_deleteAttachedMedia.name, result.id);
    var element = '.media_item_' + result.id, parent = $(element).closest('.media-gallery');
    $(element).remove();
    pushMediaCounter(parent);
}


function media_edit() {
    $('.media_edit').off().on('click', function (e) {
        e.preventDefault();
        var line = $(this).closest('.line'), link = $(this).prev('a');
        $('.zone_edit').remove();
        var zone = '<div class="zone_edit" style="margin-top: 10px;"><textarea class="form-control">' + link.text() + '</textarea><button class="btn btn-sm btn-warning" style="display:block;margin-top:4px;">Éditer</button></div>';
        line.append(zone);
        zonable();

        function zonable() {
            $('.zone_edit button').off().on('click', function (e) {
                e.preventDefault();
                var new_text = $(this).prev('textarea').val();
                link.text(new_text);
                ajax('ajax_object=mediaManager&ajax_action=editDescription&lg=' + line.attr('data-lg') + '&id=' + line.attr('data-id') + '&text=' + new_text, $(this).closest('.media-gallery'));
            });
        }
    });
}

function zone_edit_remove() {
    $('.zone_edit').remove();
}

function deleteAttachedMedia() {
    $('a.delete_media').off().on('click', function (e) {
        e.preventDefault();
        ajax("object_id=" + $(this).attr('data-id') +
            "&ajax_object=MediaManager&ajax_action=remove&callback=post_deleteAttachedMedia&media_type=" +
            $(this).attr('data-media'), $(this));
    });
}

function after_upload_distribute_document(data) {
    var uploaded_type = data.input.uploadable_type;
    var dispatcher = $('#publisher_uploaded_images .' + uploaded_type + '-bloc'),
        titre = data.input.description;
    if (!titre) {
        titre = data.input.uploadable_type == 'video' ? data.http : data.newfilename;
    }
    var media =
        '<div class="line media_item_' + data.uploaded_id + '" data-id="' + data.uploaded_id + '" data-lg="' + data.uploaded_lang + '">' +
        '<code><a target="_blank" href="' + data.http + '">' + titre + '</a></code>' +
        '<a href="#" class="btn btn-warning btn-sm media_edit"><i class="fas fa-pen"></i></a>' +
        '<a href="#" class="btn btn-danger btn-sm delete_media delete_media" data-media="' + uploaded_type + '" data-id="' + data.uploaded_id + '"><i class="fas fa-trash-alt"></i></a>' +
        '</div>';

    $('.media_type_' + uploaded_type + '.form').find('input, textarea').val('');

    dispatcher.append(media);
    deleteAttachedMedia();
    pushMediaCounter(dispatcher);
    media_edit();
}

function pushMediaCounter(container) {
    let elements = container.find('> div.img-holder');
    container.find('.counter').text(elements.length);
    elements.last().find('a.zoom').attr('data-index', elements.length-1);
}

function after_upload_distribute_image(data) {
    //console.log(after_upload_distribute_image.name);

    var dispatcher = $('#publisher_uploaded_images .image-bloc');

    var image =
        '<div class="img-holder media_item_' + data.uploaded_id + '">' +
        '<img src="' + data.uploaded_image_thumb + '" alt=""/>' +
        '<div class="text">' +
        '<a data-src="' + data.uploaded_image + '" target="_blank" class="zoom"><i class="white fa fa-search' +
        ' bigger-160"></i></a>' +
        '<a href="#" class="delete_media" data-media="image" data-id="' + data.uploaded_id + '"><i class="white fa fa-trash bigger-160"></i></a>' +
        '</div>' +
        '</div>';

    dispatcher.append(image);
    deleteAttachedMedia();
    pushMediaCounter(dispatcher);
    pushLightGallery();


    //var max_photos = parseInt($('#max_photos').val());
    //console.log(max_photos >= $('#publisher_uploaded_images img').length, max_photos, $('#publisher_uploaded_images img').length);
    /* if (max_photos <= $('#publisher_uploaded_images img').length) {
       $("#sup_images_link").removeClass('hidden');
   } */
}

deleteAttachedMedia();
