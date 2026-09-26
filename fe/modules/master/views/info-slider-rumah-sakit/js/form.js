
const redirectUrl = "/master/info-slider-rumah-sakit/index";
$('#btn-kembali').on('click', function () {
    $(location).attr('href', redirectUrl);
});

$('#btn-save').on('click', function () {
    $("#form-slider").submit();
});

$("#form-slider").submit(function(event) {
    event.preventDefault();

    var data = new FormData();
    var dataPost = $("#form-slider").serializeArray();
    data.append("InfoSliderForm[file_gambar]", $("#file")[0].files[0]);
    $.each(dataPost, function (key, value) {
        data.append(value.name, value.value);
    });

    // inisiasi file size dan apakah klik upload file atau tidak
    var filesize = typeof $("#file")[0].files[0] !== 'undefined' ? $("#file")[0].files[0].size/1024/1024 : false;

    // validasi ukuran file
    if (filesize && filesize > 2) {
        docoNotification('error', 'Proses Gagal!', 'File terlalu besar! Max 2 MB!')
        return false
    }else{
        $().docoForm("click", {
            url : $("#form-slider").attr('action'),
            dataType: false, // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: data,
            method: 'post',
            isUpload: true,
            success: function(data) {
                setTimeout(function() {
                    window.location.href = redirectUrl;
                }, 1000);
            },
            error: function(event, data) {
                if (event.status===413) {
                    docoNotification('error', 'Proses Gagal!', 'File terlalu besar! Max 2 MB!')
                }
            }
        });
    }
    
});

dateRangeHelper(".rangeLahirStart", ".rangeLahirFinish", ".targetDateLahir");

