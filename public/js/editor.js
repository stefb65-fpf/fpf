var textarea = document.querySelector('.editor')

if (window.tinyMCE) {
    tinymce.init({
        selector: '.editor',
        language_url : '/js/tinyMCE/fr_FR.js',
        language: 'fr_FR',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        // plugins: [
        //     "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
        //     "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        //     "save table contextmenu directionality emoticons template paste textcolor"
        // ],
        // menubar: 'edit insert view format table help',
        // toolbar: "undo redo | styleselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview media fullpage | forecolor backcolor",
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
