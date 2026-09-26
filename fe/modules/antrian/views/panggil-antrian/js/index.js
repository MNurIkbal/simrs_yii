/*
* @Author: Rizqi Febian
* @Date:   2018-04-12 10:41:59
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-09-07 11:20:05
*/

//kebutuhan tarif karcis
var arrTarif = [];
var dataTarif = [];
var total_tarif = 0;

var aps = false; 
var bpjs = false;
var asuransi = false;
var rujukan = false;
var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
var pemeriksaanlab = {}

$('.pickadate-w-month').pickadate({
    format: 'dd mmm, yyyy',
    selectMonths: true,
      selectYears: 99,
    max: true,
    formatSubmit: 'yyyy-mm-dd',
});

$(document).on('change', '.selectCarabayar', function(){
    var carabayar = $('.selectCarabayar').val();
    var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
    if(carabayar == '6'){
        $('.form-bpjs').removeClass('hidden');
        bpjs = true;
        if(!$('.form-asuransi').hasClass('hidden')){
            $('.form-asuransi').addClass('hidden');
            asuransi = false;
        }
    }else if(carabayar == '2'){
        $('.form-asuransi').removeClass('hidden');
            asuransi = true;
        $('#asuransi-form').find('.carabayar-id').val(carabayar);
        $('#asuransi-form').find('.pasien-id').val($('.pasien-id').val());
        if(!$('.form-bpjs').hasClass('hidden')){
            $('.form-bpjs').addClass('hidden');
            bpjs = false;
        }
    }else{
        if(!$('.form-bpjs').hasClass('hidden')){
            $('.form-bpjs').addClass('hidden');
            bpjs = false;
        }
        if(!$('.form-asuransi').hasClass('hidden')){
            $('.form-asuransi').addClass('hidden');
            $('#asuransi-form').find('.carabayar-id').val('');
            $('#asuransi-form').find('.penjamin-id').val('');
            asuransi = false;
        }
    }
    // if(carabayar != 5){
    //  var formId = '';
    //  $('.selectRujukan').val('2').trigger('change');
    // }else{
    //  $('.selectRujukan').val('').trigger('change');
    // }
    hideRujukan();
});

/*
* author: Rizqi Febian
* date: 11-04-2018
* aksi buat handle save data
* params needed: ruangan_id, kelaspelayanan_id
*/

