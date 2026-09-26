$(".datetime").AnyTime_picker({
    format: "%d-%m-%Y %H:%i",
    earliest: new Date(),
    latest: new Date(new Date().setHours(23, 59, 59, 999))
});
$(document).ready(function () {
    let checkValue = $('.form-demam').find('input').prop('checked');
    let checkValueBatuk = $('.form-batuk_pilek_nyeri_tenggorokan').find('input').prop('checked');
    let checkValueSesak = $('.form-sesak_napas').find('input').prop('checked');
    checkGejalaSuspek(checkValue, checkValueBatuk, checkValueSesak)

    let checkValuePositive = $('.swab-positif').find('input').prop('checked');
    if (checkValuePositive) {
        $('.form-tgl-swab-positif').removeClass('hidden');
    }else{
        $('.form-tgl-swab-positif').addClass('hidden');
        $('#skriningcovidform-tgl_swab_positif').val('')
    }
    checkSwabKonfirmasi(checkValuePositive)

    let checkValueNegative = $('.swab-negatif').find('input').prop('checked');
    if (checkValueNegative) {
        $('.form-tgl-swab-negatif').removeClass('hidden');
    }else{
        $('.form-tgl-swab-negatif').addClass('hidden');
        $('#skriningcovidform-tgl_swab_negatif').val('')
    }

});
$(document).on('change','.swab-positif',function () {
    let checkValue = $(this).find('input').prop('checked');
    if (checkValue) {
        $('.form-tgl-swab-positif').removeClass('hidden');
    }else{
        $('.form-tgl-swab-positif').addClass('hidden');
        $('#skriningcovidform-tgl_swab_positif').val('')
    }
    checkSwabKonfirmasi(checkValue)
})

$(document).on('change','.swab-negatif',function () {
    let checkValue = $(this).find('input').prop('checked');
    if (checkValue) {
        $('.form-tgl-swab-negatif').removeClass('hidden');
    }else{
        $('.form-tgl-swab-negatif').addClass('hidden');
        $('#skriningcovidform-tgl_swab_negatif').val('')
    }
})

$(document).on('change','.form-demam',function () {
    let checkValue = $(this).find('input').prop('checked');
    let checkValueBatuk = $('.form-batuk_pilek_nyeri_tenggorokan').find('input').prop('checked');
    let checkValueSesak = $('.form-sesak_napas').find('input').prop('checked');
    checkGejalaSuspek(checkValue, checkValueBatuk, checkValueSesak)
})

$(document).on('change','.form-batuk_pilek_nyeri_tenggorokan',function () {
    let checkValue = $(this).find('input').prop('checked');
    let checkValueDemam = $('.form-demam').find('input').prop('checked');
    let checkValueSesak = $('.form-sesak_napas').find('input').prop('checked');
    checkGejalaSuspek(checkValueDemam, checkValue, checkValueSesak)
})

$(document).on('change','.form-sesak_napas',function () {
    let checkValue = $(this).find('input').prop('checked');
    let checkValueBatuk = $('.form-batuk_pilek_nyeri_tenggorokan').find('input').prop('checked');
    let checkValueDemam = $('.form-demam').find('input').prop('checked');
    checkGejalaSuspek(checkValueDemam, checkValueBatuk, checkValue)
})

$(document).on('change','.faktor-resiko',function () {
    let checkValue = $('.form-demam').find('input').prop('checked');
    let checkValueBatuk = $('.form-batuk_pilek_nyeri_tenggorokan').find('input').prop('checked');
    let checkValueSesak = $('.form-sesak_napas').find('input').prop('checked');
    checkGejalaSuspek(checkValue, checkValueBatuk, checkValueSesak)
})

function checkGejalaSuspek(demam, batuk, sesak){
    var resultKonfirmasi = false;
    if ( (demam && batuk && sesak) || (demam && batuk) || (demam && sesak)) {
        resultKonfirmasi = true;
    }

    var resultFaktorResiko = false;
    $('.faktor-resiko').each(function(index, obj){
        if ($(obj).find('input').prop('checked')) {
            resultFaktorResiko = true
        }
    });

    if (resultKonfirmasi && resultFaktorResiko) {
        $('.suspek-check').removeClass('hidden');
        $("input[name='SkriningCovidForm[suspek]']").val(1)
    }else{
        $('.suspek-check').addClass('hidden');
        $("input[name='SkriningCovidForm[suspek]']").val(null)
    }
}

function checkSwabKonfirmasi(checkValue){
    if (checkValue) {
        $('.terkonfirmasi-check').removeClass('hidden');
        $("input[name='SkriningCovidForm[terkonfirmasi]']").val(1)
    }else{
        $('.terkonfirmasi-check').addClass('hidden');
        $("input[name='SkriningCovidForm[terkonfirmasi]']").val(null)
    }
}

$("#submit-skrining-covid").click(function (e) { 
    e.preventDefault();
    var _data = $('#form-skrining-covid').serializeArray();
    $().docoForm('click', {
        method: "POST",
        url: 'rajal/informasi/save-skrining-covid',
        data: _data,
        success: function (data) {
            var resData = data.response.data;
            $('#tab-skrining-covid').trigger('click')
        }
    });
});

$('#btn-print-skrining-covid').on('click', function(){
    let url = $(this).attr('data-target');
    window.open(url, '_blank');
})
