$("#konfigpendaftaran-form").submit(function(event) {
    event.preventDefault();

    var data = new FormData();
    var logo_files = $("#logo")[0].files;
    var other_data = $("#konfigpendaftaran-form").serializeArray();
    console.log('logo_files', logo_files);
    $.each(other_data, function(key, input) {
        data.append(input.name, input.value);
    });

    $.each(logo_files, function(key, logo) {
        data.append("KonfigPendaftaranForm[dash_logo]["+key+"]", logo);
    });

    $(this).docoForm("submit", {
        dataType: false,
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        method: "post",
        isUpload: true,
        success: function(data) {
            location.reload();
        }
    });
});

$("#logo").on("filepredelete", function(event) {
    var abort = true;

    if (confirm("Apakah anda yakin untuk menghapus logo ini?")) {
        abort = false;

        setInterval(function() {
            location.reload();
        }, 3000);
    }

    return abort;
});

$("#slides").on("filepredelete", function(event) {
    var abort = true;

    if (confirm("Apakah anda yakin untuk menghapus slides ini?")) {
        abort = false;

        setInterval(function() {
            location.reload();
        }, 3000);
    }

    return abort;
});
