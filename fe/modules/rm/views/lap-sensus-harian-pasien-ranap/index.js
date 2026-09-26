$(document).ready(() => {
    $('#export-excel').on('click', function (e) {
        e.preventDefault();

        var tgl = $('.startDate').val();
        let _data = {
            kelas: $('#filter_kelaspelayanan').val(),
            ruangan: $('#filter_ruangan').val(),
            statusperiksa: $('#filter_status_ranap').val(),
            tgl_pendaftaran: tgl,
        }
        var _param = $.param(_data);

        $(this).attr("action","/rm/lap-sensus-harian-pasien-ranap/show-popup-excel?"+_param);
        $(this).attr("data-target","#modal_backdrop");
        $(this).attr("data-width","75%");
        $(this).attr("data-toggle","modal");
    })
});