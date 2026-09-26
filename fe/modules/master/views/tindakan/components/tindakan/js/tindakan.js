var scenario = $('.scenario').val();
var isGroupInaCbgHidden = $('.isGroupInaCbg-hidden').val();

$("#daftartindakanform-daftartindakan_nama").on("change", function(){
    var nama = $(this).val();
    var nama_lainnya = $("#daftartindakanform-daftartindakan_namalainnya").val();
    if(nama_lainnya == '') {
        $("#daftartindakanform-daftartindakan_namalainnya").val(nama);
    }
    else {
        $("#daftartindakanform-daftartindakan_namalainnya").val(nama_lainnya);
    }
});

$(document).on('ready', function(){
    if(scenario == 'update') {
    if(isGroupInaCbgHidden == 0) {
        $('.ina_cbg').css('display', 'none');
    }
    else {
        $('.ina_cbg').css('display', 'block');
    }
}
else {
    $('.ina_cbg').css('display', 'block');
}
});

$(document).on('click', '.isGroupInaCbg', function(e) {
    var val = $('.isGroupInaCbg:checked').val();
    var v = $(this).val();
    if(val == 0) {
    	$('.ina_cbg').css('display', 'none');
    }
    else {
    	$('.ina_cbg').css('display', 'block');
    }
    
});

// $('#kelompoktindakan_id').on('change', function (event) {
//     event.preventDefault();
//     var _value = parseInt($(this).val());
//     if (_value == 18) {
//         $('#group-akomodasi').show();
//     } else {
//         $('#group-akomodasi').hide();
//     }
// });

$('select').on(
    'select2:close',
    function () {
        $(this).focus();
    }
);