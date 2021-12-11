$(function() {

    function getAcceptableTypes() {
        return eval($('input[name="acceptable_types"]').val());
    }

    /* -----------------------
        Media radio choices
    ----------------------- */
    if ($("input.media-selectable").length) {
        if ($("input.media-selectable :checked").length < 1) {
            $('.fileupload-container').hide();
        }
        $("input.media-selectable").click(function() {

            $('.media-containers').addClass('hidden');
            var media_channel = $(this).data('type');
            var media_type = $(this).data('media');

            $('.upload_params').addClass('hidden');
            $('.params_media_type_'+media_type).removeClass('hidden');

            console.log(media_channel);
            $('.fileupload-container input[name="uploadable_type"]').val(media_type);
            if (media_channel == 'fileupload') {
                $('.fileupload-container').removeClass('hidden').show();
                $('.fileupload-container input[name="acceptable_types"]').val($(this).attr('data-acceptable'));
                if ($(this).attr('data-config') != undefined) {
                    $('.fileupload-container input[name="uploadable_config"]').val($(this).attr('data-config'));
                } else {
                    $('.fileupload-container input[name="uploadable_config"]').val('');
                }
            } else {
                $('.fileupload-container').hide();
                $('.fileupload-container input[name="uploadable_config"]').val('');
                $(this).closest('.form').find('.media_type_' + media_type).removeClass('hidden');
            }
        });
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/panel/Publisher/ajax',
        type: 'POST',
        dataType:'json',
    });

    //console.log(getAcceptableTypes());

    // CONVENTIONAL TEXT UPLOADS

    $('button.upload_media_type').click(function(e) {
        e.preventDefault();

        var btn = $(this);
        btn.find('.loading-cog-1').remove();
        btn.append('<img class="loading-cog-1 Loading-icon" src="/aboleon/framework/icons/cogs.svg" alt="" />');

        var container = $(this).closest('.form');
        var formData = [];

        var content = container.find("input[name='content']");
        var pattern = new RegExp(content.val());
        if (!isUrlValid(content.val())) {
            content.addClass('required');
            setTimeout(function() {
                btn.find('.loading-cog-1').remove();
            },1000);
            return false;
        } else {
            content.removeClass('required');
        }
        formData.push(
            {name: "page_id", value: $('#page_id').val()},
            {name: "uploadable_type", value: $('input[name="uploadable_type"]').val()},
            {name: "ajax_object", value: 'MediaUploader'},
            {name: "ajax_action", value: 'upload'},
            {name: 'lang', value : container.find('.lang:checked').val()},
            {name: "content", value: content.val()},
            {name: "description", value: container.find('textarea.description').serialize()},
            {name: "callback", value: 'after_upload_distribute_document'}
            );
        ajax(formData,container);
    });

    // THE FILEUPLOAD MANAGER
    $('#fileupload').fileupload({
        url: '/panel/Publisher/ajax',
        type: 'POST',
        dataType:'json',
        context: $('#fileupload')[0],
        done: function (e, data)
        {
            $('.progress').hide();
            console.log(data);
            // Auto-detect Callback
            var fn = window['after_upload_distribute_'+data.result.uploadable_type];
            if(typeof fn === 'function') {
                fn(data.result)
            }
            $('tbody.files tr.template-upload').each(function() {
                if ($(this).find('button.start:disabled').length) {
                    $(this).fadeOut(function() {
                        $(this).remove();
                    });
                }
            });
        },
        error: function (xhr, ajaxOptions, thrownError)
        {
            var result = ' Error status : '+ xhr.status+ ", Thrown Error : "+ thrownError +", Error : "+ xhr.responseText;
            $('#AjaxSqlResponse').html('<div class="alert alert-danger" style="margin-top:10px;padding:8px 15px">'+result+'</div>')
        },
        always: function(e,data)
        {
            $('.progress').hide();
        },
        start: function(e,data)
        {
            $('.progress').show();
        },
            //acceptFileTypes: getAcceptableTypes(),
            maxFileSize: 16000000,
        //maxNumberOfFiles: 1,
        autoUpload:false,
        //messages : { maxNumberOfFiles: $('#imp .messages .maxNumberOfFiles').text() }
    });

    $('#fileupload').bind('fileuploadsubmit', function (e, data) {
        $('#imp div.errors').remove();
        /*
        if ($("#publisher_uploaded_images img").length >= parseInt($('#max_photos').val())) {
            $('#imp').append('<div class="errors">'+$('#imp .messages .max_photos_m').text()+'</div>');
            return false;
        }
        */
        var uploadable_type = $('input[name="uploadable_type"]').val();

        data.formData = [];
        data.formData.push(
            {name: "page_id", value: $('#page_id').val()},
            {name: "uploadable_type", value: uploadable_type},
            {name: "uploadable_config", value: $('input[name="uploadable_config"]').val()},
            {name: "ajax_object", value: 'MediaUploader'},
            {name: "ajax_action", value: 'upload'},
            {name: "description", value: data.context.find('textarea.description').serialize()},
            );

        var uploadable_type_params = $('#fileupload .params_media_type_'+uploadable_type);
        if (uploadable_type_params.length) {
            var multilang = uploadable_type_params.find('.lang');
            if (multilang.length)
            data.formData.push(
                {name: "lang", value: uploadable_type_params.find('.lang:checked').val()},
            );
        }
    });

    $('#fileupload').bind('fileuploadadd', function (e, data) {
        if (!$('.fileupload-container').is(':visible')) {
            data.abort();
        }

        var uploadErrors = [];
        var acceptFileTypes = getAcceptableTypes();
        //console.log(acceptFileTypes.test(data.files[0]['type']));

        if(data.files[0]['type'].length && !acceptFileTypes.test(data.files[0]['type'])) {
            uploadErrors.push('Not an accepted file type');
            return false;
        }

    });
});
