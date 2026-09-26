
// console.log(jsonAntrian);
// generateTable('#table-antrian-lewati', jsonAntrian);
// Event Ready
$(document).ready(function () {

    $('#btnNext').attr('disabled', false);
    $('#btnPilih').attr('disabled', true);
    $('#btnPanggilUlang').attr('disabled', true);
    $('#btnLewati').attr('disabled', true);
    $('#btnBatal').attr('disabled', true);

    // Generate Table
    refreshTable();
});


$(document).on('keydown', null, function (e) {
    if ($('#modal_backdrop').is(':visible')) {
        if (e.key == '1') {
            if (!$("#btnNext").attr('disabled')) {
                $("#btnNext").click();
            }
        }
        if (e.key == '2') {
            if (!$("#btnPilih").attr('disabled')) {
                $("#btnPilih").click();
            }
        }
        if (e.key == '3') {
            if (!$("#btnPanggilUlang").attr('disabled')) {
                $("#btnPanggilUlang").click();
            }
        }
        if (e.key == '4') {
            if (!$("#btnLewati").attr('disabled')) {
                $("#btnLewati").click();
            }
        }
        if (e.key == '5') {
            if (!$("#btnLewati").attr('disabled')) {
                $("#btnLewati").click();
            }
        }
    } else {
    }
});

function refreshTable() {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/component-antrian',
        dataType: 'JSON',
        beforeSend: function (res) {
            $('#queue').html('...');
        },
        success: function (res) {
            if (res.limit_antrian) {
                $('#hide_limit_antrian').val(res.limit_antrian.lookup_value);
            } else {
                $('#hide_limit_antrian').val('8');
            }

            if (res.count_sisa_antrian) {
                $('#queue').html(res.count_sisa_antrian);
                if (String(res.count_sisa_antrian) == '0') {
                    $('#btnNext').attr('disabled', true);
                    $('#btnPilih').attr('disabled', true);
                    $('#btnPanggilUlang').attr('disabled', true);
                    $('#btnLewati').attr('disabled', true);
                    $('#btnBatal').attr('disabled', true);
                }
            } else {
                $('#queue').html('X');
                $('#btnNext').attr('disabled', true);
                $('#btnPilih').attr('disabled', true);
                $('#btnPanggilUlang').attr('disabled', true);
                $('#btnLewati').attr('disabled', true);
                $('#btnBatal').attr('disabled', true);
            }

            if (res.data_antrian_terlewat) {
                generateTable('#table-antrian-lewati', res.data_antrian_terlewat);
            }
        },
    });
}

var pilihLewat = function (btn){
    var header = 'Perhatian !';
    var message ='Apakah anda yakin untuk menyimpan data ini ?';
    var label = {
        buttons: {
            'No': 'btn btn-danger button-no',
            'Yes': 'btn btn-success button-yes'
        }
    };
    $.showQuestionDialog(header, message, label, function(reaction) {
        if (reaction == 'Yes') {
            hideQuestionDialog();
            $.ajax({
                url: '/pendaftaran/daftar/pilih?antrian_id=' + antrian_id,
                method: "GET",
                type: "json",
                beforeSend: function(){
                    var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay-lewat"></div>';
                    var dialogTemplate = '<div id="confirm-dialog" class="confirm-dialog-lewat">';
                            dialogTemplate += '<div class="dialog-content">';
                                dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p class=\"confirm-message-text\"></p>';
                            dialogTemplate += '</div>';
                        dialogTemplate += '</div>';

                        $('body').append(overlayTemplate);
                        $('body').append(dialogTemplate);
                        $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw"></i>&nbsp;Sedang memproses . . .');
                    },
                success: function (res) {
                    refreshTable();

                    // $('#modal_backdrop').hide();
                    $('#btnNext').attr('disabled', false);
                    $('#btnPilih').attr('disabled', true);
                    $('#btnPanggilUlang').attr('disabled', true);
                    $('#btnLewati').attr('disabled', true);
                    $('#btnBatal').attr('disabled', true);

                    $('#no_antrian').html('-');
                    $('#jumlah_panggil').html('0');
                    $('#hide_antrian_id').val('');

                    // send to form pendaftaran
                    $('.close').trigger('click');
                    $('.antrian-id').val(res.response.antrian_id).trigger('change');
                    $('.no-antrian').val(res.response.no_antrian).trigger('change');
                    $('.pasien-id').val(res.response.pasien_id).trigger('change');

                    // set ruangan & dokter automatic
                    $('#ruangan_id').val(res.response.ruangan_id).trigger('change').attr('disabled', true);
                    // $('#kunjunganform-dokter_id').attr('disabled', true);

                    $('body').find('.confirm-dialog-overlay-lewat').remove()
                    $('body').find('.confirm-dialog-lewat').remove()

                }
            });
        } else {
            docoHelper.listen = false;
        }
    });
    var antrian_id = $(btn).attr('data-antrian_id');

}

