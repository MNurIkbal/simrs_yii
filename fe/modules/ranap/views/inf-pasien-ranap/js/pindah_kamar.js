
function pilihKamar(identifier) {
    let link = document.URL;
    let patternAction = link.match(/pemesanan-kamar/g);
    const jenisTempatTidur = $(identifier).data('kettempattidur_id');
    const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
    const kamarruangan_id = $(identifier).data('kamarruangan_id');
    var jk_kamar;
    var allow_jk;
    var attr = $(identifier).data('allow_jk');
    if (typeof attr !== typeof undefined && attr !== false) {
        allow_jk = $(identifier).data('allow_jk');
    }
    if (parseInt(jenisTempatTidur) == 1) {
        jk_kamar = 16;
    } else if (parseInt(jenisTempatTidur) == 2) {
        jk_kamar = 15;
    } else {
        jk_kamar = jk;
    }
    if (allow_jk && allow_jk != jk) {
        docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar fleksibel tidak sesuai dengan jenis kelamin'));
        return false;
    }
    if (jk != jk_kamar) {
        docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
        return false;
    }
    $.ajax({
        url: '/ranap/end-point/cek-ruangan',
        data: {
            ruangan_id: $(identifier).data('ruangan_id'),
            kelaspelayanan_id: $(identifier).data('kelaspelayanan_id'),
            penjamin_id: penjaminId,
            kamarruanganId: kamarruangan_id
        },
        method: 'POST',
        success: () => {
            if(typeof btnClicked == 'undefined' || (typeof btnClicked != 'undefined' && btnClicked == 'cari-kamar') ) {
                $('#modalTempatTidur').modal('hide');
                $(document).find('#kamartempattidur_id').val($(identifier).data('kamartempattidur_id'));
                $(document).find('#kamarruangan_id').val($(identifier).data('kamarruangan_id'));
                $(document).find('#nokamar').val($(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));
                $(document).find('#ruanganLabelValue').html($(identifier).data('ruangan_nama'));
                $(document).find('#ruanganIdHidden').val($(identifier).data('ruangan_id'));
                $("#kelasPelayananSelected").val($(identifier).data('kelaspelayanan_id'))
                if (typeof $("input[name='bpjsKelas']").val() !== 'undefined' && parseInt($("input[name='bpjsKelas']").val()) !== parseInt($(identifier).data('kelaspelayanan_id'))) {
                    // BPJS CONDITION
                    $(document).find('#ruanganLabelValue').append(` ${$(identifier).data('kelaspelayanan_nama')} - KAMAR TITIPAN (${($("input[name='bpjsKelas']").val() < $(identifier).data('kelaspelayanan_id') && $(identifier).data('kelaspelayanan_id') <= 3) ? 'TURUN KELAS' : 'NAIK KELAS'})`)
                } else if (typeof $("input[name='bpjsKelas']").val() === 'undefined' && parseInt($("#kelaspelayanan_id").val()) !== parseInt($(identifier).data('kelaspelayanan_id'))) {
                    $(document).find('#ruanganLabelValue').append(` ${$(identifier).data('kelaspelayanan_nama')} - KAMAR TITIPAN`)
                }
            } else if(typeof btnClicked != 'undefined' && btnClicked == 'kelas-tagihan') {
                console.log('kelas-tagihan')
                $(document).find('#kelas_ditagihkan_id').val($(identifier).data('kelaspelayanan_id'))
                $(document).find('#kamar_titipan_id').val($(identifier).data('kamartempattidur_id'))
                $(document).find('#ruangan_titipan_id').val($(identifier).data('kamarruangan_id'))
                $('#kelas-tagihan-selected').val( $(identifier).data('kelaspelayanan_nama') )
            }
            $('#kelaspelayanan_id').prop('disabled',false);
            $('#kelaspelayanan_id').val($(identifier).data('kelaspelayanan_id')).trigger('change')
            $('.modal-header').find('.close').click()
        },
        error: ({ responseJSON }) => {
            docoNotification('error', responseJSON.meta.message, '')
        },
        complete: () => {
            hideLoader()
            $.unblockUI()
        }
    })
}

// date & time
function getCurrentDate() {
    var d = new Date();
    var date = d.getDate();
    var month = d.getMonth();
    var montharr = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    month = montharr[month];
    var year = d.getFullYear();
    var day = d.getDay();
    return date + " " + month + " " + year;
}

