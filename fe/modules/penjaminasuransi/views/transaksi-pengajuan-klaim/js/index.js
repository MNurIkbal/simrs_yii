$("#carabayar_id").on("change", function () {
    var carabayar_nama = $("#carabayar_id :selected").text();
    $(".carabayar_nama").val(carabayar_nama);
});

$(document).ready(function () {
    $("#carabayar_id").trigger("change");
    $("#pengajuan-form").submit(function (event) {
        event.preventDefault();
        var _value = $(this).serializeArray();
        $(this).docoForm("submit", {
            data: _value,
            success: function (data) {
                location.href = "/penjamin-asuransi/transaksi-pengajuan-klaim/list-pasien";
            },
        });
    });
});