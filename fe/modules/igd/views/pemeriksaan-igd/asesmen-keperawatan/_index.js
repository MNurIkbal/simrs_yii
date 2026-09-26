$(" .select2 ").select2();
var loading_spinner = '<i class="icon-spinner4 spinner position-center form-control-feedback spinner-text" style="display: block;"></i>';

$(document).ready(function(){
    $('.td_field').keyup();
    $('.imt_field').keyup();
    nilaiGcsEye = typeof $(".gcs_eye").find(':selected').attr('data-nilai') !== 'undefined' ? parseInt($(".gcs_eye").find(':selected').attr('data-nilai')) : 0;
    nilaiGcsVerbal = typeof $(".gcs_verbal").find(':selected').attr('data-nilai') !== 'undefined' ? parseInt($(".gcs_verbal").find(':selected').attr('data-nilai')) : 0;
    nilaiGcsMotorik = typeof $(".gcs_motorik").find(':selected').attr('data-nilai') !== 'undefined' ? parseInt($(".gcs_motorik").find(':selected').attr('data-nilai')) : 0;
    hitungGcs()
    $('.field-asesmenperawatrdform-is_alergiobat').hide();
    $('.field-asesmenperawatrdform-alergi_obat').hide();
    $('.field-asesmenperawatrdform-is_alergilainnya').hide();
    $('.field-asesmenperawatrdform-alergi_lainnya').hide();
    $('.field-asesmenperawatrdform-lokasi_nyeri').hide();

    if($("input:radio[name='AsesmenPerawatRDForm[is_alergi]']:checked").val() == '1'){
        $('.field-asesmenperawatrdform-is_alergiobat').show();
        $('.field-asesmenperawatrdform-alergi_obat').show();
        $('.field-asesmenperawatrdform-is_alergilainnya').show();
        $('.field-asesmenperawatrdform-alergi_lainnya').show();
    }
    $('#asesmenperawatrdform-is_alergi').change(function(){
        var sourceVal = $("input:radio[name='AsesmenPerawatRDForm[is_alergi]']:checked").val();
        if(sourceVal == '1'){
            $('.field-asesmenperawatrdform-is_alergiobat').show();
            $('.field-asesmenperawatrdform-alergi_obat').show();
            $('.field-asesmenperawatrdform-is_alergilainnya').show();
            $('.field-asesmenperawatrdform-alergi_lainnya').show();
        }else{
            $('.field-asesmenperawatrdform-is_alergiobat').hide();
            $('.field-asesmenperawatrdform-alergi_obat').hide();
            $('.field-asesmenperawatrdform-is_alergilainnya').hide();
            $('.field-asesmenperawatrdform-alergi_lainnya').hide();
        }
    });

    
    if($("input:radio[name='AsesmenPerawatRDForm[is_alergi]']:checked").val() == '1'){
        $('.field-asesmenperawatrdform-lokasi_nyeri').show();
    };
    $('#asesmenperawatrdform-is_nyeri').change(function(){
        var sourceVal = $("input:radio[name='AsesmenPerawatRDForm[is_nyeri]']:checked").val();
        if(sourceVal == '1'){
            $('.field-asesmenperawatrdform-lokasi_nyeri').show();
        }else{
            $('.field-asesmenperawatrdform-lokasi_nyeri').hide();
        }
    });

    $.each(dataPenyakit, function(k,v){
        var option = new Option(v.text, v.id+"_"+v.text, true, true);
        $("#asesmenperawatrdform-r_penyakitdahulu").append(dataPenyakit).trigger("change");
    });
});

/*----------  Gcs start  ----------*/
var nilaiGcsEye = 0
var nilaiGcsVerbal = 0
var nilaiGcsMotorik = 0
var hitungGcs = function () {
    let nilaiGcs = nilaiGcsEye + nilaiGcsVerbal + nilaiGcsMotorik;
    $('.nilai_gcs').val(nilaiGcs);
    let hasil;
    var is_kapitis = $('#asesmenperawatrdform-is_kapitis').is(":checked");
    $.each(dataGcs, function (key, value) {
        if (nilaiGcs >= value['gcs_nilaimin'] && nilaiGcs <= value['gcs_nilaimax'] && value['is_kapitis'] == is_kapitis) {
            $('.hasil_gcs').val(value.gcs_nama)
            return false
        }
    })
}

