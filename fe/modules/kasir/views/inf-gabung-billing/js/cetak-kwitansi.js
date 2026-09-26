$(".btn-cetak-kwt").on("click", function (event) {
   event.preventDefault();
   var _data = $("#cetak-kwitansi-form").serializeArray();
   $().docoForm("click",{
       url:  $("#cetak-kwitansi-form").attr('action'),
       data : _data,
       skipConfirm: true,
       skipSuccessNotif: true,
       success : function (data) {
           data = data.data;
           var id = data.pembayaranpelayanan_id;
           var pembayaran_id = data.pembayaran_id;
           var jenis_kwitansi = data.jenis_kwitansi;
           var diterima_dari = data.diterima_dari;
           var keterangan = data.keterangan;
           var _url = "/kasir/inf-gabung-billing/generate-kwitansi?id=" + id + '&jenis_kwitansi=' + jenis_kwitansi + '&diterima_dari=' + diterima_dari + '&keterangan=' + keterangan + '&pembayaran_id=' + pembayaran_id;
           window.open(_url, '_blank');
           docoResetForm($("#cetak-kwitansi-form"));
           $("#modal_backdrop").modal('toggle');
       }
   });
});
