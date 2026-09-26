/*
* @Author: rizqi_fitrianto
* @Date:   2018-11-05 17:44:21
* @Last Modified by:   rizqi_fitrianto
* @Last Modified time: 2018-11-06 10:17:57
*/

$(document).on('change', '#uniform-pengembalianform-carapembayaran', function(){
    if($(this).is(":checked") == true){
        $('#pengembalianform-no_rek').removeAttr('readonly')
        $('#pengembalianform-namapemilik_rek').removeAttr('readonly')
    }
    if($(this).is(":checked") == false){
        $('#pengembalianform-namapemilik_rek').attr('readonly', true)
        $('#pengembalianform-no_rek').attr('readonly', true)
    }
})
$(document).on('keyup', '#pengembalianform-biaya_administrasi', function(){
    hitungTotal();
})
function hitungTotal(){
    let uangmuka = ($('.total-pengembalian').val() != '') ? parseInt( docoHelper.convertToAngka($('.total-pengembalian').val()) )  : 0;
    let biayaadministrasi = ($('.biayaadministrasi').val() != '') ? parseInt(docoHelper.convertToAngka($('.biayaadministrasi').val())) : 0;
    let _total = uangmuka - biayaadministrasi
    console.log(_total)
    docoHelper.pembulatan(_total, $('.pengembalianform-pembulatan'), $('.uang-diterima'), '-')
}