$(document).on('click','.simpan-btn', function(e){
    e.preventDefault();
    var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
    var asalrujukan = $('.selectRujukan').val();
    var save = false;
    var params = $('.params-header').val();
    var tarifkarcis = []; 
    var i = 0; 
    var o = 0;
    // var arrLab = $.map(pemeriksaanlab, function(value, index) {
    //                 return [value];
    //              });

    $.each(dataTarif, function(key, val){
        $.each(dataTarif[key], function(keyz, valz){
            tarifkarcis[i] = {name: 'TarifKarcis['+o+']['+keyz+']', value: valz};
            i++;
        });
    o++;
    });
    var kunjungan = $('#igd-form').serializeArray();
    var kunjunganranap = $('#ranap-form').serializeArray();
    // var penanggungjawab = $('#penanggungjawab-form').serializeArray();
    // var obj = $.merge(kunjungan, penanggungjawab);
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
    obj = $.merge(obj, tarifkarcis);
    obj = $.merge(obj, datapasien);
    // obj = $.merge(obj, penanggungjawab);
    obj = $.merge(obj, kunjungan);
    obj = $.merge(obj, kunjunganranap);
    obj = $.merge(obj, lab);
    var title = 'Terjadi Kesalahan';
    var msg = 'Belum Terisi!';
    var _pasien = true;
    var _rujukan = true;

    // console.log(obj);

    if($('.pasien-id').val() == ''){
        _pasien = false;
        docoNotification('error', title, 'Data Pasien '+msg);
    }
    if(rujukan == true){
        if($('.rujukan-id').val() == ''){
            _rujukan = false;
            docoNotification('error', title, 'Rujukan '+msg);
        }
    }
    // if(asuransi == true){
    //  if($('.asuransipasien-id').val() == ''){
    //      asuransi = false;
    //      save = false
    //      docoNotification('error', title, 'Asuransi Pasien '+msg);
    //  }
    // }
    // if(_rujukan == true && _pasien == true){
    //  save = true;
    // }
    // if( asuransi == true && _pasien == true){
    //  save = true;
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
            url: '/pendaftaran/daftar/index?param='+params,
            success: function(response){
                console.log(response)
                tableDaftarTerakhir.draw();
                clearAll();
                $("html, body").animate({
                    scrollTop: $("#table-daftar-terakhir_wrapper").offset().top
                }, 2000);
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
        .not(':button, :submit, :reset, input[name=chk-statuspasien]')
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
    $('.btn-pemeriksaan-clear').trigger('click')
    // $('.btn-pasien-inf-batal').trigger('click')
}

$(document).on('change','.dateusia', function(){
    var umur = '';
    if($(this).val() != ''){
        umur = getUmur(convertTanggalYmd($(this).val()), new Date());
    }
    $('.umurtext').val(umur);
    
})


function hideRujukan(){
    var rujukan_id = $('.selectRujukan').val()
    var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
    var param = $('.params-header').val();
    if(carabayar_group == '417' && rujukan_id != 1 && rujukan_id != '' && param != 'ranap'){
        console.log(carabayar_group, rujukan_id, param)
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
    var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
    $('#asalrujukan-id').val($(this).val());
    $('#asuransi-form').find('.asalrujukan-id').val('')
    if(asuransi == true){
        $('#asuransi-form').find('.asalrujukan-id').val($(this).val())
    }
    hideRujukan(carabayar_group, $(this).val())
});
$(document).on('change', '.selectPenjamin', function(){
    if(asuransi == true){
        $('#asuransi-form').find('.penjamin-id').val($(this).val());
    }
});

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
$('.btn-pasien-batal').on('click', function () {
    $('.inf-pasien').show();
    $('.form-data-pasien').hide();
});

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

function getInfoPasien(pasien_id) {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/get-info-pasien-new?pasien_id=' + pasien_id,
        dataType: 'JSON',
        beforeSend: function () {
            // var _html = '<div class="text-center">';
            // _html += '<i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' + loading_text + ' . . . </b>';
            // _html += '</div>';
            // _this.html(_html);
        },
        success: function (res) {
            // console.log(res);
            data = res.response;
            
            $('.inf-pasien').show();
            $('.inf-pasien-title-nama').html("<strong>" + data.nama_depan + data.nama_pasien + "</strong>");
            $('.inf-pasien-title-norm').html(i18next.t('No rekam medis') + ' : ' + data.no_rekam_medik);
            $('#inf-pasien-noidentitas').html(data.no_identitas_pasien);
            $('#inf-pasien-namapasien').html(data.nama_pasien);
            $('#inf-pasien-namapanggilan').html(data.nama_bin);
            $('#inf-pasien-tempatlahir').html(data.tempat_lahir);
            $('#inf-pasien-tanggallahir').html(convertTanggalView(data.tanggal_lahir));
            $('#inf-pasien-umur').html(getUmur(data.tanggal_lahir, new Date()));
            $('#inf-pasien-jeniskelamin').html(data.jenis_kelamin);
            $('#inf-pasien-statusperkawinan').html(data.status_perkawinan);
            $('#inf-pasien-namaibu').html(data.nama_ibu);
            $('#inf-pasien-namaayah').html(data.nama_ayah);
            $('#inf-pasien-anakke').html(data.anakke);
            $('#inf-pasien-jumlahbersaudara').html(data.jumlah_bersaudara);
            $('#inf-pasien-alamatpasien').html(data.alamat_sekarang);
            $('#inf-pasien-rtrw').html(data.rt + ' / ' + data.rw);
            $('#inf-pasien-kelurahan').html(data.kelurahan_nama);
            $('#inf-pasien-kecamatan').html(data.kecamatan_nama);
            $('#inf-pasien-kota').html(data.kabupaten_nama);
            $('#inf-pasien-propinsi').html(data.propinsi_nama);
            $('#inf-pasien-notelepon').html(data.no_mobile_pasien);
            $('#inf-pasien-alamatemail').html(data.alamatemail);
            $('#inf-pasien-suku').html(data.suku_nama);
            $('#inf-pasien-warganegara').html(data.warganegara);
            $('#inf-pasien-agama').html(data.agama_pasien);
            
            // form
            $('#instalasi_id').val(data.instalasi_id);
            $('#pendaftaran_id').val(data.pendaftaran_id);
            $('#tgl_pendaftaran').val(data.tgl_pendaftaran);
            $('#pasien_id').val(data.pasien_id);
            $('#status_periksa').val(data.status_periksa);
            $('#golonganumur_id').val(data.golonganumur_id);
            $('#status_pasien').val(data.status_pasien);
            $('#status_masuk').val(data.status_masuk);
            $('#kunjungan').val(data.kunjungan);

            $('#frm-pasien-jenisidentitas').val(data.jenisidentitas).prop('disabled', true);
            
            $('#frm-pasien-no_identitas_pasien').val(data.no_identitas_pasien).prop('readonly', true);
            $('#frm-pasien-namadepan').val(data.namadepan).prop('disabled', true);
            $('#frm-pasien-nama_pasien').val(data.nama_pasien).prop('readonly', true);
            $('#frm-pasien-nama_bin').val(data.nama_bin).prop('readonly', true);
            $('#frm-pasien-tempat_lahir').val(data.tempat_lahir).prop('readonly', true);
            $('#frm-pasien-tanggal_lahir').pickadate('picker').set('select', data.tanggal_lahir, { format: 'yyyy-mm-dd' });
            $('#frm-pasien-umur').val(getUmur(data.tanggal_lahir, new Date())).prop('readonly', true);
            // $('#frm-pasien-jeniskelamin').val(data.jeniskelamin);
            $('input[name="PasienForm[jeniskelamin]"][value=' + data.jeniskelamin + ']').prop('checked', true);
            $('input[name="PasienForm[jeniskelamin]"]:not(:checked)').prop('disabled', true);
            $('#frm-pasien-statusperkawinan').val(data.statusperkawinan).prop('disabled', true);
            $('#frm-pasien-nama_ibu').val(data.nama_ibu).prop('readonly', true);
            $('#frm-pasien-nama_ayah').val(data.nama_ayah).prop('readonly', true);
            $('#frm-pasien-anakke').val(data.anakke).prop('readonly', true);
            $('#frm-pasien-jumlah_bersaudara').val(data.jumlah_bersaudara).prop('readonly', true);
            $('#frm-pasien-alamat_pasien').val(data.alamat_sekarang);
            $('#frm-pasien-rt').val(data.rt);
            $('#frm-pasien-rw').val(data.rw);
            $('#frm-pasien-propinsi_id').val(data.propinsi_id).trigger('change');
            $('#frm-pasien-kabupaten_id').on('#frm-pasien-propinsi_id:depdrop:afterChange', function (event, id, value) {
                $(this).val(data.kabupaten_id).trigger('change');
            });
            $('#frm-pasien-kecamatan_id').on('#frm-pasien-kabupaten_id:depdrop:afterChange', function (event, id, value) {
                $(this).val(data.kecamatan_id).trigger('change');
            });
            $('#frm-pasien-kelurahan_id').on('#frm-pasien-kecamatan_id:depdrop:afterChange', function (event, id, value) {
                $(this).val(data.kelurahan_id);
            });
            // $('#frm-pasien-kelurahan_id').val(data.kelurahan_id);
            $('#frm-pasien-no_telepon_pasien').val(data.no_telepon_pasien);
            $('#frm-pasien-no_mobile_pasien').val(data.no_mobile_pasien);
            $('#frm-pasien-alamatemail').val(data.alamatemail);
            $('#frm-pasien-pekerjaan_id').val(data.pekerjaan_id);
            $('#frm-pasien-suku_id').val(data.suku_id).prop('disabled', true);
            $('#frm-pasien-warga_negara').val(data.warga_negara).prop('disabled', true);
            $('#frm-pasien-agama').val(data.agama).prop('disabled', true);
        },
        error: function (res) {
            //code
        },
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