$('#asesmenperawatrdform-is_kapitis').on("change", function (e) {
    hitungGcs();
});

$(".gcs_eye").on("change", function (e) {
    nilaiGcsEye = isNaN(parseInt($(this).find(':selected').attr('data-nilai'))) ? 0 : parseInt($(this).find(':selected').attr('data-nilai'))
    hitungGcs()
});
$(".gcs_verbal").on("change", function (e) {
    nilaiGcsVerbal = isNaN(parseInt($(this).find(':selected').attr('data-nilai'))) ? 0 : parseInt($(this).find(':selected').attr('data-nilai'))
    hitungGcs()
});
$(".gcs_motorik").on("change", function (e) {
    nilaiGcsMotorik = isNaN(parseInt($(this).find(':selected').attr('data-nilai'))) ? 0 : parseInt($(this).find(':selected').attr('data-nilai'))
    hitungGcs()
});

/*----------  Gcs end  ----------*/


/*----------  Tekanan Darah Start  ----------*/
$(".td_field").keyup(function(e){
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
         // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
         // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
             // return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }

    let systol = $('#asesmenperawatrdform-td_systolic').val() ? parseInt($('#asesmenperawatrdform-td_systolic').val()) : 0;
    let diastol = $('#asesmenperawatrdform-td_diastolic').val() ? parseInt($('#asesmenperawatrdform-td_diastolic').val()) : 0;
    let hasil_td = $("#asesmenperawatrdform-hasil_td");

    let keterangan_td = systol +"/"+ diastol;

    let pendaftaran_id = $('#asesmenperawatrdform-pendaftaran_id').val();
    let nilai = keterangan_td;

    var hasil;
   
    $.ajax({
        url: '/igd/pemeriksaan-igd/get-hasil-td?pendaftaran_id='+ pendaftaran_id +'&nilai='+ nilai,
        type: 'get',
        dataType: 'JSON',
        beforeSend : function() {
            $('.hasil-td').after(loading_spinner);
        },
        success: function(data, text, xhr){
            if(xhr.status == 200){
                hasil = data.hasil
            }
        }
    }).done(function() {
        var blank = '-';
        if((systol == 0) && diastol == 0){
            hasil_td.val(blank);
        }else{
            hasil_td.val(hasil);
        }
        $(".spinner-text").remove();
    });
});
/*----------  Tekanan Darah end  ----------*/


/*----------  Berat Badan dan IMT Start  ----------*/
$(".imt_field").keyup(function(e){
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
         // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
         // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
             // return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }

    let tb = $('#asesmenperawatrdform-tinggi_badan').val() ? parseFloat($('#asesmenperawatrdform-tinggi_badan').val()) : null;
    let bb = $('#asesmenperawatrdform-berat_badan').val() ? parseFloat($('#asesmenperawatrdform-berat_badan').val()) : null;
    let kategori_imt = $("#asesmenperawatrdform-ket_imt");
    let field_imt = $("#asesmenperawatrdform-imt");
    let field_bbideal = $("#asesmenperawatrdform-bb_ideal");
    let bbideal = 0.0;
    let imt = 0.0;
    let imt_kategori = '';

    // hitung bmi / imt
    if (bb && tb) {
        imt = (bb / ((tb/100) * (tb/100))).toFixed(2);
        
        $.each(data_bmi, function( index, value ) {
            if(parseFloat(imt) >= parseFloat(value.bmi_minimum) && parseFloat(imt) <= parseFloat(value.bmi_maksimum)){
                imt_kategori = value.bmi_defenisi;
                return false;
            }
        });
        
        // hitung berat badan ideal
        if (jeniskelamin == lakilaki){
            bbideal = parseFloat((tb - 100) - (0.1 * (tb-100))).toFixed(2);
        }else{
            bbideal = parseFloat((tb - 100) - (0.15 * (tb-100))).toFixed(2);
        }
    }

    field_imt.val(imt);
    kategori_imt.val(imt_kategori);
    field_bbideal.val(bbideal);
});
/*----------  Berat Badan dan IMT end  ----------*/

$("#btn-save-asesmen-perawat").on("click", function(event) {
    event.preventDefault();
    $(this).docoForm('click', {
        url: '/igd/pemeriksaan-igd/save-asesment',
        data: $("#form-asesmen-keperawatan").serializeArray(),
        success : function(response) {
            $("#tab-asesmen-keperawatan").trigger('click');
        }
    });
});