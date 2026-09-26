const redirectUrl = "/master/dokter/index";

$('#btn-kembali').on('click', function () {
    $(location).attr('href', redirectUrl);
});

$('#btn-save').on('click', function () {
    $("#form-dokter").submit();
});

$("#form-dokter").submit(function (event) {
    event.preventDefault();

    var data = new FormData();
    var dataPost = $("#form-dokter").serializeArray();
    $.each(dataPost, function (key, value) {
        data.append(value.name, value.value);
    });
    data.append("DokterForm[photopegawai]", $("#file")[0].files[0]);

    $(this).docoForm("submit", {
        dataType: false, // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        method: 'post',
        isUpload: true,
        success: function (data) {
            setTimeout(function () {
                window.location.href = redirectUrl;
            }, 1000);
        },
        error: function () {}
    });
});
