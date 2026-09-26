/*
* @Author: rizfardi@docotel.com
* @Date:   2018-03-21 10:45:56
* @Last Modified by:   Doconb-Bandung
* @Last Modified time: 2018-11-22 14:58:42
*/

/*================================
=            fisik            =
================================*/
var loading_spinner = '<i class="icon-spinner4 spinner position-center form-control-feedback spinner-text" style="display: block;"></i>';
// define array nilai gcs
var arr_nilai_gcs = [];
var sumbuX = 0;
var sumbuY = 0;
var dataAnatomiTable = []


$(".date").pickadate({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY"
    }
});

$(".input-tags").tagsinput();
var $inputKeadaanumum;
if($("#pemeriksaanfisikform-keadaanumum").prop("disabled") === false) {
    $inputKeadaanumum = $("#pemeriksaanfisikform-keadaanumum").tagsinput('input');
} else {
    $inputKeadaanumum = $("#pemeriksaanfisikform-keadaanumum").tagsinput({'interactive':false});
    $('.tagsinput').find('a').remove();
}
// $inputKeadaanumum.$container.attr('id','container-periksafisik-keadaanumum');
// $inputKeadaanumum.$input.attr('id','input-periksafisik-keadaanumum');
// $inputKeadaanumum.addClass('periksafisik-keadaanumum');


// $(".gcs_select2").on("change", function(e) {
//     var data_gcs = $(this).select2("data");
//     var loading_spinner = '<i class="icon-spinner4 spinner position-center form-control-feedback spinner-text" style="display: block;"></i>';
//     var _this = $(this);

//     // console.log(data_tindakan);
//     var metodegcs_id = data_gcs[0].id ? data_gcs[0].id : null;
//     var hasil_gcs = $('#pemeriksaanfisikform-gcs_hasil_metode');
//     var kategori_gcs = $('#pemeriksaanfisikform-gcs_kategori');
//     var gcs_id_field = $('#pemeriksaanfisikform-gcs_id');
//     var curval = hasil_gcs.val() ? hasil_gcs.val() : 0;
//     let nilaikategori_gcs = kategori_gcs.val() ? kategori_gcs.val() : '';
//     let gcs_id = null;

//     let is_kapitis = 0;
//     let type = _this.data("type");

//     if (arr_nilai_gcs[type] != null){
//         curval = curval - arr_nilai_gcs[type];
//     }
//     let nilai_gcs = curval;

//     var url = "/rajal/pemeriksaan/get-nilai-gcs?metodegcs_id="+metodegcs_id+"&curval="+curval+"&kategori="+nilaikategori_gcs+"&is_kapitis="+is_kapitis;
//     $.ajax({
//         url: url,
//         type: "GET",
//         dataType: "json",
//         beforeSend : function() {
//             hasil_gcs.after(loading_spinner);
//             kategori_gcs.after(loading_spinner);
//         },
//         success : function(response) {
//             if (response.status = 200){
//                 let data_gcs = response.data;

//                 nilai_gcs = data_gcs.nilai;
//                 nilaikategori_gcs = data_gcs.kategori;
//                 gcs_id = data_gcs.gcs_id;

//                 arr_nilai_gcs[type] = nilai_gcs;
//             }else{
//                 console.log(response.message);
//             }
//         }
//     }).done(function() {
//         hasil_gcs.val(nilai_gcs);
//         kategori_gcs.val(nilaikategori_gcs);
//         gcs_id_field.val(gcs_id);
//         $(".spinner-text").remove();
//     });
// });

/*----------  Gcs start  ----------*/
var nilaiGcsEye = 0
var nilaiGcsVerbal = 0
var nilaiGcsMotorik = 0
var nilaiGcs = 0
var hitungGcs = function () {
    nilaiGcsEye = parseInt($(".gcs_eye").find(':selected').attr('data-nilai'));
    nilaiGcsVerbal = parseInt($(".gcs_verbal").find(':selected').attr('data-nilai'));
    nilaiGcsMotorik = parseInt($(".gcs_motorik").find(':selected').attr('data-nilai'));
    nilaiGcs = nilaiGcsEye + nilaiGcsVerbal + nilaiGcsMotorik;
    $('#pemeriksaanfisikform-gcs_hasil_metode').val(isNaN(nilaiGcs) ? '' : nilaiGcs);
    $('#gcs_hasil_metode').val(isNaN(nilaiGcs) ? '' : nilaiGcs);
    let hasil;
    var is_kapitis = $('#pemeriksaanfisikform-gcs_is_kapitis').is(":checked");
    $.each(dataGcs, function (key, value) {
        if (nilaiGcs >= value['gcs_nilaimin'] && nilaiGcs <= value['gcs_nilaimax'] && value['is_kapitis'] == is_kapitis) {
            $('#pemeriksaanfisikform-gcs_id').val(value.gcs_id)
            $('.hasil_gcs').val(value.gcs_nama)
            return false
        }
    })
}

