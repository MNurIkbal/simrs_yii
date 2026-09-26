
// // Event export excel
// $(document).on('click', '.btn-custom-excel', function() {
//     // Go to specified location
//     window.open('/pendaftaran/penjadwalan-dokter/export-excel');
// });

// $(document).ready(function () {
//     $('body').tooltip({ selector: '[data-tooltip=tooltip]' });
// });

// // Event filter
// $('.data-filter').click(function() {
//     var ruangan_id = $('#ruangan').val();
//     var jam_mulai = $('#jam_mulai').val();
//     var jam_selesai = $('#jam_selesai').val();
//     var pegawai_id = $('#dokter').val();

//     $.ajax({
//         type: "POST",
//         url: baseUrl + "pendaftaran/penjadwalan-dokter/index",
//         data: {
//             ruangan_id: ruangan_id,
//             jam_mulai: jam_mulai,
//             jam_selesai: jam_selesai,
//             pegawai_id: pegawai_id
//         },
//         success: function (res) {
//           $('.result').html(res)
//         },
//         error: function (res) {
//           $('.result').html(res)
//         }
//     });
// });
$(document).ready(function () {
    // $('[data-tooltip="tooltip"]').tooltip(); 
    $('body').tooltip({ selector: '[data-tooltip=tooltip]' })
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
        'url': baseUrl + "pendaftaran/penjadwalan-dokter/render-penjadwalan-dokter",
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
        'url': baseUrl + "pendaftaran/penjadwalan-dokter/render-penjadwalan-dokter",
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
    window.open('/pendaftaran/penjadwalan-dokter/export-excel?instalasi_id=' + instalasi_id + '&ruangan_id=' + ruangan_id + '&pegawai_id=' +pegawai_id + '&hari='+ hari + '&jam_mulai='+jam_mulai+'&jam_selesai='+jam_selesai);
});

