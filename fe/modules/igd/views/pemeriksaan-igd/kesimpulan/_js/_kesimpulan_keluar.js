/*----------  Gcs start  ----------*/
var nilaiGcsEye = 0
var nilaiGcsVerbal = 0
var nilaiGcsMotorik = 0

$(document).ready(function() {
    hitungGcs();
});

var hitungGcs = function() {
    nilaiGcsEye = nilaiGcsEye ? nilaiGcsEye : parseInt($(".gcs_eye").find(':selected').attr('data-nilai'));
    nilaiGcsVerbal = nilaiGcsVerbal ? nilaiGcsVerbal : parseInt($(".gcs_verbal").find(':selected').attr('data-nilai'));
    nilaiGcsMotorik = nilaiGcsMotorik ? nilaiGcsMotorik : parseInt($(".gcs_motorik").find(':selected').attr('data-nilai'));

    if ($("#kesimpulankeluarform-is_kapitis").is(":checked")) {
        var is_kapitis = 1;
    } else {
        var is_kapitis = 0;
    }

    nilaiGcsEye      = isNaN(parseInt(nilaiGcsEye)) ? 0 : parseInt(nilaiGcsEye);
    nilaiGcsVerbal   = isNaN(parseInt(nilaiGcsVerbal)) ? 0 : parseInt(nilaiGcsVerbal);
    nilaiGcsMotorik  = isNaN(parseInt(nilaiGcsMotorik)) ? 0 : parseInt(nilaiGcsMotorik);
    
    let nilaiGcs = nilaiGcsEye + nilaiGcsVerbal + nilaiGcsMotorik;
    $('.nilai_gcs').val(nilaiGcs);
    let hasil;
    // var is_kapitis = $('#asesmenperawatrdform-is_kapitis').is(":checked");

    // $.each(dataGcs, function(key, value){
    //     if(nilaiGcs >= value['gcs_nilaimin'] && nilaiGcs <= value['gcs_nilaimax'] && value.is_kapitis == is_kapitis){
    //         $('.hasil_gcs').val(nilaiGcs);
    //         $('.nilai_gcs').val(value.gcs_nama)
    //         return false;
    //     }
    // });
    $.each(dataGcs, function (key, value) {
        if (nilaiGcs >= value['gcs_nilaimin'] && nilaiGcs <= value['gcs_nilaimax'] && value['is_kapitis'] == is_kapitis) {
            $('.hasil_gcs').val(nilaiGcs);
            $('.hasil_gcs').val(value.gcs_nama)
            return false
        }
    })
}

$(".gcs_eye").on("change", function(e) { 
    nilaiGcsEye = isNaN(parseInt($('option:selected', this).attr('data-nilai'))) ? 0 : parseInt($('option:selected', this).attr('data-nilai'))
    hitungGcs();
});

$(".gcs_verbal").on("change", function(e) {
    nilaiGcsVerbal = isNaN(parseInt($('option:selected', this).attr('data-nilai'))) ? 0 : parseInt($('option:selected', this).attr('data-nilai'))
    hitungGcs();
});

$(".gcs_motorik").on("change", function(e) {
    nilaiGcsMotorik = isNaN(parseInt($('option:selected', this).attr('data-nilai'))) ? 0 : parseInt($('option:selected', this).attr('data-nilai'))
    hitungGcs();
});

$("#kesimpulankeluarform-is_kapitis").on("change", function(e) {
    // if($(this).is(":checked")) {
    //     // $(this).attr("checked", false);
    //     // $(this).val(0);
    //     $("input[name='KesimpulanKeluarForm[is_kapitis]']").val(1);
    // } else {
    //     // $(this).attr("checked", true);
    //     // $(this).val(1);
    //     $("input[name='KesimpulanKeluarForm[is_kapitis]']").val(0);
    // }
    hitungGcs();
});
/*----------  Gcs end  ----------*/