$('#pemeriksaanfisikform-gcs_is_kapitis').on("change", function (e) {
    hitungGcs();
});

$(".gcs_eye").on("change", function (e) {
    hitungGcs()
});
$(".gcs_verbal").on("change", function (e) {
    hitungGcs()
});
$(".gcs_motorik").on("change", function (e) {
    hitungGcs()
});

// $('#pemeriksaanfisikform-gcs_is_kapitis').on('change', function(e){
//     let field_gcs_kategori = $("#pemeriksaanfisikform-gcs_kategori");
//     if (!field_gcs_kategori.val().length){
//         return false;
//     }

    //code
// });

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

    let systol = typeof $('#pemeriksaanfisikform-td_systolic').val() != 'undefined' ? $('#pemeriksaanfisikform-td_systolic').val() : $('#td_systolic').val()
    let diastol = typeof $('#pemeriksaanfisikform-td_diastolic').val() != 'undefined' ? $('#pemeriksaanfisikform-td_diastolic').val() : $('#td_diastolic').val()
    let kategori_td = typeof $('#pemeriksaanfisikform-tekanandarah') != 'undefined' ? $('#pemeriksaanfisikform-tekanandarah') : $('#tekanandarah')
    let kategori_td_keterangan = typeof $("#pemeriksaanfisikform-tekanandarah_kategori") != 'undefined' ? $("#pemeriksaanfisikform-tekanandarah_kategori") : $('#tekanandarah_kategori');
    let field_klasifikasitekanandarah = typeof $("#pemeriksaanfisikform-klasifikasitekanandarah_id") != 'undefined' ? $("#pemeriksaanfisikform-klasifikasitekanandarah_id") : $('#klasifikasitekanandarah_id');
    let map_td = typeof $("#pemeriksaanfisikform-meanarteripressure").val() != 'undefined' ? $("#pemeriksaanfisikform-meanarteripressure") : $('#meanarteripressure');
    let map_td_kategori = $(".kategori-map");
    
    systol = systol ? parseInt(systol) : 0;
    diastol = diastol ? parseInt(diastol) : 0;
    
    let kategori = '';
    let kategori_sistol = '';
    let kategori_diastol = '';
    let map = 0.0;
    let map_kategori = '';
    let tekanandarah = null;
    let klasifikasitekanandarah_id = null;
    let systol_urutan = 0;
    let diastol_urutan = 0;

    let keterangan_td = systol +"/"+ diastol;

    let td_match = 0;

    let pendaftaran_id = $('#pemeriksaanfisikform-pendaftaran_id').val();
    if(typeof pendaftaran_id == 'undefined') {
        pendaftaran_id = pendaftaranId
    }
    
    let nilai = keterangan_td;

    var hasil;

    $.ajax({
        url: '/rajal/pemeriksaan/get-hasil-td?pendaftaran_id='+ pendaftaran_id +'&nilai='+ nilai,
        type: 'get',
        dataType: 'JSON',
        beforeSend : function() {
            $('.hasil-td').after(loading_spinner);
        },
        success: function(data, text, xhr){
            if(xhr.status == 200){
                hasil = data.hasil
                klasifikasitekanandarah_id = data.klasifikasitekanandarah_id
            }
        }
    }).done(function() {
        // $('.hasil-td').val( hasil )
        var blank = '-';
        if((systol == 0) && diastol == 0){
            kategori_td_keterangan.val(blank);
        }else{
            kategori_td_keterangan.val(hasil);
        }
        field_klasifikasitekanandarah.val(klasifikasitekanandarah_id);
        $(".hasil-td").val(hasil)
        $(".spinner-text").remove();
    });

    /*$.each(data_tekanandarah, function( index, value ) {
        if ((systol >= value['sistolik_min']) && (systol <= value['sistolik_max'])){
            kategori_sistol = value['klasifikasitekanadarah'];
            klasifikasitekanandarah_id = value['klasifikasitekanadarah_id'];
            systol_urutan = value['urutan'];
            td_match++;
        }

        if ((diastol >= value['diastolik_min']) && (diastol <= value['diastolik_max'])){
            kategori_diastol = value['klasifikasitekanadarah'];
            diastol_urutan = value['urutan'];

            if (diastol_urutan > systol_urutan){
                klasifikasitekanandarah_id = value['klasifikasitekanadarah_id'];
            }

            td_match++;
        }

        if (td_match == 2){
            if (kategori_sistol == kategori_diastol){
                kategori = kategori_sistol;
            }else{
                kategori = kategori_sistol +" "+ kategori_diastol;
            }

            return false;
        }
    });*/

    kategori_td.val(keterangan_td);
    // kategori_td_keterangan.val(kategori);
    // field_klasifikasitekanandarah.val(klasifikasitekanandarah_id);

    // perhitungan MAP
    map = parseFloat((systol + (2 * diastol)) / 3).toFixed(2);
    
    if (map >= 70 && map <= 110){
        map_kategori = 'Normal';
    }else if (map <= 60){
        map_kategori = 'Berbahaya'
    }
    
    map_td.val(map.toString().replace('.', ','));
    map_td_kategori.text(map_kategori);
});

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

    let tb = typeof $('#pemeriksaanfisikform-tinggibadan_cm').val() != 'undefined' ? $('#pemeriksaanfisikform-tinggibadan_cm').val() : $('#tinggibadan_cm').val()
        tb = tb ? parseFloat(tb.replace(',', '.')) : null;

    let bb = typeof $('#pemeriksaanfisikform-beratbadan_kg').val() != 'undefined' ? $('#pemeriksaanfisikform-beratbadan_kg').val() : $('#beratbadan_kg').val()
        bb = bb ? parseFloat(bb.replace(',', '.')) : null;

    let kategori_imt = typeof $('#pemeriksaanfisikform-imt_kategori').val() != 'undefined' ? $('#pemeriksaanfisikform-imt_kategori') : $('#imt_kategori')
    let bodymassindex_id = typeof $('#pemeriksaanfisikform-bodymassindex_id').val() != 'undefined' ? $('#pemeriksaanfisikform-bodymassindex_id') : $('#bodymassindex_id') 
    let field_imt = typeof $('#pemeriksaanfisikform-imt').val() != 'undefined' ? $('#pemeriksaanfisikform-imt') : $('#imt') 
    let field_bbideal = typeof $('#pemeriksaanfisikform-bb_ideal').val() != 'undefined' ? $('#pemeriksaanfisikform-bb_ideal') : $('#bb_ideal') 
    
    let bbideal = 0.0;
    let imt = 0.0;
    let imt_kategori = '';
    let bodymassindex = '';

    // hitung bmi / imt
    if (bb && tb) {
        imt = (bb / ((tb/100) * (tb/100))).toFixed(2);

        $.each(data_bmi, function( index, value ) {
            /*if ((imt >= value['bmi_minimum']) && (imt <= value['bmi_maksimum'])){
                imt_kategori = value['bmi_defenisi'];
                bodymassindex = value['bodymassindex_id'];
                console.log(value);
                return false; //break
            }*/
            if(parseFloat(imt) >= parseFloat(value.bmi_minimum) && parseFloat(imt) <= parseFloat(value.bmi_maksimum)){
                imt_kategori = value.bmi_defenisi;
                bodymassindex = value.bodymassindex_id;
                return false;
            }
        });

        // hitung berat badan ideal
        if (jeniskelamin == 'Laki-laki'){
            bbideal = parseFloat((tb - 100) - (0.1 * (tb-100))).toFixed(2);
        }else{
            bbideal = parseFloat((tb - 100) - (0.15 * (tb-100))).toFixed(2);
        }
    }
    
    field_imt.val(imt.toString().replace('.', ','));
    kategori_imt.val(imt_kategori);
    bodymassindex_id.val(bodymassindex);
    field_bbideal.val(bbideal.toString().replace('.', ','));
});

