$(".btn-cetak-kwt").on("click", function (event) {
    event.preventDefault();
    var _data = $("#kwitansi-gabung-form").serializeArray();
    $().docoForm("click",{
        url:  $("#kwitansi-gabung-form").attr('action'),
        data : _data,
        skipConfirm: true,
        skipSuccessNotif: true,
        success : function (data) {
            data = data.data;
            var id = data.invoiceGabungIdEncrypt;
            var diterima_dari = data.diterima_dari;
            var keterangan = data.keterangan;
            var jenis_kwitansi = data.jenis_kwitansi;
            var _url = "/kasir/inf-gabung-invoice/generate-kwitansi?id=" + id + '&diterima_dari=' + diterima_dari + '&keterangan=' + keterangan + '&jenis_kwitansi=' + jenis_kwitansi;
            window.open(_url, '_blank');
            docoResetForm($("#kwitansi-gabung-form"));
            $("#modal_backdrop").modal('toggle');
        }
    });
});