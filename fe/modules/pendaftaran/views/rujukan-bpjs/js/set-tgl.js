$(document).ready(function() {
    var tgl_rencana = $("#tanggal_rencana_kunjungan").val();
    $("#tanggal-rencana-kunjungan").val(tgl_rencana);
});

var date = new Date();
var today = [date.getFullYear(), date.getMonth(), date.getDate()];
var tomorrow = [date.getFullYear(), date.getMonth(), date.getDate() + 6];
$('.pickadate-w-month').pickadate({
    format: 'yyyy-mm-dd',
    selectMonths: true,
    selectYears: 99,
    formatSubmit: 'yyyy-mm-dd',
    min: today,
    max: tomorrow
});

$(document).on("change", "#tanggal_rencana_kunjungan", function () {
    var tgl_rencana = $(this).val();
    $("#tanggal-rencana-kunjungan").val(tgl_rencana);
});