/*----------  Tekanan Darah start  ----------*/
/*var nilaiTd = '';
$('.sysdia').on('change', function(){
    let diastolic = ($('.diastolic').val() != '') ? $('.diastolic').val() : 0
    let systolic = ($('.systolic').val() != '') ? $('.systolic').val() : 0
    $('.tekanan-darah').val(systolic + '/' +diastolic).trigger('change')
    nilaiTd = systolic + '/' +diastolic
})
$('.systolic').on('change', function(){
    if (($('.diastolic').val() != '')) {
        let diastolic = ($('.diastolic').val() != '') ? $('.diastolic').val() : 0
        let systolic = ($('.systolic').val() != '') ? $('.systolic').val() : 0
        $('.tekanan-darah').val(systolic + '/' +diastolic).trigger('change')
        nilaiTd = systolic + '/' +diastolic
    }
})
$('.tekanan-darah').on('change', function(){
    var hasil;
    $.ajax({
        url: '/ranap/pemeriksaan-rawat-inap/get-hasil-td?pendaftaran_id='+$('.pendaftaran_id').val()+'&nilai='+ $('.tekanan-darah').val()+'&golongan_umur='+$('.golongan_umur').val(),
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
        $('.hasil-td').val( hasil )
        $(".spinner-text").remove();
    });
});
*/
$("#pemeriksaanfisikform-detaknadi").keyup(function(e){
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


    let kategori = 'Irreguler';
    let _this = $(this);
    let val = _this.val() ? parseInt(_this.val()) : 0;
    let field_denyut = $('#pemeriksaanfisikform-denyutjantung');

    if ((umur.tahun < 1) && ((val >= 100) && (val <= 160))) {
        kategori = 'Reguler';
    }else if (((umur.tahun >= 1) && (umur.tahun <= 10)) && ((val >= 70) && (val <= 120))) {
        kategori = 'Reguler';
    }else if (((umur.tahun >= 11) && (umur.tahun <= 17)) && ((val >= 60) && (val <= 100))) {
        kategori = 'Reguler';
    }else if ((umur.tahun > 17) && ((val >= 60) && (val <= 100))) {
        kategori = 'Reguler';
    }

    field_denyut.val(kategori);
});

