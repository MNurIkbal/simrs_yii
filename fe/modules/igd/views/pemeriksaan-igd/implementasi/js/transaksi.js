var tabel_tindakanmedis;
var tabel_bmhpalkes;
// var sdf = new Date();
// console.log(sdf.getMonth());
function clock_imp() {
    var d = new Date();
    var hour = checkTime(d.getHours());
    var min = checkTime(d.getMinutes());
    var sec = checkTime(d.getSeconds());
    var ampm = (hour >= 12) ? 'PM' : 'AM';
    var currentTime = hour +":"+ min +":"+ sec;
    var getmont = parseInt(d.getMonth()) +1;
    var month = checkTime(getmont);
    var date = checkTime(d.getDate());

    var realtime = d.getFullYear()+"-"+month+"-"+date+" "+hour+":"+min+":"+sec;
    $('#implementasiform-tgl_implementasi').val( realtime );
    $('.tgl_implementasi').html( realtime );
}
function checkTime(i) {
    if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
    return i;
}

setInterval(clock_imp, 1000);

$('#btn-simpan-transaksi-implementasi').on('click',function(event){

    event.preventDefault();
    let dataImplementasi = $('#form-implementasi').serializeArray();
    let dataTindakan = $("#form-implementasi-tindakan").serializeArray();
    let dataBmhp = $("#form-implementasi-bmhp").serializeArray();
    let dataAdditional = $("#form-implementasi-additional").serializeArray();

    let dataTemp = $.merge(dataTindakan, dataImplementasi);
    let dataTemp2 = $.merge(dataTemp, dataBmhp);
    let formData = $.merge(dataTemp2, dataAdditional);
    $(this).docoForm('click',{
        url: "/igd/pemeriksaan-igd/implementasi-create-transaksi",
        data: formData,
        success: function(){
            $("#btn-kembali-transaksi-implementasi").trigger('click');
        }
    });
});

$('#btn-kembali-transaksi-implementasi').on('click',function(){
    $('#content-implementasi').docoLoad({
        url: '/igd/pemeriksaan-igd/implementasi?id='+pendaftaran_id,
        dataType: 'html',
        success : function(data) {
        }
    });
});

$(document).on('keyup', '.jml_implemen', function(e) {
    e.preventDefault();
    var qty_belum = parseInt($(this).closest('tr').find('td.qty_belum').html());
    var thisVal = $(this).val();
    var qty_implemen = thisVal != '' ? parseInt(thisVal) : 0;
    var tarif_satuan = $(this).closest('tr').find('td.tarif_satuan').data('tarif');
    tarif_satuan = tarif_satuan != '' ? parseInt(tarif_satuan) : 0;
    var data_tarif_cyto = parseInt($(this).closest('tr').find('td.tarif_cyto').data('tarif'));
    var data_is_cyto = parseInt($(this).closest('tr').find('td.is_cyto').data('cyto'));
    var tarif_cyto = 0;
    if(data_is_cyto == 1){
        tarif_cyto = data_tarif_cyto;
    }
    var subtotalTindakan = 0;
    subtotalTindakan = qty_implemen * (tarif_satuan+tarif_cyto);
    if (qty_implemen > qty_belum) {
        $(this).val('');
        subtotalTindakan = 0;
    }
    $(this).closest('tr').find('td.subtotalTindakan').html(docoHelper.convertToRupiah(subtotalTindakan));

    var grandTotalTindakan =0;
    $('#tb-implementasi-tindakan-medis').find("tbody").find("tr").each(function(){
        var subtotal = docoHelper.convertToAngka($(this).closest('tr').find('td.subtotalTindakan').html());
        grandTotalTindakan = grandTotalTindakan + subtotal;
    });

    $('#totalTindakan').html(docoHelper.convertToRupiah(grandTotalTindakan));
});
$(document).on('keyup', '.jml_implemen_bmhp', function(e) {
    e.preventDefault();
    var qty_belum = parseInt($(this).closest('tr').find('td.qty_belum_bmhp').html());
    var thisVal = $(this).val();
    var qty_implemen = thisVal != '' ? parseInt(thisVal) : 0;
    var tarif_satuan = $(this).closest('tr').find('td.tarif_satuan').data('tarif');
    tarif_satuan = tarif_satuan != '' ? tarif_satuan : 0;
    var subtotalBmhp = 0;
    subtotalBmhp = qty_implemen * tarif_satuan;
    if (qty_implemen > qty_belum) {
        $(this).val('');
        subtotalBmhp = 0;
    }
    $(this).closest('tr').find('td.subtotalBmhp').html(docoHelper.convertToRupiah(subtotalBmhp));

    var grandTotalBmhp = 0;
    $('#tb-implementasi-pemakaian-bmhp').find("tbody").find("tr").each(function(){
        var subtotal = parseFloat(docoHelper.convertToAngka($(this).closest('tr').find('td.subtotalBmhp').html()));
        grandTotalBmhp = grandTotalBmhp + subtotal;
    });

    $('#totalBmhp').html(docoHelper.convertToRupiah(grandTotalBmhp));
});