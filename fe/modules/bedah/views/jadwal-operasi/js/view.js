$(document).ready(function () {
    setPendaftaran();
    var readmore = $('#c_klinis').html();
    var lessmore = readmore.substr(0, 112);
    if (readmore.length > 150) {

        $('#c_klinis').html(lessmore).append("<a href='' class='read-more-link'>Tampilkan Lebih ...</a>");

    } else {
        $('#c_klinis').html(readmore);
    }

    $("body").on("click", ".read-more-link", function (event) {
        event.preventDefault();
        $(this).parent('#c_klinis').html(readmore).append("<a href='' class='show-less-link' >Sembunyinkan ...</a>");
    });
    $("body").on("click", ".show-less-link", function (event) {
        event.preventDefault();
        $(this).parent('#c_klinis').html(readmore.substr(0, 112)).append("<a href='' class='read-more-link' >Tampilkan Lebih ...</a>");
    });

    var _options = new Option(ruanganNama, ruanganId, false, false);
    $('#ruangan-form').append(_options).val(ruanganId).trigger('change')

    $('#ruangan-form').on('change', function() {
        $("#kamar-form").val('').trigger('change')
    })
});

$(document).on('click', '#setuju-pasien', function (event) {
    event.preventDefault();
    var data_kamar = $('#kamar-form').select2('data');
    var kamarruangan_id = null;
    if (data_kamar.length) {
        kamarruangan_id = data_kamar[0].id;
    }
    var _url = '/bedah/jadwal-operasi/terima?id=' + surgeryId;
    $().docoForm('click', {
        url: _url,
        data: $('#ajax-form').serializeArray().concat([
            { name: 'JadwalOperasiForm[kamarruangan_id]', value: $("#kamar-form").val() },
            { name: 'JadwalOperasiForm[ruangan_id]', value: $("#ruangan-form").val() },
            { name: 'JadwalOperasiForm[pendaftaran_id]', value: $("#pendaftaranid-form").val()}
        ]),
        confirmMessage: 'Apakah anda setuju untuk pasien ini ?',
        success: function (data) {
            var _res = data.response;
            var _data = _res.data;
            if (_res.is_registrasi_terbaru) {
                (new PNotify({
                    title: "Informasi",
                    text: "No. Registrasi Pasien <strong>" + _data.nama_pasien + "</strong> dengan No RM. "
                        + _data.no_rekam_medik + " terdeteksi telah melakukan pendaftaran pada tanggal " + _data.tgl_permintaan +
                        " dengan no. registrasi " + _data.no_pendaftaran_lama + " .Data no. registrasi pasien akan secara otomatis diperbaharui. Apakah Anda ingin melanjutkan ?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "warning",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: 'Ya',
                                addClass: 'btn btn-xs btn-success',
                            },
                            {
                                text: 'Tidak',
                                addClass: 'btn btn-xs btn-danger',
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on('pnotify.confirm', function () {
                    _url = _url + "&is_konfirm=1";
                    $().docoForm('click', {
                        url: _url,
                        data: $('#ajax-form').serializeArray().concat([
                            { name: 'JadwalOperasiForm[kamarruangan_id]', value: $("#kamar-form").val() },
                            { name: 'JadwalOperasiForm[ruangan_id]', value: $("#ruangan-form").val() }
                        ]),
                        skipConfirm: true,
                        success: function (data) {
                            setTimeout(function () {
                                window.location.href = $('.data-back').attr('href');
                            }, 1000);
                        }
                    });
                }).on('pnotify.cancel', function () {
                    location.reload();
                });
            }
            else {
                setTimeout(function () {
                    window.location.href = $('.data-back').attr('href');
                }, 1000);
            }
        },
        error: function(){
            if($(".has-error").length > 0){
                $('html, body').animate({
                    scrollTop: $(".has-error").offset().top - (300)
                }, 0);
            }
        }
    });
})
$(() => {
    var selectedKamar = new Option(kamarRuanganText, kamarRuanganId, true, true);
    $('#kamar-form').append(selectedKamar).trigger('change');
    
    $("#kamar-form").select2InfinityScroll({
        url: '/bedah/jadwal-operasi/kamar-ruangan',
        callbackData: (params) => {
            return {
                payload: {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                    kelaspelayanan_id: kelasPelayananId,
                    ruangan_id: $('#ruangan-form option').filter(':selected').val()
                }
            }
        }
    })
    $("#ruangan-form").select2InfinityScroll({
        url: '/bedah/jadwal-operasi/ruangan',
        callbackData: (params) => {
            return {
                payload: {
                    term: params.term,
                    page: params.page || 1,
                    limit: params.limit,
                }
            }
        }
    })
})

function setPendaftaran() {
    var initOption = new Option("Pilih", '', false, false);
    $('#pendaftaranid-form').append(initOption);
    JSON.parse(_listPendaftaran).forEach((data, key) => {
        let text = `${data.no_pendaftaran} - ${data.tgl_pendaftaran}`;
        var newOption = new Option(text, data.pendaftaran_id, false, false);
        $('#pendaftaranid-form').append(newOption);
    });
}
