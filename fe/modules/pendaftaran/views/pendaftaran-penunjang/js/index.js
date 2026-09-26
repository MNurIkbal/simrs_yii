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


$(document).on("change","#kunjunganform-kelaspelayanan_id", function (event) {
    event.preventDefault();
    $("#penjamin_id").trigger("change");
    $('.btn-pemeriksaan-clear').trigger('click');
});


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
                if (typeof _caraBayar !== 'undefined') {
                    $('#selectCarabayar').val(_caraBayar).trigger("change");
                    $('#selectCarabayar').val(_caraBayar).trigger("depdrop:change");
                }
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
    _formPendaftaran.params = 'penunjang';
    _formPendaftaran.additional = function () {
        $('input[name="TipePasienForm[is_aps]"]').on('change', function (event) {
            event.preventDefault();
            var _value = parseInt($(this).val());
            _disabled = _value;
            $('#asalrujukan_id option').each(function () {
                if (_disabled) {
                    if (parseInt($(this).attr('value')) != 1) {
                        $(this).attr('disabled', false);
                    } else {
                        $(this).attr('disabled', false);
                    }
                } else {
                    if (parseInt($(this).attr('value')) == 1) {
                        $(this).attr('disabled', false);
                    } else {
                        $(this).attr('disabled', false);
                    }
                }
            });
            $("#selectCarabayar option").each(function() {
                if(_disabled) { // aps
                    if ($(this).data('id') != 417) {
                        $(this).attr('disabled', false);
                    } else {
                        $(this).attr('disabled', false);
                    }
                } else {
                    if ($(this).data('id') == 418) {
                        $(this).attr('disabled', true);
                    } else {
                        $(this).attr('disabled', false);
                    }

                }
            });
            var _asalRujukan = 2;
            if (_disabled) {
                // $('#selectCarabayar').find('option:contains("BPJS")').prop('disabled', _disabled);
                _asalRujukan = 1;
            }
            $('#asalrujukan_id').select2().val(_asalRujukan).trigger('change');
            // $('#selectCarabayar').select2().val(5).trigger('change');
            // $('#selectCarabayar').trigger('depdrop:change');
            $('#penjamin_id').select2().empty();
        });
    }
    _formPendaftaran.resetForm = function () {
        var stepsLength = $('ul[role="tablist"] > li:not(.first)').length;
        for (x=stepsLength;x>0;x--) {
            $('.steps-basic').steps('previous');
        }
        setTimeout(function(){
            for (x=stepsLength;x>0;x--) {
                $('.steps-basic').steps("remove",x);
            }
        },300);

        $('#form-parent-info').hide();
        $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
        var _currentLi = $('ul[role="tablist"] > li.current > a').attr("aria-controls");
        $('#'+ _currentLi +' select').val('').trigger('change');
        // tableDaftarTerakhir.draw();
        _formPendaftaran.resetAsuransi();
        $('.field-tipepasienform-no_asuransi').hide();
        $('#selectCarabayar').focus();
        $('input[name="no_antrian"]').val("");

        $('input[name="TipePasienForm[is_aps]"][value="1"]').prop("checked", true).trigger('change');
        // $('#selectCarabayar').val(5).trigger('change');
        // setTimeout(function () {
        //     $('#selectCarabayar').trigger('depdrop:change');
        // },300);
    }
    _formPendaftaran.init();
    $('input[name="TipePasienForm[is_aps]"][value="1"]').prop("checked", true).trigger('change');
    // $('#selectCarabayar').val(5).trigger('change');
    // setTimeout(function () {
    //     $('#selectCarabayar').trigger('depdrop:change');
    // },300);
});
