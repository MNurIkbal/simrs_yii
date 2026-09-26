$(document).ready(function () {
    // $('[data-tooltip="tooltip"]').tooltip(); 
    // $('body').tooltip({ selector: '[data-tooltip=tooltip]' })
    $('.select2').val('').trigger('change.select2');
    $('.data-filter').trigger('click');
});

$('.data-filter').click(function() {
    var instalasi_id = $('#instalasi_id').val();
    var ruangan_id = $('#ruangan_id').val();
    var pegawai_id = $('#dokter_id').val();
    var hari = $('#hari').val();
    var jam_mulai = $('#jam_mulai').val();
    var jam_selesai = $('#jam_selesai').val();

    $.ajax({
        'type': "POST",
        'dataType': 'html',
        'url': baseUrl + "pendaftaran/penjadwalan-dokter/render-jadwal-dokter",
        'data': {
            instalasi_id: instalasi_id,
            ruangan_id: ruangan_id,
            pegawai_id: pegawai_id,
            hari: hari,
            jam_mulai:jam_mulai,
            jam_selesai:jam_selesai
        },
        'beforeSend': function() {
            $('.list-jadwal-dokter').html('<tr><td colspan=32><h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' + i18next.t("memuat") +'... </b></h3></td></tr>');
            
        },
        'success': function (res) {
            if (res == '""') {
                $('.list-jadwal-dokter').html('<tr><td></td><td></td><td></td><td colspan=29 > ' + i18next.t('Data tidak ditemukan') + ' </td ></tr>');
            } else {
                $('.list-jadwal-dokter').html(res);
            }
        },
        'error': function (res) {
            $('.list-jadwal-dokter').html('<tr><td></td><td></td><td></td><td colspan=29 > ' + i18next.t('Terjadi kesalahan') + ' </td ></tr>');
        }
    });
});
$('.data-reset').click(function() {
    $('#instalasi_id').val('');
    $('#ruangan_id').val('');
    $('#dokter_id').val('');
    $('#hari').val('');
    $('#jam_mulai').val('');
    $('#jam_selesai').val('');

    var instalasi_id = $('#instalasi_id').val();
    var ruangan_id = $('#ruangan_id').val();
    var pegawai_id = $('#dokter_id').val();
    var hari = $('#hari').val();
    var jam_mulai = $('#jam_mulai').val();
    var jam_selesai = $('#jam_selesai').val();

    $.ajax({
        'type': "POST",
        'dataType': 'html',
        'url': baseUrl + "pendaftaran/penjadwalan-dokter/render-jadwal-dokter",
        'data': {
            instalasi_id: instalasi_id,
            ruangan_id: ruangan_id,
            pegawai_id: pegawai_id,
            hari: hari,
            jam_mulai:jam_mulai,
            jam_selesai:jam_selesai
        },
        'beforeSend': function() {
            $('.list-jadwal-dokter').html('<tr><td colspan=32><h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' + i18next.t("memuat") +'... </b></h3></td></tr>');
            
        },
        'success': function (res) {
            if (res == '""') {
                $('.list-jadwal-dokter').html('<tr><td></td><td></td><td></td><td colspan=29 > ' + i18next.t('Data tidak ditemukan') + ' </td ></tr>');
            } else {
                $('.list-jadwal-dokter').html(res);
            }
        },
        'error': function (res) {
            $('.list-jadwal-dokter').html('<tr><td></td><td></td><td></td><td colspan=29 > ' + i18next.t('Terjadi kesalahan') + ' </td ></tr>');
        }
    });
});

$('.data-excel2').click(function () {
    var instalasi_id = $('#instalasi_id').val();
    var ruangan_id = $('#ruangan_id').val();
    var pegawai_id = $('#dokter_id').val();
    var hari = $('#hari').val();
    var jam_mulai = $('#jam_mulai').val();
    var jam_selesai = $('#jam_selesai').val();
    window.open('/master/jadwal-dokter/export-excel?instalasi_id=' + instalasi_id + '&ruangan_id=' + ruangan_id + '&pegawai_id=' +pegawai_id + '&hari='+ hari + '&jam_mulai='+jam_mulai+'&jam_selesai='+jam_selesai);
});

$(document).on('click', '.delete', function (event) {
    event.preventDefault();
    $(this).docoForm('delete', {
        additional: 'data-rm',
        success: function (data) {
            $('.data-filter').click();
        }
    });
})
$(document).on('click', '.update-jadwal', function (event) {
    // window.open('update?id='+$(this).data('id'));
    var url = '/master/jadwal-dokter/update?id='+$(this).data('id');
    $(location).attr('href',url);
});

$('.show-info').hide();
$(document).on('mouseover', '.jadwal-dokter', function () {
    $('.show-info').hide();
    $(this).find('.show-info').toggle();
});

$(document).on('mouseleave', '.jadwal-dokter', function () {
    $('.show-info').hide();
});