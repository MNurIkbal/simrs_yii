$(document).on("change","#bb_biasanya", function (e) {
    e.preventDefault();
    docoHelper.valToDecimal(this, 2);
});

$(document).on("change","#bb_saatini", function (e) {
    e.preventDefault();
    docoHelper.valToDecimal(this, 2);
});
$('.hitung_bb').on('change', function() {
    var saatini = $('#bb_saatini').val()
    var biasanya = $('#bb_biasanya').val()
    if (saatini && biasanya) {
        // Hitung Selisih bb
        var int_selisih = Math.abs(biasanya.replace(',','.') - saatini.replace(',','.'));
        int_selisih = int_selisih.toFixed(2);
        str_selisih = int_selisih.replace('.', ',');
        $('.hasil_hitung_kg').val(str_selisih);
        $('#asesmenawalgiziform-perubahan_kg').val(int_selisih);

        // Hitung selisih persen
        var int_prosen = ((int_selisih / biasanya.replace(',','.') ) * 100).toFixed(2);
        var str_prosen = int_prosen.replace(/\./g, ',');
        $('.hasil_hitung_persen').val(str_prosen);
        $('#asesmenawalgiziform-perubahan_persen').val(int_prosen);
        $.each(listPerubahan, function(index, el) {
            if (int_prosen >= parseInt(el.lookup_value)) {
                $('.hasil_hitung_perubahan').val(el.lookup_name);
                $('#asesmenawalgiziform-perubahan_hasil').val(el.lookupkeperawatan_id);
                return false;
            }
        });

    } else {
        $('.hasil_hitung_kg').val('');
        $('.hasil_hitung_persen').val('');
        $('.hasil_hitung_perubahan').val('');
        $('#asesmenawalgiziform-perubahan_hasil').val('');
    }
})

$('input[name="AsesmenAwalGiziForm[sumberdata]"]').on('change', function(){
    if($(this).val() == 1){
        if (!$('.sumber-dari').hasClass('hidden')) {
            $('.sumber-dari').addClass('hidden');
        }
        $('#asesmenawalgiziform-sumberdata_dari').val('')
    }else{
        $('.sumber-dari').removeClass('hidden');
        $('#asesmenawalgiziform-sumberdata_dari').val('')
    }
})

$('#form-sga').docoForm('submit',{
    success : function(data) {
        $('#tab-asuhan-gizi').removeClass('hidden');
        $('#tab-sga').click();
    }
});


/*$('#form-sga').on('submit', function(e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    // console.log(data); return false;
    $(this).docoForm('submit',{
        data: data,
        // url: $('#form-sga').attr('action'),
        success : function(response) {
            $('#tab-asuhan-gizi').removeClass('hidden');
            $('#tab-sga').click();
        }
    });
})*/
