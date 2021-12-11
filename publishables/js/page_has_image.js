function pushLightGallery() {

    $('.img-holder a.zoom').off().on('click', function (e) {
        e.preventDefault();
        var t = $(this), images = [], index = parseInt($(this).attr('data-index'))
        ;
        t.closest('.media-gallery').find('a.zoom').each(function () {
            images.push({
                'src': $(this).attr('data-src'),
                'thumb': $(this).closest('.img-holder').find('img').attr('src'),
                'subHtml': '',
            });
        });

        $(this).lightGallery({
            dynamic: true,
            thumbnail: true,
            index: index,
            dynamicEl: images
        });
    });
}

$(function() {
    pushLightGallery();
    function showErrorAlert (reason, detail) {
        var msg='';
        if (reason==='unsupported-file-type') { msg = "Unsupported format " +detail; }
        else {
            console.log("error uploading file", reason, detail);
        }
        $('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>'+
            '<strong>File upload error</strong> '+msg+' </div>').prependTo('#alerts');
    }
});
