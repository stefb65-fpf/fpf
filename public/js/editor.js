var textarea = document.querySelector('.editor')

if (window.tinyMCE) {
    tinymce.init({
        selector: '.editor',
        language_url : '/js/tinyMCE/fr_FR.js',
        language: 'fr_FR',
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table contextmenu directionality emoticons template paste textcolor"
        ],
        menubar: 'edit insert view format table help',
        toolbar: "undo redo | styleselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview media fullpage | forecolor backcolor",
        paste_data_images: true,
        automatic_uploads: true,
        relative_urls: false, remove_script_host: false, convert_urls: true,
        table_sizing_mode: 'responsive',
        // images_upload_handler: function (blobinfo, success, failure) {
        //     var data = new FormData();
        //     data.append('attachable_id', textarea.dataset.id);
        //     data.append('attachable_type', textarea.dataset.type);
        //     data.append('name', blobinfo.blob(), blobinfo.filename());
        //     axios.post(textarea.dataset.url, data)
        //         .then(function (res) {
        //             success(res.data.url)
        //         })
        //         .catch(function (err) {
        //             alert(err.response.statusText)
        //             success('http://placehold.it/350x150')
        //             // failure(err.response.statusText)
        //         })
        // }
    })
}
