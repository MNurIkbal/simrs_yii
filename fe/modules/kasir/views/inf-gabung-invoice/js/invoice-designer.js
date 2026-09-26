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
 
    $(".btn-cetak-invoice-gabung").on("click", function (event) {
        var penjamin_id = ''
        var jenis_invoice = $('.jenis_invoice:checked').val()
        var params = pembayaran_id + '&invoicegabung_id=' + invoicegabung_id + '&jenis_invoice=' + jenis_invoice + '&id_pegawai=' + _pegawaiId
        var url = '/reports/viewer/invoice-gabung-detail?invoice_id='

        if(jenis_invoice == 3 && listPenjamin.length != 0 && groupcarabayar_id != groupUmum) {
            penjamin_id = $('#penjamin_id').val()
            params = params + '&penjamin_id=' + penjamin_id;
        }
        window.open(url + params);
    });
 })
 
 