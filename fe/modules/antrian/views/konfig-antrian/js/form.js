/*
* @Author: Sigit
* @Date:   2019-07-24 17:16:28
*/

$(document).ready(function() {
    var is_slider = $('input[type=radio][name="KonfigAntrianForm\\[is_slider\\]"]:checked').val();

    if (is_slider != 2) {
        $(".input-file").prop("hidden", false);
        $(".input-url-slider").prop("hidden", true);
    } else {
        $(".input-file").prop("hidden", true);
        $(".input-url-slider").prop("hidden", false);
    }
});

$("#konfigantrian-form").submit(function(event) {
    event.preventDefault();

    var data = new FormData();
    var logo_files = $("#logo")[0].files;
    var slide_files = $("#slides")[0].files;
    var other_data = $("#konfigantrian-form").serializeArray();

    $.each(other_data, function(key, input) {
        data.append(input.name, input.value);
    });

    $.each(logo_files, function(key, logo) {
        data.append("KonfigAntrianForm[logo]["+key+"]", logo);
    });

    $.each(slide_files, function(key, slides) {
        data.append("KonfigAntrianForm[slides]["+key+"]", slides);
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

$('input[type=radio][name="KonfigAntrianForm\\[is_slider\\]"]').on("click", function(event) {
    var is_slider = $('input[type=radio][name="KonfigAntrianForm\\[is_slider\\]"]:checked').val();

    if (is_slider != 2) {
        $(".input-file").prop("hidden", false);
        $(".input-url-slider").prop("hidden", true);
    } else {
        $(".input-file").prop("hidden", true);
        $(".input-url-slider").prop("hidden", false);
    }
});

/* Pemisahan carabayar bpjs dan nonbpjs (kuota antrian harus di set dokter/598) */
$('#kuota_antrian').on('change', function(e) {
    if($(this).val() == 598) {
        $('.pisahCabar').prop('hidden', false)
    } else {
        $('.pisahCabar').prop('hidden', true)
    }
})