function clock() {
    var d = new Date();
    var hour = checkTime(d.getHours());
    var min = checkTime(d.getMinutes());
    var sec = checkTime(d.getSeconds());
    var ampm = (hour >= 12) ? 'PM' : 'AM';
    var currentTime = hour + ":" + min + ":" + sec;

    // set time
    document.getElementById("tgl_pindahkamar").innerHTML = getCurrentDate() + '  ' + currentTime;;
}
function checkTime(i) {
    if (i < 10) { i = "0" + i; }  // add zero in front of numbers < 10
    return i;
}

setInterval(clock, 1000);

// modal kamar

$(() => {
    $('.uniform-checkbox').uniform()
    $("#kamarTitipanCheck").bind('change', ({ delegateTarget }) => {
        if ($(delegateTarget).is(':checked')) {
            initDatatable(true)
            activeTable = 'kamarTitipan'
            $("#tableKamarTitipanWrapper").show()
            $(".filterKamarSection").show()
            $("#tableKamarWrapper").hide()
            $("#filterHeaderKamarNonTitipan").hide()
            $("#filterHeaderKamarTitipan").show()
        } else {
            initDatatable(true)
            activeTable = 'kamar'
            $("#filterHeaderKamarNonTitipan").show()
            $("#filterHeaderKamarTitipan").hide()
            $("#tableKamarTitipanWrapper").hide()
            $("#tableKamarWrapper").show()
        }
    })
    $('#pindahkamarform-is_pasientitipan').bind('change', ({delegateTarget}) => {
        if( $(delegateTarget).is(':checked') ){
            $('.kelas-tagihan-row').removeClass('hidden')
        } else if( !$(delegateTarget).is(':checked') && !$('.kelas-tagihan-row').hasClass('hidden') ) {
            $('.kelas-tagihan-row').addClass('hidden')
        }
    })
    $("#jeniskasuspenyakit_id,#kelaspelayanan_id").bind('change', () => {
        $("#kamarTitipanCheck").prop('checked', false)
        $("#filterHeader").html('')
        activeTable = 'kamar'
    })
})
$("#btnCariKamar").bind('click', () => {
    btnClicked = 'cari-kamar'
    var jenis_id = $('#jeniskasuspenyakit_id').val();
    var kelas_id = $('#kelaspelayanan_id').val();
    var ruangan_id = $('#ruangan_id').val();
    if (!jenis_id) {
        return new PNotify({
            title: "Terjadi Kesalahan",
            text: "Jenis Kasus belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });
    }
    // if (!kelas_id) {
    //     return new PNotify({
    //         title: "Terjadi Kesalahan",
    //         text: "List Kelas Pelayanan belum dipilih!",
    //         addclass: "alert alert-warning alert-arrow-right alert-styled-right",
    //         type: "warning"
    //     });
    // }
    // if cara bayar is umum it will hide kamar titipan
    if (caraBayarId === '5') {
        $("#kamarTitipanCheck").parent().hide()
        activeTable = 'kamar'
    } else {
        $("#kamarTitipanCheck").parent().show()
    }
    initDatatable(activeTable !== 'kamar', true)
    $("#modalTempatTidur").modal({
        backdrop: 'static',
        keyboard: false
    })
})

$('#btn-kelas-tagihan').bind('click', () => {
    btnClicked = 'kelas-tagihan'
    $("#kamarTitipanCheck").parent().hide()
    if ( !$("#kamarTitipanCheck").is(':checked')) {
        $("#kamarTitipanCheck").click()
    }
    activeTable = 'kamarTitipan'
    var jenis_id = $('#jeniskasuspenyakit_id').val();
    var kelas_id = $('#kelaspelayanan_id').val();
    var ruangan_id = $('#ruangan_id').val();
    if (!jenis_id) {
        return new PNotify({
            title: "Terjadi Kesalahan",
            text: "Jenis Kasus belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });
    }
    if (!kelas_id) {
        return new PNotify({
            title: "Terjadi Kesalahan",
            text: "List Kelas Pelayanan belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });
    }
    $("#modalTempatTidur").modal({
        backdrop: 'static',
        keyboard: false
    })
})

$('#btn-pindah-kamar').click(function (e) {
    // $('#terapi-penunjang-form').submit(function(e) {
    e.preventDefault();
    var data = $('#pindah-kamar-form').serializeArray();

    // console.log(data);
    $().docoForm('click', {
        data: data,
        url: $('#pindah-kamar-form').attr('action'),
        success: function (data) {
            if (data.statusCode == 200) {
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        },
        error: function (data) {
        }
    });
});