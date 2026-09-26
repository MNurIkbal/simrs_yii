$(document).ready(function () {
    $('#table_history_fisioterapi').docoTabel({
        info: false,
        searching: false,
        scrollY: "250px",
        serverSide: false,
        scrollCollapse: true,
    })

    $('.btn-cetak-history').on('click', function () {
        let pendaftaran_id = $(this).data('pendaftaran_id')
        let tgl_permintaan = $(this).data('tgl_permintaan')
        let jam_permintaan = $(this).data('jam_permintaan')
        window.open(`/reports/viewer/cetak-program-terapi?pendaftaran_id=${pendaftaran_id}&tgl_permintaan=${tgl_permintaan}&jam_permintaan=${jam_permintaan}`, '_blank')
    })


    $(".btn-cetak-form").on("click", function () {
        let pendaftaran_id = $(this).data("pendaftaran_id");
        let tgl_permintaan = $(this).data('tgl_permintaan')
        let jam_permintaan = $(this).data('jam_permintaan')
        window.open(`/reports/viewer/cetak-formulir-klaim?pendaftaran_id=${pendaftaran_id}&tgl_permintaan=${tgl_permintaan}&jam_permintaan=${jam_permintaan}`, '_blank');
    })
});