function runInitModalTambahPemeriksaan() {
    $('#btn-tambah-pemeriksaan').unbind()
    $('#btn-tambah-pemeriksaan').bind('click', function () {
        const { href, width } = $('#btn-tambah-pemeriksaan').data()
        showLoader('Memuat Halaman ...');
        $('#modal-order-pemeriksaan').find('.modal-dialog').css('width', width)
        $('#modal-order-pemeriksaan .modal-content').parents('.modal').modal('show')
        $('#modal-order-pemeriksaan .modal-content').docoLoad({
            url: href,
            dataType: 'html',
            success: function (data) {
                hideLoader()
                $('#modal-order-pemeriksaan .modal-content').parents('.modal').modal('show')
            },
            error: function () {
                hideLoader()
            }
        })
    })
}

// $().docoForm('click', {
//     data: _form,
//     skipScrollUp: false,
//     url: $('#order-penunjang-form').attr('action'),
//     success: function (data) {
//         const { url } = data.response;
//         const { cppt_id } = data.response;
//         if ($('#order-penunjang-form').attr('action').includes('rajal')) {
//             tableCppt.draw()
//         } else if ($('#order-penunjang-form').attr('action').includes('ranap')) {
//             tabel.draw()
//         } else if ($('#order-penunjang-form').attr('action').includes('igd')) {
//             table_dpjp.draw()
//         }
//         if (typeof last_cppt != 'undefined' && cppt_id != 'undefined') {
//             last_cppt = cppt_id
//         }
//         $('#modal-lab').find('.close').click();
//         if (_listpemeriksaanpenunjang.length) {
//             PNotify.prototype.options.styling = "bootstrap3";
//             (new PNotify({
//                 title: "Berhasil",
//                 text: "Data berhasil disimpan, apakah Anda ingin melakukan cetak?",
//                 addclass: "alert alert-success alert-arrow-right alert-styled-right",
//                 type: "success",
//                 buttons: {
//                     closer: false,
//                     sticker: false
//                 },
//                 hide: false,
//                 confirm: {
//                     confirm: true
//                 },
//                 history: {
//                     history: false
//                 }
//             })).get().on('pnotify.confirm', function () {
//                 // Print
//                 window.open(url);
//             }).on('pnotify.cancel', function () {

//             });
//         }
//     }
// })

$(document).ready(function () {
    runInitModalTambahPemeriksaan();
});