$("#detaknadi").keyup(function(e){
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


    let kategori = 'Irreguler';
    let _this = $(this);
    let val = _this.val() ? parseInt(_this.val()) : 0;
    let field_denyut = $('#denyutjantung');

    if ((umur.tahun < 1) && ((val >= 100) && (val <= 160))) {
        kategori = 'Reguler';
    }else if (((umur.tahun >= 1) && (umur.tahun <= 10)) && ((val >= 70) && (val <= 120))) {
        kategori = 'Reguler';
    }else if (((umur.tahun >= 11) && (umur.tahun <= 17)) && ((val >= 60) && (val <= 100))) {
        kategori = 'Reguler';
    }else if ((umur.tahun > 17) && ((val >= 60) && (val <= 100))) {
        kategori = 'Reguler';
    }
    
    field_denyut.val(kategori);
});

/*----------  Anatomi tubuh start  ----------*/

$(document).ready(function () {
    $('#gcsmotorik_select2').trigger('change');
    $('.td_field').keyup();
    $('.imt_field').keyup();
    $("#pemeriksaanfisikform-detaknadi").keyup();
    $("#detaknadi").keyup();
    if(tmpData) {
        if(Object.keys(tmpData).length != 0 && tmpData.constructor === Object){
            collectObject()
        }
    }

    hitungGcs();

    $('.image-frame').unbind('click');
    $('.image-frame').bind('click', function(event) {
        event.preventDefault();
        var posX = (event.pageX - $(this).offset().left),
            posY = (event.pageY - $(this).offset().top) - 10,
            tag = $('.tag');
        sumbuX = posX;
        sumbuY = posY;
        if (tag.data('show') != 1) {
            tag.attr({
                style : 'display:none;',
            });
            tag.data('show',1);
        } else {
            tag.attr({
                style : 'top: '+ (posY + 20) +'px; left: '+ posX +'px;width:500px;z-index:3;position:absolute;'
            });
            tag.data('show',2);
        }
    });
    pageFormId = $("#form-fisik :not([readonly]):not('.disable-get-change')"); // get form id page | declare di pemeriksaan js
    pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pemeriksan js

    if (is_draft) {
      $(".draft").show()
    } else {
      $(".draft").hide()
    }
  
    $("#form-fisik").on("change", () => {
        saveChanges()
    })
});

