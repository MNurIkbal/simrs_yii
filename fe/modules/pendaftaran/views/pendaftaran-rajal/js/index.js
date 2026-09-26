/*
* @Author: Rizqi Febian
* @Date:   2018-04-12 10:41:59
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-15 11:36:49
*/

//kebutuhan tarif karcis
var arrTarif = [];
var dataTarif = [];
var total_tarif = 0;
var tabelKunjungan;

var aps = false;
var bpjs = false;
var asuransi = false;
var rujukan = false;
var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
var pemeriksaanlab = {}


// $(document).on("change","#kunjunganform-kelaspelayanan_id", function (event) {
//     event.preventDefault();
//     $("#penjamin_id").trigger("change");
// });


$(document).on('keydown', null, function (e) {
    if (e.key=="F9") {
        if ($('input[name=chk-statuspasien]').is(':checked')) {
            $('input[name=chk-statuspasien]').prop('checked', false).trigger('change');
        } else {
            $('input[name=chk-statuspasien]').prop('checked', true).trigger('change');
        }
    }

    if (e.key == "F8") {
        if ($("input[name=chk-statuspasien]").is(":checked")) {
            if ($('#modal_pencarian_lanjutan').hasClass('in')) {
                $("#modal_pencarian_lanjutan").modal("hide");
            } else {
                $("#btn-pencarian-lanjutan").click();
            }
        }
    }

    if (e.key == "Enter") {
        if ($('#modal_pencarian_lanjutan').hasClass('in')) {
            if (typeof $(':focus').data("mask") !== "undefined") {
                var date = $(':focus').val();
                var regex = /^(((0[1-9]|[12]\d|3[01])\-(0[13578]|1[02])\-((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\-(0[13456789]|1[012])\-((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\-02\-((19|[2-9]\d)\d{2}))|(29\-02\-((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/g;
                var resultRegex = regex.test(date);

                if (date != '' && date != "__-__-____") {
                    if (resultRegex == false) {
                        docoNotification("warning", "Perhatian!", "Format Tanggal Salah!");

                        $(':focus').val(null);
                    } else {
                        $(".data-filter").click();
                    }
                }
            } else {
                $(".data-filter").click();
            }
        }
    }

    if (e.key == "F7") {
        if ($('#modal_pencarian_lanjutan').hasClass('in')) {
            $(".data-reset").click();
        }
    }
});

$('input').on('keydown', null, 'alt+s', function (event) {
    //each input event one by one... will be blured
    $('input').each(function () {
        $(this).trigger('blur');
    })

    if ($('.form-data-pasien').is(':visible')) {
        $(".btn-pasien-simpan-tambah").click();
    } else {
        $(".simpan-btn").click();
    }
});

$(document).on('keydown', null, 'alt+s', function (event) {
    if ($('.form-data-pasien').is(':visible')) {
        $(".btn-pasien-simpan-tambah").click();
    } else {
        $(".simpan-btn").click();
    }
});

$(document).on('keydown', null, 'alt+q', function (event) {
    $("#btn-pilih-antrian").click();
});

$(document).on('keydown', null, 'alt+w', function (event) {
    $("#btn-ubah-jenis-antrian").click();
});

$(document).on('keydown', null, 'alt+e', function (event) {
    $("#btn-pindah-loket").click();
});



// close every modal in pendaftaran
$(document).on('keydown', null, 'esc', function (event) {
    $(".close").click();
});



$('#panggilAntian').on('click', function () {
    $('.modal-antrian').modal('show');
});


function take_snapshot() {
    // take snapshot and get image data
    Webcam.snap(function (data_uri) {
        $('#profilePict').attr('src', data_uri);
    });
}

$('#open-camera').on('click', function () {
    $('.camera-modal-sm').modal('show');

    Webcam.set({
        width: 200,
        height: 200,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');
});


$(document).on('change', ".no-antrian", function () {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/pilih-antrian-manual?no_antrian=' + $(this).val(),
        dataType: 'JSON',
        beforeSend: function () {
        },
        success: function (res) {
            if (res.metadata.status == '200') {
                var response = res.response;
                _formPendaftaran.dataAntrian = response;
                var _caraBayar = $(`option[data-id="${_formPendaftaran.dataAntrian.groupcarabayar_id}"]`).attr("value");
                $('#selectCarabayar').val(_caraBayar).trigger("change");
                $('#selectCarabayar').val(_caraBayar).trigger("depdrop:change");
                $('.antrian-id').val(response.antrian_id).trigger('change');
            } else {
                docoNotification('error', null, res.response.message);
                $(this).val('');
            }

        },
    });
});


/**
 * @todo Fungsi untuk memfokuskan cursor ke suatu input
 * @author Sigit Arif Munandar <sigit@docotel.com>
 */
function focusField(attribute = "id", name, focus = true, type = "input", open=false) {
    if (attribute == "id") {
        if (type == "text" || type == "textarea") {
            $("#" + name).focus();
        } else if (type == "select") {
            if (open) {
                $("#" + name).select2("open");
            }
            $("#" + name).focus();
        }
    } else {
        if (type == "text" || type == "textarea") {
            $("." + name).focus();
        } else if (type == "select") {
            if (open) {
                $("#" + name).select2("open");
            }
            $("." + name).focus();
        }
    }
}

$(document).ready(function() {
    let pendaftaranol_id = $(".pendaftaranol_id").val();
    _formPendaftaran.pendaftaranOl = _penOl;
    _formPendaftaran.params = 'rajal';
    _formPendaftaran.init();
    if (Object.keys(_penOl).length) {
        _formPendaftaran.getInfoPasien(_penOl.pasien_id,"");
        $("#selectCarabayar").val(_penOl.carabayar_id).trigger('change');
        $('#no_rekam_medik').val(_penOl.no_rekam_medik).trigger('change');
        setTimeout(function() {
            $('#selectCarabayar').trigger('depdrop:change');
        }, 3);
    }
});
