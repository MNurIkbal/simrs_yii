$("#kelompoktindakanform-kelompoktindakan_nama").on("change", function(){
    var nama = $(this).val();
    var nama_lainnya = $("#kelompoktindakanform-kelompoktindakan_namalainnya").val();
    if(nama_lainnya == '') {
        $("#kelompoktindakanform-kelompoktindakan_namalainnya").val(nama);
    }
    else {
        $("#kelompoktindakanform-kelompoktindakan_namalainnya").val(nama_lainnya);
    }
});

// $('.cyto').on('change', function() {
//     var number = $(this);
//     var eVal = (isNaN(Math.round( number.val() * 10 ) / 10)) ? 0 : ( number.val() * 10 ) / 10;
//     if(isNaN(Math.round( number.val() * 10 ) / 10)) {
//     	$('.cyto').val(0);
//     	docoNotification('error', "Error", "Format Penulisan Cyto harus 10 atau 10.5");
//     }
//     $('.cyto').val(eVal);
// });

// $('.diskon').on('change', function() {
//     var number = $(this);
//     var eVal = (isNaN(Math.round( number.val() * 10 ) / 10)) ? 0 : ( number.val() * 10 ) / 10;
//     if(isNaN(Math.round( number.val() * 10 ) / 10)) {
//     	$('.diskon').val(0);
//     	docoNotification('error', "Error", "Format Penulisan Diskon harus 10 atau 10.5");
//     }
//     $('.diskon').val(eVal);
// });