$(window).keydown(function(e){
    if ( $('#tab-pemeriksaanfisik').hasClass('active') ) {
        if (e.keyCode == 27) {
            $('.add-caption').val('');
            $('.bagian-tubuh').val('');
            $('.tag').attr({
                style : 'display:none;',
            });
            $('.tag').data('show',1);
            return false;
        }
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    }
});

var addCaption = function(e) {
    e.preventDefault();

    $(document).ready(function(){
        $("span.tag.label.label-info").attr('style','display:block');
    });

    var tabel = $('.tabel-anggotatubuh');
    var _contentParent = $(this).closest('.well-sm');
    var valBagian = $('.bagian-tubuh').val();
    if (e.keyCode == 13 || e.keyCode == 27) {
        if (e.keyCode == 13 && (/[\w\d]+/.test($(this).val())) && valBagian != '') {
            if(Object.keys(tmpData).length === 0 && tmpData.constructor === Object){
                counter = 1
            }
            var bagian = typeof bagianTubuh[valBagian] != 'undefined' ? bagianTubuh[valBagian] : '-';
            tmpData[counter] = {
                counters : counter,
                bagian : bagian,
                bagiantubuh_id : valBagian,
                koordinat_y : sumbuY,
                koordinat_x : sumbuX,
                catatan_tubuh : $(this).val()
            }
            addRow();
            counter++;
        }
        $(this).val('');
        $('.bagian-tubuh').val('');
        $('.tag').attr({
            style : 'display:none;',
        });
        $('.tag').data('show',1);
        return false;
    }
}

var collectObject = function (){
    for (let i = 0; i < Object.keys(tmpData).length; i++) {
        $('.image-frame').append('<div style=\"top: '+ tmpData[i]["koordinat_y"]+'px; left: '+ tmpData[i]["koordinat_x"] +'px;z-index:3;position:absolute;\" class=\"tag-image counter-'+ tmpData[i]["counters"] +'\"><span class=\"badge bg-warning-400\">'+ tmpData[i]["counters"] +'</span></div>');
        tmpData[i]["buttonAksi"] = '<button type="button" style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item\" data-counter=\"'+ tmpData[i]["counters"] +'\">'+'<i class=\"fa fa-trash\"></i></button>'
        dataAnatomiTable.push(tmpData[i])
    }
    refreshTable();
}

var refreshTable = function () {
    let _tabel = $('.tabel-anggotatubuh').DataTable({
        data: dataAnatomiTable,
        columns: [
          { title: 'No', data: 'counters' },
          { title: 'Bagian Tubuh', data: 'bagian' },
          { title: 'Catatan', data: 'catatan_tubuh' },
          { title: 'Aksi', data: 'buttonAksi'
         },
        ],
        destroy: true
    });
    $('.dataTables_filter').hide()
    $('.dataTables_length').hide()
}

