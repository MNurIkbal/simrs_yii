
$(".btn-save").on('click', function (event) {
    event.preventDefault();
    var data = new FormData();
    var dataPost = $("#ruangan-form").serializeArray();
    data.append("RuanganForm[ruangan_image]", $("#ruangan_image")[0].files[0]);
    data.append("RuanganForm[ruangan_filesuara]", $("#ruangan_filesuara")[0].files[0]);
    $.each(dataPost, function(key, value) {
        data.append(value.name, value.value);
    });
    $(this).docoForm("click", {
        url: '/master/ruangan/create', // point to server-side PHP script 
        dataType: false,  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: data,                         
        method: 'post',              
        isUpload: true,
        success: function (data) {
            var form = $("#ruangan-form");
            form[0].reset();
            $("#modal_backdrop").modal('toggle');
            setTimeout(function () {
                tableRuangan.draw();
            }, 1000);
        }
    });
})


$(".btn-edit").on('click', function (event) {
    var formAction = $("#ruangan-form").attr('action');
    event.preventDefault();
    var data = new FormData();
    var dataPost = $("#ruangan-form").serializeArray();
    data.append("RuanganForm[ruangan_image]", $("#ruangan_image")[0].files[0]);
    data.append("RuanganForm[ruangan_filesuara]", $("#ruangan_filesuara")[0].files[0]);
    $.each(dataPost, function(key, value) {
        data.append(value.name, value.value);
    });
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
            var form = $("#ruangan-form");
            form[0].reset();
            tableRuangan.draw();
            $("#modal_backdrop").modal('toggle');
        }
    });
})