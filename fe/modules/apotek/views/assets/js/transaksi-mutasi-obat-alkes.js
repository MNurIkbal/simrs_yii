var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
var _d = new Date();
var $input = $('.pickadate').pickadate({
    format: 'dd mmmm yyyy',
    min: [_tahunpesan,_bulanpesan-1,_tglpesan],
    max: [_d.getFullYear(),_d.getMonth(),_d.getDate()],
    onStart: function () {
        var date = new Date();
        var _y = date.getFullYear();
        var _m = date.getMonth();
        var _d = date.getDate();
        if(_tglmutasioa){
            this.set('select', [_tahunmutasi, _bulanmutasi-1, _tglmutasi]);
        }else{
            this.set('select', [_y, _m, _d]);
        }
    }
});

$('#btn-print').prop('disabled', true);

$("#btn-save").on('click', function (event) {
    event.preventDefault();
    $('.form-group').removeClass('has-error');
    $('span.help-block.error').remove();
    $('div.help-block.error').remove();
    var dataPost = $("#mutasi-form").serializeArray();
    var data = table.$('input').serializeArray();
    var tableData = table.rows().data();
    var isNotLessQty = true;
    var isNotLessStock = true;
    var isValidQty = false;
    var obatAlkesNama = '';
    $.each(tableData,function(key,value){
        if(value.nilai_konversi == null){
            value.nilai_konversi = 1;
        }
        var val_pengirim = docoHelper.removeNumberFormat(value.stok_pengirim);
        var stokPengirim = val_pengirim * parseInt(value.nilai_konversi);

        if((
            data[key].value != '' && parseInt(data[key].value) > 0) || 
            parseInt(data[key].value) == 0 && (parseInt(value.jumlah_pesan) == 0
        )){
            isValidQty = true;
        }
        if(parseInt(value.jumlah_pesan) < parseInt(value.stok_pengirim)){
            if(parseInt(data[key].value) > parseInt(value.jumlah_pesan)){
                isNotLessQty= false;
                obatAlkesNama = value.nama_obat;
            }
        }else{
            if(parseInt(data[key].value) > parseInt(stokPengirim)){
                isNotLessStock= false;
                obatAlkesNama = value.nama_obat;
            }
        }
    });
    if(isNotLessStock == false){
        new PNotify({
            title: 'Gagal',
            text: `Qty Kirim ${obatAlkesNama} Tidak dapat melebihi Qty Stok Pengirim`,
            addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
            type: 'error',
            delay:2500,
            hide:true
        });
        return false;
    }

    if(isNotLessQty == false){
        new PNotify({
            title: 'Gagal',
            text: `Qty Kirim ${obatAlkesNama} Tidak dapat melebihi Qty Pesan`,
            addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
            type: 'error',
            delay:2500,
            hide:true
        });
        return false;
    }

    if(isValidQty == false){
        new PNotify({
            title: 'Gagal',
            text: 'Qty harus terisi minimal 1 dan bernilai lebih dari 0',
            addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
            type: 'error',
            delay:2500,
            hide:true
        });
        return false;
    }

    $.merge(dataPost,data);

    const promise = new Promise((resolve, reject) => {
        $(this).docoForm("click", {
            data: dataPost,
            method: 'post',
            skipSuccessNotif: true,
            success: function (data) {
                var id = data.response.id;
                $('#btn-print').prop('disabled', false);
                $('#btn-print').attr('data-target', '/apotek/transaksi-mutasi/cetak-pdf?id=' + id);
                (new PNotify({
                    title: "Berhasil",
                    text: "Proses Mutasi Berhasil.",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    history: {
                        history: false
                    }
                })).get().on('pnotify.confirm', function(){
                    window.open('/apotek/inf-pemesanan-obat-alkes/cetak-detail?id='+data.response.pemesanan);
                    window.location.href = '/apotek/inf-pemesanan-obat-alkes';
                }).on('pnotify.cancel', function() {
                    window.location.href = '/apotek/inf-pemesanan-obat-alkes';
                });

                setTimeout(function() {
                    window.location.href = '/apotek/inf-pemesanan-obat-alkes';
                }, 800);

                resolve();
            }
        });
    })

    promise.then(function(){
        setTimeout(function(){
            $("#btn-save").prop('disabled', true);
        }, 1000)
    })
})

$('#btn-ulang').on('click', function () {
    location.reload();
})

$('#btn-print').on('click', function () {
    var link = $(this).attr('data-target');

    if (typeof link !== 'undefined') {
        window.location.href = link;
    } else {
        $('#btn-print').prop('disabled', true);
    }
})

if (mutasiobatruangan_id) {
    $('#btn-print').prop('disabled', false);
    $('#btn-print').attr('data-target', '/apotek/transaksi-mutasi/cetak-pdf?id=' + mutasiobatruangan_id);
}

$(document).on('keyup', ".qty-kirim", function(){
    var _val = $(this).val();
    var _kon = $(this).data("konversi");
    var _id = $(this).data("id");
    
    if(_val % 1 != 0) {
        $(this).val(_val - (_val % 1));
    }

    var _hidden_label = ".hidden-control-" + _id;
    var _konversi = parseInt(_val) * parseInt(_kon);

    $(_hidden_label).val(_konversi);
});