var batalLewat = function(btn){
    event.preventDefault();
    var antrian_id = $(btn).attr('data-antrian_id');
    var recheck = false;
    var header = 'Perhatian !';
    var message ='Apakah anda yakin untuk membatalkan data ini?';
    var label = {
        buttons: {
            'No': 'btn btn-danger button-no',
            'Yes': 'btn btn-success button-yes'
        }
    };
    $.showQuestionDialog(header, message, label, function(reaction) {
        if (reaction == 'Yes') {
            hideQuestionDialog();
            $.ajax({
                url: '/pendaftaran/daftar/batal?antrian_id=' + antrian_id,
                method: "GET",
                type: "json",
                beforeSend: function(){
                    var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay-lewat"></div>';
                    var dialogTemplate = '<div id="confirm-dialog" class="confirm-dialog-lewat">';
                            dialogTemplate += '<div class="dialog-content">';
                                dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p class=\"confirm-message-text\"></p>';
                            dialogTemplate += '</div>';
                        dialogTemplate += '</div>';

                        $('body').append(overlayTemplate);
                        $('body').append(dialogTemplate);
                        $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw"></i>&nbsp;Sedang memproses . . .');
                    },
                success: function (data) {

                    if (typeof $(this).data('antrian_id') !== 'undefined') {
                        antrian_id = $(this).data('antrian_id');
                        recheck = true;
                    }
                    refreshTable();
                    docoNotification('success', "Batal Antrian", "Batal Antrian Berhasil");
                    $('#btnNext').attr('disabled', false);
                    $('#btnPilih').attr('disabled', true);
                    $('#btnPanggilUlang').attr('disabled', true);
                    $('#btnLewati').attr('disabled', true);
                    $('#btnBatal').attr('disabled', true);

                    $('#no_antrian').html('-');
                    $('#jumlah_panggil').html('0');
                    $('#hide_antrian_id').val('');
                    $('body').find('.confirm-dialog-overlay-lewat').remove()
                    $('body').find('.confirm-dialog-lewat').remove()
                }
            });
        } else {
            docoHelper.listen = false;
        }
    });

}

$('#btnNext').click(function() {
    var loading_text = i18next.t("memuat");
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/next-antrian',
        // data: _data,
        dataType: 'JSON',
        beforeSend: function () {

            $('#btnNext').attr('disabled', true);
            $('#btnPilih').attr('disabled', false);
            $('#btnPanggilUlang').attr('disabled', false);
            $('#btnLewati').attr('disabled', false);
            $('#btnBatal').attr('disabled', false);
            var _html = '<i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' + loading_text + ' . . . </b>';
            $(this).html(_html);
        },
        success: function (res) {
            $('#no_antrian').html(res.data.no_antrian);
            $('#jumlah_panggil').html(res.data.panggilan_ke);
            $('#hide_antrian_id').val(res.data.antrian_id);
            $('#btnPilih').attr('data-antrian_id', res.data.antrian_id);
            $('#btnLewati').attr('data-antrian_id', res.data.antrian_id);
            $('#btnBatal').attr('data-antrian_id', res.data.antrian_id);

            // panggil antrian, DEPRECATED, MOVED TO DISPLAY ANTRIAN
            // var text = res.teks_panggil;
            // var player = $("#playerAudio");
            // var arrayText = text.split(" ");
            // arrayText.push("stop");
            // arrayText = arrayText.filter(Boolean);

            // var index = 0;

            // player[0].defaultPlaybackRate = 1;
            // player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
            // player[0].play();

            // player[0].addEventListener("ended", function () {
            //     index = index + 1;

            //     if (index < arrayText.length) {
            //         player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
            //         if (arrayText[index] == "stop") {
            //             console.log("masuk");
            //             // hapusAntrianAudio();
            //         } else {
            //             console.log(arrayText[index]);
            //             player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
            //             player[0].play();
            //         }
            //     }
            // });
        },
    });
});


$('.btnPanggilUlang').on('click', function (e) {
    e.preventDefault();
    panggilUlang();
});

function panggilUlang() {
    var loading_text = i18next.t("memuat");
    var antrian_id = $('#hide_antrian_id').val();
    if (typeof $('.btnPanggilUlang').data('antrian_id') !== 'undefined') {
        antrian_id = $('.btnPanggilUlang').data('antrian_id');
    }

    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/panggil-ulang?antrian_id=' + antrian_id,
        dataType: 'JSON',
        beforeSend: function () {
            $('#btnNext').attr('disabled', true);
            $('#btnPilih').attr('disabled', false);
            $('#btnPanggilUlang').attr('disabled', true);
            $('#btnLewati').attr('disabled', false);
            $('#btnBatal').attr('disabled', false);
            var _html = '<i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' + loading_text + ' . . . </b>';
            $('.btnPanggilUlang').html(_html);
        },
        success: function (res) {
            $('#no_antrian').html(res.data.no_antrian);
            $('#jumlah_panggil').html(res.data.panggilan_ke);
            $('#btnPilih').attr('data-antrian_id', antrian_id);
            _html = '<i class="fa fa-volume-up"></i> Panggil Ulang (3)';

            if (res.data.panggilan_ke >= $('#hide_limit_antrian').val()) {
                $('#btnPanggilUlang').attr('disabled', true);
            } else {
                $('#btnPanggilUlang').attr('disabled', false);
            }

            $('#btnPanggilUlang').html(_html);
        },
    });
}

