$("#btn-upload").on('click', function (event) {
    event.preventDefault();
    var data = new FormData();
    var dataPost = $("#form-upload").serializeArray();
    data.append("UploadForm[upload_file]", $("#file")[0].files[0]);
    $.each(dataPost, function (key, value) {
        data.append(value.name, value.value);
    });
    $(this).docoForm("click", {
        url: '/laboratorium/input-hasil/upload', // point to server-side PHP script 
        dataType: false, // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        method: 'post',
        isUpload: true,
        success: function (data) {
            // setTimeout(function () {
            //     location.reload();
            // }, 1000);
        }
    });
})