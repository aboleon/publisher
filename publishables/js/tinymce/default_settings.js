var baseHref = location.protocol+'//'+window.location.hostname+'/';
var settings = {
    selector: ".textarea",
    theme: "silver",
    width: '100%',
    height: 480,
    menubar : false,
    entity_encoding : "raw",
    branding:false,
    plugins: [
    "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
    "table directionality emoticons template paste textpattern"
    ],
    // | forecolor backcolor
    toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontsizeselect",
    toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media responsivefilemanager | insertdatetime preview",
    toolbar3: "table | hr removeformat | subscript superscript | fullscreen | ltr rtl | spellchecker | nonbreaking restoredraft code",
    image_advtab: true ,
    language: "fr_FR",
    language_url :baseHref+"aboleon/framework/js/tinymce/langs/fr_FR.js",
    document_base_url: baseHref,
    relative_urls: false,
    remove_script_host: true,
    content_css:baseHref+"aboleon/framework/css/style_tiny.css",
    external_filemanager_path: baseHref+"vendor/responsivefilemanager/filemanager/",
    filemanager_title:"Filemanager" ,
    external_plugins: {
        "filemanager" : baseHref+"vendor/responsivefilemanager/filemanager/plugin.min.js",
        "responsivefilemanager" : baseHref+"vendor/responsivefilemanager/tinymce/plugins/responsivefilemanager/plugin.min.js"
    }
}
