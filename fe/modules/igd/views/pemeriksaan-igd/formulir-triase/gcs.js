
/*----------  Gcs start  ----------*/
var nilaiGcsEye = 0
var nilaiGcsVerbal = 0
var nilaiGcsMotorik = 0
var nilaiGcs = 0
var hitungGcs = function () {

    let nilaiGcs = nilaiGcsEye + nilaiGcsVerbal + nilaiGcsMotorik;
    $('.nilai_gcs').val(nilaiGcs);
    let hasil;
    var is_kapitis = $('#triasegcsform-is_kapitis').is(":checked");
    $.each(dataGcs, function(key, value){
        if(nilaiGcs >= value['gcs_nilaimin'] && nilaiGcs <= value['gcs_nilaimax'] && value['is_kapitis'] == is_kapitis){
            $('.hasil_gcs').val(value.gcs_nama)
            return false
        }
    })

}

$('#triasegcsform-is_kapitis').on("change", function(e) {
    hitungGcs();
});

$(".gcs_eye").on("change", function(e) { 
    nilaiGcsEye = isNaN(parseInt($(this).find(':selected').attr('data-nilai'))) ? 0 : parseInt($(this).find(':selected').attr('data-nilai'))
    hitungGcs()
});
$(".gcs_verbal").on("change", function(e) { 
    nilaiGcsVerbal = isNaN(parseInt($(this).find(':selected').attr('data-nilai'))) ? 0 : parseInt($(this).find(':selected').attr('data-nilai'))
    hitungGcs()
});
$(".gcs_motorik").on("change", function(e) { 
    nilaiGcsMotorik = isNaN(parseInt($(this).find(':selected').attr('data-nilai'))) ? 0 : parseInt($(this).find(':selected').attr('data-nilai'))
    hitungGcs()
});

/*----------  Gcs end  ----------*/

$(() => {
    $(".gcs_eye").val($("#gcseye_id-form").val()).change();
    $(".gcs_verbal").val($("#gcsverbal_id-form").val()).change();
    $(".gcs_motorik").val($("#gcsmotorik_id-form").val()).change();
    $('#triasegcsform-is_kapitis').prop( "checked", $("#is_kapitis-form").val() == 1 ? true : false );
    nilaiGcsEye     = isNaN(parseInt($(".gcs_eye").find(':selected').attr('data-nilai'))) ? 0 : parseInt($(".gcs_eye").find(':selected').attr('data-nilai'))
    nilaiGcsVerbal  = isNaN(parseInt($(".gcs_verbal").find(':selected').attr('data-nilai'))) ? 0 : parseInt($(".gcs_verbal").find(':selected').attr('data-nilai'))
    nilaiGcsMotorik = isNaN(parseInt($(".gcs_motorik").find(':selected').attr('data-nilai'))) ? 0 : parseInt($(".gcs_motorik").find(':selected').attr('data-nilai'))
    hitungGcs();

    $('#btn-hitung').on('click',function(e){
        $("#gcseye_id-form").val($("#triasegcsform-gcseye_id").val())
        $("#gcsverbal_id-form").val($("#triasegcsform-gcsverbal_id").val())
        $("#gcsmotorik_id-form").val($("#triasegcsform-gcsmotorik_id").val())
        $("#hasil_gcs-form").val($('#triasegcsform-jumlah_gcs').val());
        $("#is_kapitis-form").val($('#triasegcsform-is_kapitis').is(":checked") ? 1 : 0);
    });
})