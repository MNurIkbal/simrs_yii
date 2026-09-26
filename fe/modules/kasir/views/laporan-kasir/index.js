$(document).ready(() => {
    var jenis_laporan = null;
    $('input[type=radio][name="LaporanKasirForm\\[jenisLaporan\\]"]').change(function(event) {
        jenis_laporan = this.value;
    });
    
    
    $('#export-excel').on('click', function (e) {
        let lama_tanggal = date_diff_indays($('.startDate').val(), $('.endDate').val());
        if(lama_tanggal >= '366'){
            docoNotification("error","Export Excel Gagal !","Lebih dari 1 tahun");
            return false;
        }

        e.preventDefault();
        if (!jenis_laporan) {
            docoNotification("error","Export Excel Gagal !","Jenis Laporan harus di pilih");
            return false;
        }
    
        var tgl = $('.startDate').val()+' - '+$('.endDate').val();
        var kasir = $('#list-kasir').val();
        var instalasi = $('#list-instalasi').val();
        var penjamin = $('#list-penjamin').val();
        let _data = {
            jenis_laporan: jenis_laporan,
            range_tanggal: tgl,
            instalasi: instalasi,
            kasir: kasir,
            penjamin: penjamin,
        }
        var _param = $.param(_data);
    
        $(this).attr("action","/kasir/laporan-kasir/show-popup-excel?"+_param);
        $(this).attr("data-target","#modal_backdrop");
        $(this).attr("data-width","75%");
        $(this).attr("data-toggle","modal");
    })

    function date_diff_indays(date1, date2) {
        let addDays = 1;
        let dt1 = new Date(date1);
        let dt2 = new Date(date2);
        let dtRes = Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate())) / (1000 * 60 * 60 * 24));
        return dtRes + addDays;
    }
});

