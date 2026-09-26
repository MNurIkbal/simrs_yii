$(document).on("change","#bb_saatini", function (e) {
    e.preventDefault();
    docoHelper.convertToDecimal(this, '.', ',' );
});
$('.hitung_bb').on('change', function() {
    var saatini = $('#bb_saatini').val()
    var biasanya = $('#bb_biasanya').val()

    if (saatini && biasanya) {
        // var selisih = Math.abs(biasanya - saatini);
        var int_selisih = Math.abs(biasanya.replace(',','.') - saatini.replace(',','.'));
        var int_prosen = ((int_selisih / biasanya.replace(',','.') ) * 100).toFixed(2);
        var str_prosen = int_prosen.replace('.',',');
        $('#penurunan_bb').val(str_prosen);
        $('#label_penurunan_bb').val(str_prosen);
        $('#asuhangiziform-penurunan_bb').val(int_prosen);

    } else {
        $('#penurunan_bb').val('');
        $('#label_penurunan_bb').val('');
        $('#asuhangiziform-penurunan_bb').val('');
    }
})

$('input[name="AsuhanGiziForm[pengalaman_diet]"]').on('change', function(){
    if($(this).val() == 1){
        $('.pengalaman_diet').removeClass('hidden');
        $('#asuhangiziform-pengalaman_diet_desc').val('')
    }else{
        if (!$('.pengalaman_diet').hasClass('hidden')) {
            $('.pengalaman_diet').addClass('hidden');
        }
        $('#asuhangiziform-pengalaman_diet_desc').val('')
    }
})

$('.hitung_imt').on('change', function() {
    var saatini = $('#bb_saatini').val()
    var pb_tb = $('#pb_tb').val()

    if (saatini && pb_tb) {
        var berat = saatini.replace(',','.');
        var tinggi = pb_tb / 100; // convert jadi meter

        var int_imt = (berat / (tinggi * tinggi)).toFixed(2);
        var str_imt = int_imt.replace('.', ',');
        var imt_definisi = '';
        $.each(listBmi, function(index, val) {
            if (int_imt >= parseFloat(val.bmi_minimum) && int_imt <= parseFloat(val.bmi_maksimum)) {
                imt_definisi = val.bmi_defenisi;
                return false;
            }
        });

        $('#asuhangiziform-imt').val(str_imt);
        $('#label_imt').val(str_imt);
        $('#status_gizi').val(imt_definisi);
        $('#label_status_gizi').val(imt_definisi);
        $('#asuhangiziform-status_gizi').val(imt_definisi);

    } else {
        $('#asuhangiziform-imt').val('');
        $('#label_imt').val('');
        $('#status_gizi').val('');
        $('#asuhangiziform-status_gizi').val('');
    }
})

$('.hitung_tekanan_darah').on('change', function() {
    var mm = $('#mm').val();
    var hg = $('#hg').val();
    var tekanan_darah = '';
    if (mm && hg) {
        $('#tekanan_darah_mmhg').val(mm + '/' + hg)
        $('#asuhangiziform-tekanan_darah_mmhg').val(mm + '/' + hg)
        $('#label_tekanan_darah_mmhg').val(mm + '/' + hg)
        $.each(listTekananDarah, function(index, val) {
             if ((mm >= parseInt(val.sistolik_min) && mm < parseInt(val.sistolik_max)) 
                || (hg >= parseInt(val.diastolik_min) && hg < parseInt(val.diastolik_max))) {
                tekanan_darah = val.klasifikasitekanadarah
             }
        });
        $('#tekanan_darah_kondisi').val(tekanan_darah)
        $('#asuhangiziform-tekanan_darah_kondisi').val(tekanan_darah)
        $('#label_tekanan_darah_kondisi').val(tekanan_darah)
    } else {
        $('#tekanan_darah_mmhg').val('')
        $('#asuhangiziform-tekanan_darah_mmhg').val('')
        $('#label_tekanan_darah_mmhg').val('')
        $('#tekanan_darah_kondisi').val('')
        $('#asuhangiziform-tekanan_darah_kondisi').val('')
        $('#label_tekanan_darah_kondisi').val('')
    }
})

$('#form-asuhan-gizi').on('submit', function(e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    // console.log(data); return false;
    $(this).docoForm('submit',{
        data: data,
        success : function(response) {
            var asuhangizi_id = response.response.asuhangizi_id;

            // Pnotify
            PNotify.prototype.options.styling = "bootstrap3";
            (new PNotify({
                title: "Berhasil",
                text: "Data berhasil disimpan, apakah Anda ingin melakukan cetak?",
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: "success",
                buttons: {
                    closer: false,
                    sticker: false
                },
                hide: false,
                confirm: {
                    confirm: true
                },
                history: {
                    history: false
                }
            })).get().on('pnotify.confirm', function() {
                // Print
                window.open("/gizi/asesmen-gizi/cetak-asuhan-gizi?id="+pendaftaran_id+"&asuhangizi_id="+asuhangizi_id);
            }).on('pnotify.cancel', function() {

            });
            $('#tab-asuhan-gizi').click();
        }
    }); 
})

$('.data-reset').on('click', function() {
    $('#form-asuhan-gizi').trigger("reset");
    $('.input-tags').tagsinput('removeAll');

    if($('input[name="AsuhanGiziForm[pengalaman_diet]"]').val() == 1){
        $('.pengalaman_diet').removeClass('hidden');
        $('#asuhangiziform-pengalaman_diet_desc').val('')
    }else{
        if (!$('.pengalaman_diet').hasClass('hidden')) {
            $('.pengalaman_diet').addClass('hidden');
        }
        $('#asuhangiziform-pengalaman_diet_desc').val('')
    }
});