var newDeleteRow = function(id) {
    let tempArray = [];
    let newCounter = 1
    $(`.counter-${id}`).remove();
    $('.tag-image').remove();
    dataAnatomiTable.map((item) => {
        if(item.counters != id){
            item.counters = newCounter++
            item.buttonAksi = '<button type="button" style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item\" data-counter=\"'+ item.counters +'\">'+'<i class=\"fa fa-trash\"></i></button>'
            $('.image-frame').append('<div style=\"top: '+ item.koordinat_y+'px; left: '+ item.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image counter-'+ item.counters +'\"><span class=\"badge bg-warning-400\">'+ item.counters +'</span></div>');
            tempArray.push(item)
        }
    })
    dataAnatomiTable = tempArray
    let newTmpData = Object.assign({}, dataAnatomiTable)
    tmpData = newTmpData
    if(dataAnatomiTable.length == 0){
        tmpData = {}
    }

    saveChanges();
    refreshTable();
}

var addRow = function () {
    let data = Object.values(tmpData).pop()
    $('.image-frame').append('<div style=\"top: '+ data.koordinat_y+'px; left: '+ data.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image counter-'+ data.counters +'\"><span class=\"badge bg-warning-400\">'+ data.counters +'</span></div>');
    data["buttonAksi"] = '<button type="button" style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item\" data-counter=\"'+ data.counters +'\">'+'<i class=\"fa fa-trash\"></i></button>'
    dataAnatomiTable.push(data)
    saveChanges()
    refreshTable();

    // if (_tabel.find('tbody > tr').length == 1) {
    //     $('.default-row').hide();
    // }
    // var clone  = $('.default-row').clone();
    // _tabel.find('tbody').html('<tr class=\"default-row\" style=\"display:none\">'+ clone.html() + '</tr>');
    // var i = 1;
    // $.each(tmpData, function (key,items) {
    //     $('.image-frame').append('<div style=\"top: '+ items.koordinat_y+'px; left: '+ items.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image counter-'+ items.counters +'\"><span class=\"badge bg-warning-400\">'+ items.counters +'</span></div>');
    //     var html = '';
    //     html += '<tr>';
    //     html += '<td>'+ i +'</td>';
    //     html += '<td>'+ items.bagian +'</td>';
    //     html += '<td>'+ items.catatan_tubuh +'</td>';
    //     html += '<td><button style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item\" data-counter=\"'+ items.counters +'\">'+
    //                 '<i class=\"fa fa-trash\"></i></button></td>';
    //     html += '</tr>';
    //     _tabel.find('tbody').append(html);
    //     i++;
    // })
}

// var delRow = function (event) {
    /*event.preventDefault();
    var _this = $(this);
    // console.log(_this); return;
    // var _counter = _this.data('counter') + 1;
    var _counter = _this.data('counter');
    var _trParent = _this.closest('tr');
    var _tabel = $('.tabel-anggotatubuh');
    var index = -1;

    $.each(tmpData, function (key, item) {
        if (item.counters == _counter) {
            index = key;
        }
    });

    delete tmpData[index];
    _trParent.remove();*/
    // delete tmpData[_counter];


    // $.each(tmpData, function (key, items) {
    //     if (_counter < key) {
    //         // delete tmpData[key];
    //         tmpData[(key - 1)] = items;
    //         tmpData[(key - 1)]['counters'] = items.counters - 1;
    //     }
    // });

    // $('.counter-' + _counter).remove();
    /*$('.tag-image').remove();
    addRow();
    if (_tabel.find('tbody > tr').length == 1) {
        $('.default-row').show();
    }*/

    /*event.preventDefault();
    var _this = $(this);
    var _counter = _this.data('counter');
    var _trParent = _this.closest('tr');
    var _tabel = $('.tabel-anggotatubuh');
    console.log(_trParent.remove());
    _trParent.remove();
    delete tmpData[_counter];

    $.each(tmpData, function (key, items) {
        if (_counter < key) {
            delete tmpData[key];
            tmpData[(key - 1)] = items;
            tmpData[(key - 1)]['counters'] = items.counters - 1;
        }
    });

    $('.counter-' + _counter).remove();
    $('.tag-image').remove();
    addRow();
    if (_tabel.find('tbody > tr').length == 1) {
        $('.default-row').show();
    }
    counter--;*/
// }

var delRow = function (event) {
    event.preventDefault();
    var _this = $(this);
    // console.log(_this); return;
    // var _counter = _this.data('counter') + 1;
    var _counter = _this.data('counter');
    var _trParent = _this.closest('tr');
    var _tabel = $('.tabel-anggotatubuh');
    var index = -1;

    $.each(tmpData, function (key, item) {
        if (item.counters == _counter) {
            index = key;
        }
    });

    delete tmpData[index];
    _trParent.remove();
    // delete tmpData[_counter];


    // $.each(tmpData, function (key, items) {
    //     if (_counter < key) {
    //         // delete tmpData[key];
    //         tmpData[(key - 1)] = items;
    //         tmpData[(key - 1)]['counters'] = items.counters - 1;
    //     }
    // });

    // $('.counter-' + _counter).remove();
    $('.tag-image').remove();
    addRow();
    if (_tabel.find('tbody > tr').length == 1) {
        $('.default-row').show();
    }
    // counter--;
}

$(document).off('click', '.hapus-item')
$(document).on('click','.hapus-item', function(){
    let id = $(this).attr("data-counter")
    newDeleteRow(id)
});
$('.add-caption').on('keyup',addCaption);
var saveAnatomi = function (data) {
    let res = data;
    pemeriksaanfisik_id = res.response['pemeriksaanfisik_id'] ? res.response['pemeriksaanfisik_id'] : null;
    let url = "/rajal/pemeriksaan/save-anatomi";

    if (Object.keys(tmpData).length) {
        $.ajax({
            type : 'POST',
            dataType : 'json',
            url : url,
            data : {
                data:tmpData,
                pendaftaran_id : $('#pemeriksaanfisikform-pendaftaran_id').val(),
                pasien_id : $('#pemeriksaanfisikform-pasien_id').val(),
                pemeriksaanfisik_id : pemeriksaanfisik_id,
            },
            error : function (data) {
            }
        });
    } else {
        // deprecated rizal
        // alert('Anatomi Harus Di isi');
    }
};
/*----------  Anatomi tubuh end  ----------*/

$("#form-fisik").on("submit", function(e){
    $(this).docoForm("submit",{
        success : function(data) {
            $(document).ready(function(){
                $("span.tag.label.label-info").attr('style','display:block');
            });
            $(".print").prop("disabled", false);
            saveAnatomi(data);
            pageFormDataValues = pageFormId.serializeArray(); // Get original value ketika pertama kali load page | declare di pemeriksan js
            $("#tab-periksafisik").trigger("click");
        }
    });
})

function saveChanges() {
    $("[type='hidden']").prop('disabled', true)
    let updatedData = $("#form-fisik").serializeArray();
    $("[type='hidden']").prop('disabled', false)
    
    // handling default value for unchecked checkbox
    let uncheked_checkboxes = []
    $("[type='checkbox']:not(:checked)").each(function() {
        updatedData.push({
            name: $(this).attr("name"),
            value: 0
        })
    });

    let valueObject = [];
    $.each(tmpData, function (indexInArray, valueOfElement) { 
        const objData = {
            "bagiantubuh_id": valueOfElement?.bagiantubuh_id,
            "catatan_tubuh": valueOfElement?.catatan_tubuh,
            "koordinat_x": valueOfElement?.koordinat_x,
            "koordinat_y": valueOfElement?.koordinat_y,
            "counters": valueOfElement?.counters,
            "bagian": valueOfElement?.bagian,
            "bagianTubuh": {
                "bagiantubuh_id": valueOfElement?.bagiantubuh_id
            },
        }
        valueObject.push(objData)
    });

    let value = {
        name: "PemeriksaanFisikForm[data_anatomi]",
        value: JSON.stringify(Object.assign({}, valueObject))
    }

    let pendaftaran_id = {
        name: "pendaftaran_id",
        value: typeof $('#pemeriksaanfisikform-pendaftaran_id').val() != 'undefined' ? $('#pemeriksaanfisikform-pendaftaran_id').val() : pendaftaranId
    }

    updatedData.push(value, pendaftaran_id)
    $.ajax({
        url: `/rajal/pemeriksaan/set-cache-asmed`,
        method: 'GET',
        data: updatedData,
        success: (data) => {
            if (data.is_draft) {
                $(".draft").show()
            } else {
                $(".draft").hide()
            }
        },
    })
}

$(document).on("click", ".print", function() {
    window.open("/rajal/pemeriksaan/export-pdf-periksa-fisik?pemeriksaanfisik_id="+pemeriksaanfisik_id);
});

function clearForm(form) {
  $(':input', form).each(function() {
    var type = this.type;
    var tag = this.tagName.toLowerCase();
    if (type == 'text' || type == 'password' || tag == 'textarea')
      this.value = "";
    else if (type == 'checkbox' || type == 'radio')
      this.checked = false;
    // else if (tag == 'select')
    //   this.selectedIndex = -1;
  });
};

$('.data-reset').click(function(){
    var $input = $('#pemeriksaanfisikform-tglperiksafisik').pickadate();
    var picker = $input.pickadate('picker');
    var nama_dokter = $("#pemeriksaanfisikform-nama_dokter").val();

    clearForm($('#form-fisik'));
    $("#pemeriksaanfisikform-nama_dokter").val(nama_dokter);
    $('.hapus-item').trigger('click');
    picker.set('select', new Date(), { format: 'yyyy-mm-dd' });
    $('#pemeriksaanfisikform-keadaanumum').tagsinput('removeAll');
});
/*=====  End of Fisik  ======*/
