$(() => {
   $('#penjamin_id').prop('disabled', true);
   $('.jenis_invoice').on('change', function () {
      if ($(this).val() == 3 && listPenjamin.length != 0 && groupcarabayar_id != groupUmum) {
          $('#penjamin_id').prop('disabled', false);
      }
      else {
          $('#penjamin_id').prop('disabled', true);
      }
   })

   $(".btn-cetak-invoice").on("click", function (event) {
       var penjamin_id = url = ''
       var jenis_invoice = $('.jenis_invoice:checked').val()
       if(jenis_invoice == 3 && listPenjamin.length != 0 && groupcarabayar_id != groupUmum) {
           penjamin_id = $('#penjamin_id').val()
           url = '/reports/viewer/new-invoice-detail?invoice_id=' + pembayaran_id + '&penjamin_id=' + penjamin_id + '&jenis_invoice=' + jenis_invoice + '&id_pegawai=' + _pegawaiId;
       }
       else {
           url = '/reports/viewer/new-invoice-detail?invoice_id=' + pembayaran_id + '&jenis_invoice=' + jenis_invoice + '&id_pegawai=' + _pegawaiId;
       }
       window.open(url);
   });

   $(".btn-direct-cetak-invoice").on("click", function (event) {
       var penjamin_id = url = ''
       var jenis_invoice = $('.jenis_invoice:checked').val()
       if(jenis_invoice == 3 && listPenjamin.length != 0 && groupcarabayar_id != groupUmum) {
           penjamin_id = $('#penjamin_id').val()
           url = '/kasir/inf-pasien-sudah-bayar/pdf-cetak-invoice-detail?invoice_id=' + pembayaran_id + '&penjamin_id=' + penjamin_id + '&jenis_invoice=' + jenis_invoice + '&id_pegawai=' + _pegawaiId;
       }
       else {
           url = '/kasir/inf-pasien-sudah-bayar/pdf-cetak-invoice-detail?invoice_id=' + pembayaran_id + '&jenis_invoice=' + jenis_invoice + '&id_pegawai=' + _pegawaiId;
       }
       window.open(url);
   });
})

