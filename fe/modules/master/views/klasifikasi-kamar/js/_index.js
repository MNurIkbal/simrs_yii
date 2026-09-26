
$(".btn-save").on('click', function (event) {
    event.preventDefault();
    var data = new FormData();
    var dataPost = $("#klasifikasi-kamar-form").serializeArray();
    $.each(dataPost, function(key, value) {
        data.append(value.name, value.value);
    });
    var namakelas_aplicare = $('#klasifikasikamarform-kodekelas_aplicare :selected').val() != '' ? $('#klasifikasikamarform-kodekelas_aplicare :selected').text() : '';
    data.append("KlasifikasiKamarForm[namakelas_aplicare]", namakelas_aplicare);
    $(this).docoForm("click", {
        url: '/master/klasifikasi-kamar/create', // point to server-side PHP script 
        dataType: false,  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: data,                         
        method: 'post',              
        isUpload: true,
        success: function (data) {
            var form = $("#klasifikasi-kamar-form");
            tableKlasifikasiKamar.draw();
            $("#modal_backdrop").modal('toggle');
        },
        error: function(data) {
            var response = data.responseJSON.response;
            let message = [];

            if (response.data != undefined) {
                var data = response.data
                for (const [key, value] of Object.entries(data)) {
                    message.push(value);
                }
                docoNotification("error", "Proses Gagal", message.join( "<br />" ))
            } else {
                docoNotification("error",  response.message)
            }
        }
    });
})


$(".btn-edit").on('click', function (event) {
    var formAction = $("#klasifikasi-kamar-form").attr('action');
    event.preventDefault();
    var data = new FormData();
    var dataPost = $("#klasifikasi-kamar-form").serializeArray();
    $.each(dataPost, function(key, value) {
        data.append(value.name, value.value);
    });
    var kodekelas_aplicare = $('#klasifikasikamarform-kodekelas_aplicare :selected').val()
    var namakelas_aplicare = kodekelas_aplicare != '' ? $('#klasifikasikamarform-kodekelas_aplicare :selected').text() : '';
    data.append("KlasifikasiKamarForm[kodekelas_aplicare]", kodekelas_aplicare);
    data.append("KlasifikasiKamarForm[namakelas_aplicare]", namakelas_aplicare);
    $(this).docoForm("click", {
        url: formAction, // point to server-side PHP script 
        dataType: false,  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: data,                         
        method: 'post',              
        isUpload: true,
        success: function (data) {
            var form = $("#klasifikasi-kamar-form");
            tableKlasifikasiKamar.draw();
            $("#modal_backdrop").modal('toggle');
        }
    });
})