$('.btnPilih').on('click', function (event) {
    event.preventDefault();
    var antrian_id = $(this).attr('data-antrian_id');
    $(this).docoForm("click", {
        url: '/pendaftaran/daftar/pilih?antrian_id=' + antrian_id,
        method: "GET",
        type: "json",
        success: function (res) {
            refreshTable();

            // $('#modal_backdrop').hide();
            $('#btnNext').attr('disabled', false);
            $('#btnPilih').attr('disabled', true);
            $('#btnPanggilUlang').attr('disabled', true);
            $('#btnLewati').attr('disabled', true);
            $('#btnBatal').attr('disabled', true);

            $('#no_antrian').html('-');
            $('#jumlah_panggil').html('0');
            $('#hide_antrian_id').val('');

            // send to form pendaftaran
            $('.antrian-id').val(res.response.antrian_id).trigger('change');
            $('.pasien-id').val(res.response.pasien_id).trigger('change');
            $('.no-antrian').val(res.response.no_antrian).trigger('change');

            // set ruangan & dokter automatic
            $('#ruangan_id').val(res.response.ruangan_id).trigger('change').attr('disabled', true);

            $('.close').trigger('click');
        }
    });
});


$('#btnLewati').on('click', function (event) {
    event.preventDefault();
    var antrian_id = $(this).attr('data-antrian_id')

    $(this).docoForm("click", {
        url: '/pendaftaran/daftar/lewati?antrian_id=' + antrian_id,
        method: "GET",
        type: "json",
        success: function (data) {
            refreshTable();

            $.ajax({
                type: 'GET',
                url: '/pendaftaran/daftar/count-sisa-antrian',
                dataType: 'JSON',
                beforeSend: function (res) {
                    $('#queue').html('...');
                },
                success: function (res) {
                    if (res) {
                        $('#queue').html(res);
                        if (res == '0') {
                            $('#btnNext').attr('disabled', false);
                            $('#btnPilih').attr('disabled', false);
                            $('#btnPanggilUlang').attr('disabled', false);
                            $('#btnLewati').attr('disabled', false);
                            $('#btnBatal').attr('disabled', false);
                        } else {
                            $('#btnNext').attr('disabled', false);
                            $('#btnPilih').attr('disabled', true);
                            $('#btnPanggilUlang').attr('disabled', true);
                            $('#btnLewati').attr('disabled', true);
                            $('#btnBatal').attr('disabled', true);
                        }
                    } else {
                        $('#queue').html('X');
                        $('#btnNext').attr('disabled', true);
                        $('#btnPilih').attr('disabled', true);
                        $('#btnPanggilUlang').attr('disabled', true);
                        $('#btnLewati').attr('disabled', true);
                        $('#btnBatal').attr('disabled', true);
                    }
                },
            });

            $('#no_antrian').html('-');
            $('#jumlah_panggil').html('0');
            $('#hide_antrian_id').val('');
        }
    });
});

var btnBatal;
$('.btnBatal').on('click', function (event) {
    event.preventDefault();
    var antrian_id = $(this).attr('data-antrian_id');
    var recheck = false;

    if (typeof $(this).data('antrian_id') !== 'undefined') {
        antrian_id = $(this).data('antrian_id');
        recheck = true;
    }

    $(this).docoForm("click", {
        url: '/pendaftaran/daftar/batal?antrian_id=' + antrian_id,
        method: "GET",
        type: "json",
        success: function (data) {
            refreshTable();

            $.ajax({
                type: 'GET',
                url: '/pendaftaran/daftar/count-sisa-antrian',
                dataType: 'JSON',
                beforeSend: function (res) {
                    $('#queue').html('...');
                },
                success: function (res) {
                    if (res) {
                        $('#queue').html(res);
                    } else {
                        $('#queue').html('X');
                    }
                },
            });
            $('#btnNext').attr('disabled', false);
            $('#btnPilih').attr('disabled', true);
            $('#btnPanggilUlang').attr('disabled', true);
            $('#btnLewati').attr('disabled', true);
            $('#btnBatal').attr('disabled', true);

            $('#no_antrian').html('-');
            $('#jumlah_panggil').html('0');
            $('#hide_antrian_id').val('');
        }
    });
});

var disableLewati = function(){
    $('.btnLewati').attr('disabled', true)
}
var disableBatal = function(){
    $('.btnBatal').attr('disabled', true)
}
