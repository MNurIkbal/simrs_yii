/*
* @Author: Rizqi Febian
* @Date:   2018-04-12 10:41:59
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-15 11:36:49
*/

//kebutuhan tarif karcis
var arrTarif = [];
var dataTarif = [];
var total_tarif = 0;
var tabelKunjungan;

var aps = false;
var bpjs = false;
var asuransi = false;
var rujukan = false;
var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
var pemeriksaanlab = {}

$('.pickadate-w-month').pickadate({
    format: 'dd mmm, yyyy',
    selectMonths: true,
    selectYears: true,
    max: true,
    formatSubmit: 'yyyy-mm-dd',
});

$(document).on("change","#kunjunganform-kelaspelayanan_id", function (event) {
    event.preventDefault();
    $("#penjamin_id").trigger("change");
});


$(document).on('keydown', null, function (e) {
    if (e.key=="F9") {
        if ($('input[name=chk-statuspasien]').is(':checked')) {
            $('input[name=chk-statuspasien]').prop('checked', false).trigger('change');
            // $('#frm-pasien-jenisidentitas').focus();
        } else {
            $('input[name=chk-statuspasien]').prop('checked', true).trigger('change');
            // $("#no_rekam_medik").select2("open");
            // $("#no_rekam_medik").focus();
        }
    }

    if (e.key == "F8") {
        if ($("input[name=chk-statuspasien]").is(":checked")) {
            if ($('#modal_pencarian_lanjutan').hasClass('in')) {
                $("#modal_pencarian_lanjutan").modal("hide");
            } else {
                $("#btn-pencarian-lanjutan").click();
            }
        }
    }

    if (e.key == "Enter") {
        if ($('#modal_pencarian_lanjutan').hasClass('in')) {
            if (typeof $(':focus').data("mask") !== "undefined") {
                var date = $(':focus').val();
                var regex = /^(((0[1-9]|[12]\d|3[01])\-(0[13578]|1[02])\-((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\-(0[13456789]|1[012])\-((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\-02\-((19|[2-9]\d)\d{2}))|(29\-02\-((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/g;
                var resultRegex = regex.test(date);

                if (date != '' && date != "__-__-____") {
                    if (resultRegex == false) {
                        docoNotification("warning", "Perhatian!", "Format Tanggal Salah!");

                        $(':focus').val(null);
                    } else {
                        $(".data-filter").click();
                    }
                }
            } else {
                $(".data-filter").click();
            }
        }
    }

    if (e.key == "F7") {
        if ($('#modal_pencarian_lanjutan').hasClass('in')) {
            $(".data-reset").click();
        }
    }
});

$('input').on('keydown', null, 'alt+s', function (event) {
    //each input event one by one... will be blured
    $('input').each(function () {
        $(this).trigger('blur');
    })

    if ($('.form-data-pasien').is(':visible')) {
        $(".btn-pasien-simpan-tambah").click();
    } else {
        $(".simpan-btn").click();
    }
});

$(document).on('keydown', null, 'alt+s', function (event) {
    if ($('.form-data-pasien').is(':visible')) {
        $(".btn-pasien-simpan-tambah").click();
    } else {
        $(".simpan-btn").click();
    }
});

$(document).on('keydown', null, 'alt+q', function (event) {
    $("#btn-pilih-antrian").click();
});

$(document).on('keydown', null, 'alt+w', function (event) {
    $("#btn-ubah-jenis-antrian").click();
});

$(document).on('keydown', null, 'alt+e', function (event) {
    $("#btn-pindah-loket").click();
});

// close every modal in pendaftaran
$(document).on('keydown', null, 'esc', function (event) {
    $(".close").click();
});

$(document).on('change', '.selectCarabayar', function(){
    var carabayar = $('.selectCarabayar').val();
    var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
    if(carabayar == '6'){ // bpjs
        bpjs = true;
        $('.form-bpjs').removeClass('hidden');

        if(!$('.form-asuransi').hasClass('hidden')){
            $('.form-asuransi').addClass('hidden');
            // asuransi = false;
        }
        if (!$('.form-rujukan').hasClass('hidden')) {
            $('.form-rujukan').addClass('hidden');
        }

        $('#asalrujukan_id').val('27').trigger('change');
    }else if(carabayar == '2'){ // asuransi
        bpjs = false;
        $('.form-asuransi').removeClass('hidden');
            // asuransi = true;
        // $('#asuransi-form').find('.carabayar-id').val(carabayar);
        // $('#asuransi-form').find('.pasien-id').val($('.pasien-id').val());
        if(!$('.form-bpjs').hasClass('hidden')){
            $('.form-bpjs').addClass('hidden');
        }

        if (rujukan == true) {
            $('.form-rujukan').removeClass('hidden');
        } else {
            if(!$('.form-rujukan').hasClass('hidden')){
                $('.form-rujukan').addClass('hidden');
            }
        }
        $('#asalrujukan_id').val('').trigger('change');
    }else{
        bpjs = false;
        if(!$('.form-bpjs').hasClass('hidden')){
            $('.form-bpjs').addClass('hidden');
        }
        if(!$('.form-asuransi').hasClass('hidden')){
            $('.form-asuransi').addClass('hidden');
            // asuransi = false;
        }

        if (rujukan == true) {
            $('.form-rujukan').removeClass('hidden');
        } else {
            if(!$('.form-rujukan').hasClass('hidden')){
                $('.form-rujukan').addClass('hidden');
            }
        }
        $('#asalrujukan_id').val('').trigger('change');
    }
    // if(carabayar != 5){ // umum
    //  var formId = '';
    //  $('.selectRujukan').val('2').trigger('change');
    // }else{
    //  $('.selectRujukan').val('').trigger('change');
    // }
    // hideRujukan();
});

/*
* author: Rizqi Febian
* date: 11-04-2018
* aksi buat handle save data
* params needed: ruangan_id, kelaspelayanan_id
*/

$(document).on('click','.simpan-btn', function(e){
    e.preventDefault();
    $('#ruangan_id').attr('disabled', false);
    var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
    var asalrujukan = $('.selectRujukan').val();
    var save = false;
    var params = $('.params-header').val();
    var tarifkarcis = [];
    var i = 0;
    var o = 0;
    var dataTarif = [];

    $.each($('.check-aksi'),function() {
        if ($(this).is(':checked')) {
            var _ke = $(this).attr('data-key');
            dataTarif.push(arrTarif[_ke]);
        }
    });
    $.each(dataTarif, function(key, val){
        $.each(dataTarif[key], function(keyz, valz){
            tarifkarcis[i] = {name: 'TarifKarcis['+o+']['+keyz+']', value: valz};
            i++;
        });
    o++;
    });
    var kunjungan = $('#ranap-form').serializeArray();
    var kunjunganranap = $('#ranap-form').serializeArray();
    var rujukan = $('#rujukan-form').serializeArray();
    var asuransi = $('#asuransi-form').serializeArray();
    var pasien = $('#form-daftar-ranap').serializeArray();
    var obj = [];
    var datapasien = [];
    var lab = [];
    lab[0] = {name: 'periksalab', value: JSON.stringify(pemeriksaanlab)};
    datapasien[0] = {name: 'DataPasien[pasien_id]', value: $('.pasien-id').val()};
    datapasien[1] = {name: 'DataPasien[status_kunjungan]', value: $('.status-kunjungan').val()};
    datapasien[2] = {name: 'DataPasien[antrian_id]', value: $('.antrian-id').val()};
    datapasien[3] = {name: 'DataPasien[tanggal_lahir]', value: $('#frm-pasien-tanggal_lahir').val()};
    datapasien[4] = {name: 'DataPasien[umur]', value: $('#frm-pasien-umur').val()};
    datapasien[5] = {name: 'DataPasien[jk]', value: $('input[name="PasienForm[jeniskelamin]"]:checked').val()};
    datapasien[6] = {name: 'DataPasien[is_aps]', value: aps};
    datapasien[7] = {name: 'DataPasien[no_rekam_medik]', value: $('#hidden-no_rekam_medik').val()};
    datapasien[8] = {name: 'DataPasien[nama_pasien]', value: $('#hidden-nama_pasien').val()};
    obj = $.merge(obj, tarifkarcis);
    obj = $.merge(obj, datapasien);
    obj = $.merge(obj, kunjungan);
    obj = $.merge(obj, kunjunganranap);
    obj = $.merge(obj, lab);
    obj = $.merge(obj, rujukan);
    obj = $.merge(obj, asuransi);
    obj = $.merge(obj, pasien);
    var title = 'Terjadi Kesalahan';
    var msg = 'Belum Terisi!';
    var _pasien = true;
    var _rujukan = true;

    // if($('.pasien-id').val() == ''){
    //     _pasien = false;
    //     docoNotification('error', title, 'Data Pasien '+msg);
    // }

    if( _pasien == true){
        if(_rujukan == false){
            save = false
        }else{
            save = true;
        }
    }

    if(save){
        $(this).docoForm('click', {
            method: 'POST',
            data: obj,
            url: '/pendaftaran/daftar-ranap/index',
            success: function(response){
                var res = response.response;
                var pendaftaran_id = res.pendaftaran_id;
                var no_rekam_medik = res.no_rekam_medik;
                var carabayar_id = res.carabayar_id;
                var asalrujukan_id = res.asalrujukan_id;
                var nama_pasien = res.nama_pasien;
                var pasienadmisi_id = res.pasienadmisi_id;
                var penjamin_id = res.penjamin_id;
                var pasien_id = res.pasien_id;
                
                if(carabayar_id == 5) {

                } else if(carabayar_id == 6) {
                    var btn_bpjs = document.getElementsByClassName("btn-form-bpjs")[0];
                    var attBpjs = document.createAttribute("action");
                    var url = [
                        {name: 'pendaftaran_id', value : pendaftaran_id},
                        {name: 'no_rekam_medik', value : no_rekam_medik},
                        {name: 'nama_pasien', value : nama_pasien},
                        {name: 'params', value : params},
                        {name: 'pasienadmisi_id', value : pasienadmisi_id},
                    ];
                    var _param = $.param(url);
                    attBpjs.value = "/pendaftaran/daftar-ranap/get-form-bpjs?"+_param;
                    btn_bpjs.setAttributeNode(attBpjs);
                    $(".btn-form-bpjs").trigger('click');
                    $("#jnspelayanan").val(1);
                    $("#jnspelayanan").prop("disabled", true);
                    $("#nama_pasien").text(nama_pasien);
                } else {
                    var btn_asuransi = document.getElementsByClassName("btn-form-asuransi")[0];
                    var attAsuransi = document.createAttribute("action");
                    attAsuransi.value = "/pendaftaran/daftar-ranap/get-form-asuransi?pendaftaran_id="+pendaftaran_id+'&asalrujukan_id='+asalrujukan_id+'&carabayar_id='+carabayar_id+'&penjamin_id='+penjamin_id+'&pasien_id='+pasien_id+'&params='+paramsPelayanan;
                    btn_asuransi.setAttributeNode(attAsuransi);
                    $(".btn-form-asuransi").trigger('click');
                    if(asalrujukan_id != 1) {
                        $('.form-rujukan').show();
                    }
                    else {
                        $('.form-rujukan').hide();
                    }
                }

                // tableDaftarTerakhir.draw();
                tabelKunjungan.ajax.url(baseUrl + "pendaftaran/daftar-ranap/get-data-kunjungan-pasien").draw();
                clearAll();
                // $("html, body").animate({
                //     scrollTop: $("#table-daftar-terakhir_wrapper").offset().top
                // }, 2000);

                // $('.createSep').attr('disabled', false);
            },
            error: function() {
                if ($('.antrian-id').val()) {
                    $('#ruangan_id').attr('disabled', true);
                }
            }
        });
    } else {
        docoNotification('error', title, 'Data belum lengkap');
    }

});


$(document).on('click','.reset-btn', function(e){
    clearAll();
});

function clearAll() {
    $("input[name='no_antrian']").val('');
    $(':input','#form-daftar-rajal')
        .not(':button, :submit, :reset, input[name=chk-statuspasien], :radio')
        .val('')
        .prop('checked', false)
        .prop('selected', false)
        .trigger('change');
    $(':input','#igd-form')
        .not(':button, :submit, :reset, :hidden')
        .val('')
        .prop('checked', false)
        .prop('selected', false)
        .trigger('change');
    $(':input','#ranap-form')
        .not(':button, :submit, :reset, :hidden')
        .val('')
        .prop('checked', false)
        .prop('selected', false)
        .trigger('change');
    $(':input','#penanggungjawab-form')
        .not(':button, :submit, :reset, :hidden,:radio')
        .val('')
        .prop('checked', false)
        .prop('selected', false)
        .trigger('change');
    $("input[name='PjpasienForm[jk]']").prop('checked',false);
    $(':input','#bpjs-form')
        .not(':button, :submit, :reset, :hidden')
        .val('')
        .prop('checked', false)
        .prop('selected', false)
        .trigger('change');
    $(':input','#asuransi-form')
        .not(':button, :submit, :reset, :hidden')
        .val('')
        .prop('checked', false)
        .prop('selected', false)
        .trigger('change');
    $(':input','#rujukan-form')
        .not(':button, :submit, :reset, :hidden')
        .val('')
        .prop('checked', false)
        .prop('selected', false)
        .trigger('change');
    $('.select-no-rm').show();
    $('.inf-pasien').hide();
    $('.form-data-pasien').hide();
    $('#no_rekam_medik').attr('disabled', false);
    // $("input[name=chk-statuspasien]").prop('checked',true);
    $('#no_rekam_medik').val('').trigger('change');
    $('div.form-group.has-error').each(function(){
            $(this).removeClass('has-error');
    });
    $('body').find('span.help-block.error').remove();
    $('body').find('div.help-block').remove();

    // set new date pendaftaran / admisi
    $("#datetime").val(function() {
        var d = new Date();
        return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
    });

    $('[name="PasienForm[jeniskelamin]"]').each(function() {$(this).prop("checked", false)});
    $('[name="PasienForm[golongandarah]"]').each(function() {
        $(this).prop("checked", ($(this).val() == 600 ? true : false))
    });

    $('.btn-pemeriksaan-clear').trigger('click')
    // $('.btn-pasien-inf-batal').trigger('click')
}

$(document).on('change','.dateusia', function(){
    var umur = '';
    if($(this).val() != ''){
        umur = getUmur(convertTanggalYmd($(this).val()), new Date());
    }
    $('.usiapjtext').val(umur);

})


function hideRujukan(){
    var rujukan_id = $('.selectRujukan').val()
    var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
    var param = $('.params-header').val();
    if(carabayar_group == '417' && rujukan_id != 1 && rujukan_id != '' && param != 'ranap'){
        // console.log(carabayar_group, rujukan_id, param)
        if(asuransi != true || bpjs != true){
            rujukan = true;
        }
        $('.form-rujukan').removeClass('hidden');
    }else{
        rujukan = false;
        if(!$('.form-rujukan').hasClass('hidden')){
            $('.form-rujukan').addClass('hidden');
        }
    }
}

$(document).on('change', '.selectRujukan', function(){
    // var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
    // $('#asalrujukan-id').val($(this).val());

    // if (carabayar_group == 419) { // asuransi
    //     if (bpjs == true) {

    //     }
    // }
    // $('#asuransi-form').find('.asalrujukan-id').val('');
    // if(asuransi == true){
    //     $('#asuransi-form').find('.asalrujukan-id').val($(this).val())
    // }
    // hideRujukan(carabayar_group, $(this).val())

    var valRujukan = $(this).val();
    if (valRujukan && valRujukan != 1 && valRujukan != 6) {
        rujukan = true;
        if (bpjs == false) {
            $('.form-rujukan').removeClass('hidden');
        } else {
            if(!$('.form-rujukan').hasClass('hidden')){
                $('.form-rujukan').addClass('hidden');
            }
        }
    } else {
        rujukan = false;
        if(!$('.form-rujukan').hasClass('hidden')){
            $('.form-rujukan').addClass('hidden');
        }
    }
});


$(document).on('change', '.selectPenjamin', function(){
    if(asuransi == true){
        $('#asuransi-form').find('.penjamin-id').val($(this).val());
    }
});

// DEPRECATED
$('#rujukan-form').submit(function(event){
    event.preventDefault();
    var _value = $(this).serializeArray();
    $(this).docoForm("submit", {
        data: _value,
        success: function (response) {
            $('.rujukan-id').val(response);
        }
    });
})

$('#asuransi-form').submit(function(event){
    event.preventDefault();
    var _value = $(this).serializeArray();
    var _save = true
    var msg = "Belum Terisi"
    var title = 'Terjadi Kesalahan';
    if($('.pasien-id').val() == ''){
        _save = false;
        docoNotification('error', title, 'Data Pasien '+msg);
    }
    if($('.selectPenjamin').val() == ''){
        _save = false;
        docoNotification('error', title, 'Penjamin '+msg);
    }
    if($('.selectRujukan').val() == ''){
        _save = false;
        docoNotification('error', title, 'Asal rujukan '+msg);
    }

    if(_save == true){
        $(this).docoForm("submit", {
            data: _value,
            success: function (response) {
                $('#igd-form').find('.rujukan-id').val(response.rujukanid);
                $('#igd-form').find('.asuransipasien-id').val(response.asuransipasienid);
            }
        });
    }
})

$('#panggilAntian').on('click', function () {
    $('.modal-antrian').modal('show');
});

$("#datetime").val(function() {
    var d = new Date();
    return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
});

$("#datetime").AnyTime_picker({
    format: "%d-%m-%Y %H:%i",
});

// $('.pickadate').pickadate({
//  format: 'dd-mm-yyyy',
//  formatSubmit: 'yyyy-mm-dd',
//  onStart: function () {
//      var date = new Date();
//      this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
//  }
// });


function take_snapshot() {
    // take snapshot and get image data
    Webcam.snap(function (data_uri) {
        $('#profilePict').attr('src', data_uri);
    });
}

$('#open-camera').on('click', function () {
    $('.camera-modal-sm').modal('show');

    Webcam.set({
        width: 200,
        height: 200,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');
});

$(document).on('change', '.pasien-id', function(){
    if(asuransi == true){
        $('#asuransi-form').find('.pasien-id').val('')
        $('#asuransi-form').find('.pasien-id').val($(this).val())
    }
})

$('.btn-pasien-ubah').on('click', function () {
    $('.inf-pasien').hide();
    $('.form-data-pasien').show();
});
// $('.btn-pasien-batal').on('click', function () {
//     $('.inf-pasien').show();
//     $('.form-data-pasien').hide();
// });

$('.autoListRuangan').on('change',function(e){
    thisval = $(this).val();
    // console.log(thisval);
    if(thisval) {
        $('#cari_kamar').attr('action',$('#cari_kamar').data('action')+thisval);
    }
    else {
        $('#cari_kamar').removeAttr('action');
    }
});

// ---pasien lama
function getInfoPasien(id) {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/get-info-pasien?id=' + id,
        dataType: 'JSON',
        beforeSend: function () {
        },
        success: function (res) {
            data = res.response;
            form = res.form
            data_pasien = data;
            $('.select-no-rm').hide();
            $('.inf-pasien').show();
            $('.pasien-id').val(id)
            $('.form-data-pasien').hide();
            $('.inf-pasien-title-nama').html("<strong>" + data.nama_depan + data.nama_pasien + "</strong>");
            $('.inf-pasien-title-norm').html(i18next.t('No rekam medis') + ' : ' + data.no_rekam_medik);
            $('#nomr').val(data.no_rekam_medik); // for bpjs form
            $('#inf-pasien-jenisidentitas').html(data.identitas);
            $('#inf-pasien-noidentitas').html(data.no_identitas_pasien);
            $('#inf-pasien-namadepan').html(data.nama_depan);
            $('#inf-pasien-namapasien').html(data.nama_pasien);
            $('#inf-pasien-namapanggilan').html(data.nama_bin);
            $('#inf-pasien-tempatlahir').html(data.tempat_lahir);
            $('#inf-pasien-tanggallahir').html(convertTanggalView(data.tanggal_lahir));
            $('#inf-pasien-umur').html(getUmur(data.tanggal_lahir, new Date()));
            $('#inf-pasien-jeniskelamin').html(data.jenis_kelamin);
            $('#inf-pasien-golongandarah').html(data.golongan_darah);
            $('#inf-pasien-statusperkawinan').html(data.status_perkawinan);
            $('#inf-pasien-namaibu').html(data.nama_ibu);
            $('#inf-pasien-namaayah').html(data.nama_ayah);
            $('#inf-pasien-anakke').html(data.anakke);
            $('#inf-pasien-jumlahbersaudara').html(data.jumlah_bersaudara);
            $('#inf-pasien-alamatpasien').html(data.alamat_pasien);
            $('#inf-pasien-rtrw').html((data.rt ? data.rt : '-') + ' / ' + (data.rw ? data.rw : '-'));
            $('#inf-pasien-kelurahan').html(data.kelurahan_nama);
            $('#inf-pasien-kecamatan').html(data.kecamatan_nama);
            $('#inf-pasien-kota').html(data.kabupaten_nama);
            $('#inf-pasien-propinsi').html(data.propinsi_nama);
            $('#inf-pasien-notelepon').html(data.no_telepon_pasien);
            $('#inf-pasien-nomobile').html(data.no_mobile_pasien);
            $('#inf-pasien-alamatemail').html(data.alamatemail);
            $('#inf-pasien-pendidikan').html(data.pendidikan_nama);
            $('#inf-pasien-pekerjaan').html(data.pekerjaan_nama);
            $('#inf-pasien-suku').html(data.suku_nama);
            $('#inf-pasien-warganegara').html(data.warganegara);
            $('#inf-pasien-agama').html(data.agama_pasien);
            if (data.photopasien) {
              img = "/media/img/pasien/"+data.photopasien;
            }else {
              img = "/media/img/icon-app/default.jpg";
            }

            $('#pasang_image').empty().append('<img id="profilePict" src="'+img+'" alt="">');
            $('#pasang_image2').empty().append('<img id="profilePict" src="'+img+'" alt="">');

            // hide as default{
            $('.collapse-pasien').click();

            // form bpjs
            $('#norm').val(data.no_rekam_medik);

            // form
            $('#form-daftar-ranap').autofill(form);
            $('#frm-pasien-jenisidentitas').val(data.jenisidentitas).trigger('change')
            $('#frm-pasien-namadepan').val(data.namadepan).trigger('change')
            $('#frm-pasien-statusperkawinan').val(data.statusperkawinan).trigger('change')
            $('#frm-pasien-statusperkawinan').val(data.statusperkawinan).trigger('change')
            $('#frm-pasien-pekerjaan_id').val(data.pekerjaan_id).trigger('change')
            $('#frm-pasien-suku_id').val(data.suku_id).trigger('change')
            $('#frm-pasien-warga_negara').val(data.warga_negara).trigger('change')
            $('#frm-pasien-agama').val(data.agama).trigger('change')
            var $input_date_tgl_info = $('#frm-pasien-tanggal_lahir').pickadate({
                editable: true,
                // format:'dd-mm-yyyy',
                // formatSubmit:'dd-mm-yyyy',
                onClose: function() {
                    $('.datepicker').focus();
                }
            });
            $input_date_tgl_info.pickadate('picker').set('select', data.tanggal_lahir, { format: 'dd-mm-yyyy' });
            $('#frm-pasien-umur').val(getUmur(data.tanggal_lahir, new Date()));

            /*$('#frm-pasien-propinsi_id').val(data.propinsi_id).trigger('change').trigger('depdrop:change');
            $('#frm-pasien-kabupaten_id').on('depdrop:afterChange', function (event, id, value) {
                // console.log(event)
                $(this).val(data.kabupaten_id).trigger('change').trigger('depdrop:change');
            });
            $('#frm-pasien-kecamatan_id').on('depdrop:afterChange', function (event, id, value) {
                $(this).val(data.kecamatan_id).trigger('change').trigger('depdrop:change');
            });
            $('#frm-pasien-kelurahan_id').on('depdrop:afterChange', function (event, id, value) {
                $(this).val(data.kelurahan_id).trigger('change');
            });*/


            // $('#frm-pasien-jenisidentitas').prop('disabled', true);
            // $('#frm-pasien-no_identitas_pasien').prop('readonly', true);
            // $('#frm-pasien-namadepan').prop('disabled', true);
            // $('#frm-pasien-nama_pasien').prop('readonly', true);
            // $('#frm-pasien-nama_bin').prop('readonly', true);
            // $('#frm-pasien-tempat_lahir').prop('readonly', true);
            // $('#frm-pasien-tanggal_lahir').prop('disabled', true);
            // // $('#frm-pasien-umur').prop('readonly', value);
            // // $(':radio[name="PasienForm[jeniskelamin]"]:not(:checked)').prop('disabled', value);
            // // $(':radio[name="PasienForm[golongandarah]"]:not(:checked)').prop('disabled', value);
            // $('#frm-pasien-statusperkawinan').prop('disabled', true);
            // $('#frm-pasien-nama_ibu').prop('readonly', true);
            // $('#frm-pasien-nama_ayah').prop('readonly', true);
            // $('#frm-pasien-anakke').prop('readonly', true);
            // $('#frm-pasien-jumlah_bersaudara').prop('readonly', true);

            // $('#frm-pasien-suku_id').prop('disabled', true);
            // $('#frm-pasien-warga_negara').prop('disabled', true);
            // $('#frm-pasien-agama').prop('disabled', true);

            focusField("id", "ruangan_id", true, "select");

            $("#hidden_no_bpjs").val(data.nopeserta_bpjs);
            var caraBayar = parseInt($('#selectCarabayar').val());
            if (caraBayar == 6) {
                var noPesertaBpjs = data.nopeserta_bpjs;

                $('#nomor_cari').val(noPesertaBpjs);
                $('input[name=source_peserta][value=2]').prop('checked', true);
            }
        },
        error: function (res) {
            //code
        },
    }).done(function () {
        if (tabelKunjungan instanceof $.fn.dataTable.Api) {
            tabelKunjungan.ajax.url(baseUrl + "pendaftaran/daftar/get-data-kunjungan-pasien?pasien_id="+data.pasien_id).draw();
        } else {
            tabelKunjungan = $("#tbl-kunjungan").docoTabel({
                filter: false,
                processing: true,
                serverSide: true,
                scrollX: true,
                scrollCollapse: true,
                order: [[0, "desc"]],
                displayLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
                ajax: baseUrl + "pendaftaran/daftar/get-data-kunjungan-pasien?pasien_id="+data.pasien_id,
                columns: [
                    {title: "Tanggal Pendaftaran", data: "tgl_pendaftaran"},
                    {title: "No. Pendaftaran", data: "no_pendaftaran"},
                    {title: "Instalasi", data: "instalasi_nama"},
                    {title: "Ruangan", data: "ruangan_nama"},
                    {title: "Dokter", data: "nama_pegawai"},
                ],
            });
        }
    });
}

$(document).on('change', ".no-antrian", function () {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/pilih-antrian-manual?no_antrian=' + $(this).val(),
        dataType: 'JSON',
        beforeSend: function () {
        },
        success: function (res) {
            // console.log(res);
            if (res.metadata.status == '200') {
                var response = res.response;
                // docoNotification('success', null, res.response.message);
                $('.antrian-id').val(response.antrian_id).trigger('change');
            } else {
                docoNotification('error', null, res.response.message);
                $(this).val('');
            }

        },
    });
});

// ranap

$("#is_booked").change(function (e) {
    e.preventDefault();
    if (this.checked) {
        $('.btn-caribooking').prop('disabled', false);
    } else {
        $('.btn-caribooking').prop('disabled', true);
        $('#bookingkamar_no').val('');
        $('#bookingkamar_id').val('');
    }
});

$('#ruangan_id').change(function(e) {
    e.preventDefault();
    if ($(this).val()) {
        $('.btn-carikamar').prop('disabled', false);
    } else {
        $('.btn-carikamar').prop('disabled', true);
    }
});

$("#ruangan_id").on("depdrop:afterChange", function (event, id, value) {
    $(this).val($('#temp_ruangan_id').val()).trigger("change");
});

$(document).on('click','.toggle-list-pjawab', function(){
    var data = $('#HiddenPenanggungJawab').val();
    if (data==1) {
        $('#HiddenPenanggungJawab').val(0);
    }else{
        $('#HiddenPenanggungJawab').val(1);
    }
})

$('#cari_kamar').on('click',function(e){
    e.preventDefault();
    var jenis_id = $('#jeniskasuspenyakit_id').val();
    var kelas_id = $('#kelaspelayanan_id').val();
    var ruangan_id = $('#ruangan_id').val();
    if(!jenis_id){
        return new PNotify({
        title: "Terjadi Kesalahan",
        text: "Jenis Kasus belum dipilih!",
        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
        type: "warning"
        });
    }
    if(!kelas_id){
        return new PNotify({
        title: "Terjadi Kesalahan",
        text: "List Kelas Pelayanan belum dipilih!",
        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
        type: "warning"
        });
    }
    if(!ruangan_id){
        return new PNotify({
        title: "Terjadi Kesalahan",
        text: "Ruangan belum dipilih!",
        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
        type: "warning"
        });
    }
    _this = $(this);
    modal = $(_this.data('target'));
    // modal.modal('toggle');
    // console.log(modal);
    width = _this.data('width');
    var modal_content = modal.find('div.modal-dialog');
    var url = _this.attr('href')+'?jenis_id='+jenis_id+'&kelas_id='+kelas_id+'&ruangan_id='+ruangan_id;

    if (typeof width != 'undefined') {
        modal_content.css('width',width);
    }

    $('.modal-content', modal).empty();
    var _html = '<div class="text-center">';
        _html += '<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>'+ i18next.t("memuat") +' . . . </b></h3>';
    _html +=    '</div>';
    $('[data-popup="tooltip"]').tooltip('destroy');
    // console.log(modal.find('.modal-content'));
    modal.find('.modal-content').html(_html).load(url, function() {
        modal.modal({show:true});
    });
});


$('#penjamin_select').on('change', function() {
    var pasien_id = $('.pasien-id').val();
    var carabayar_id = $('#selectCarabayar').val();
    var penjamin_id = $(this).val();

    $.ajax({
        url: '/pendaftaran/end-point/get-data-asuransi?pasien_id='+pasien_id+'&penjamin_id='+penjamin_id,
        type: 'GET',
        dataType: 'json',
        beforeSend: function (data) {
        },
        success: function (data) {
            if (data.response) {
                $('#asuransiform-carabayar_id').val(data.response.carabayar_id);
                $('#asuransiform-penjamin_id').val(data.response.penjamin_id);
                $('#asuransiform-pasien_id').val(data.response.pasien_id);
                $('#asuransiform-nokartuasuransi').val(data.response.nokartuasuransi);
                $('#asuransiform-namapemilikasuransi').val(data.response.namapemilikasuransi);
                $('#asuransiform-nomorpokokperusahaan').val(data.response.nomorpokokperusahaan);
                $('#asuransiform-kelastanggungan_id').val(data.response.kelastanggunganasuransi_id).trigger('change'); // dropdownlist
                $('#asuransiform-namaperusahaan').val(data.response.namaperusahaan);
                var dt = parseInt(data.response.tgl_konfirmasi.substring(8,10));
                var mon = parseInt(data.response.tgl_konfirmasi.substring(5,7));
                var yr = parseInt(data.response.tgl_konfirmasi.substring(0,4));
                var date = new Date(yr, mon-1, dt);
                var picker = $('#asuransiform-tgl_konfirmasi').pickadate('picker');
                picker.set('select', date);
                if (data.response.status_konfirmasi == '1') {
                    $('#asuransiform-status_konfirmasi').attr("checked", true); // checkbox
                } else {
                    $('#asuransiform-status_konfirmasi').attr("checked", false); // checkbox
                }
            } else {
                $('#asuransiform-carabayar_id').val(carabayar_id);
                $('#asuransiform-penjamin_id').val(penjamin_id);
                $('#asuransiform-pasien_id').val(pasien_id);
                $('#asuransiform-carabayar_id').val('');
                $('#asuransiform-penjamin_id').val('');
                $('#asuransiform-pasien_id').val('');
                $('#asuransiform-nokartuasuransi').val('');
                $('#asuransiform-namapemilikasuransi').val('');
                $('#asuransiform-nomorpokokperusahaan').val('');
                $('#asuransiform-kelastanggungan_id').val('').trigger('change'); // dropdownlist
                $('#asuransiform-namaperusahaan').val('');
                $('#asuransiform-tgl_konfirmasi').val('');
                $('#asuransiform-status_konfirmasi').attr("checked", false); // checkbox
            }
        }
    });
});


/**
 * @todo Fungsi untuk memfokuskan cursor ke suatu input
 * @author Sigit Arif Munandar <sigit@docotel.com>
 */
function focusField(attribute = "id", name, focus = true, type = "input", open=false) {
    if (attribute == "id") {
        if (type == "text" || type == "textarea") {
            $("#" + name).focus();
        } else if (type == "select") {
            if (open) {
                $("#" + name).select2("open");
            }
            $("#" + name).focus();
        }
    } else {
        if (type == "text" || type == "textarea") {
            $("." + name).focus();
        } else if (type == "select") {
            if (open) {
                $("#" + name).select2("open");
            }
            $("." + name).focus();
        }
    }
}

$(document).ready(function() {
    _formPendaftaran.rujukRanap = _rujukRanap;
    if (Object.keys(_rujukRanap).length) {
        _formPendaftaran.getInfoPasien(_rujukRanap.pendaftaran.pasien_id,"");
        $('#no_rekam_medik').val(_rujukRanap.pendaftaran.no_rekam_medik).trigger('change');
    }
});