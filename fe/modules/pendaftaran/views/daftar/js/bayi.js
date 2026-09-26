/*
* @Author: Rizqi Febian
* @Date:   2018-04-12 10:41:59
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-15 11:36:49
*/

var data_pasien = '';

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
      selectYears: 99,
    max: true,
    formatSubmit: 'yyyy-mm-dd',
});


$("#jenisidentitas").select2();
$("#propinsi_id").select2();
$("#kabupaten_id").select2();
$("#kecamatan_id").select2();
$("#kelurahan_id").select2();
$("#warga_negara").select2();
$("#agama").select2();
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

    $.each(dataTarif, function(key, val){
        $.each(dataTarif[key], function(keyz, valz){
            tarifkarcis[i] = {name: 'TarifKarcis['+o+']['+keyz+']', value: valz};
            i++;
        });
    o++;
    });
    var kunjungan = $('#igd-form').serializeArray();
    var kunjunganranap = $('#ranap-form').serializeArray();
    var rujukan = $('#rujukan-form').serializeArray();
    var asuransi = $('#asuransi-form').serializeArray();
    var datapasien = $("#form-daftar-rajal").serializeArray();
    var obj = [];
    var lab = [];
    obj = $.merge(obj, tarifkarcis);
    obj = $.merge(obj, datapasien);
    obj = $.merge(obj, kunjungan);
    obj = $.merge(obj, kunjunganranap);
    obj = $.merge(obj, rujukan);
    obj = $.merge(obj, asuransi);
    var title = 'Terjadi Kesalahan';
    var msg = 'Belum Terisi!';
    var _pasien = true;
    var _rujukan = true;

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
            url: '/pendaftaran/daftar/bayi-lahir',
            success: function(response){
                var res = response.response;
                var pendaftaran_id = res.pendaftaran_id;
                var no_rekam_medik = res.no_rekam_medik;
                var carabayar_id = res.carabayar_id;
                var asalrujukan_id = res.asalrujukan_id;
                var nama_pasien = res.nama_pasien;
             
                if(carabayar_id != 5) {
                    if(carabayar_id == 6) {
                        var btn_bpjs = document.getElementsByClassName("btn-form-bpjs")[0];
                        var attBpjs = document.createAttribute("action");
                        attBpjs.value = "/pendaftaran/daftar/get-form-bpjs?pendaftaran_id="+pendaftaran_id+'&no_rekam_medik='+no_rekam_medik;
                        btn_bpjs.setAttributeNode(attBpjs);
                        $(".btn-form-bpjs").trigger('click');
                        $("#jnspelayanan").val(1);
                        $("#jnspelayanan").prop("disabled", true);
                        $("#nama_pasien").text(nama_pasien);
                    }
                    else {
                        var btn_asuransi = document.getElementsByClassName("btn-form-asuransi")[0];
                        var attAsuransi = document.createAttribute("action");
                        attAsuransi.value = "/pendaftaran/daftar/get-form-asuransi?pendaftaran_id="+pendaftaran_id+'&asalrujukan_id='+asalrujukan_id;
                        btn_asuransi.setAttributeNode(attAsuransi);
                        $(".btn-form-asuransi").trigger('click');
                        if(asalrujukan_id != 1) {
                            $('.form-rujukan').show();
                        }
                        else {
                            $('.form-rujukan').hide();
                        }
                    }
                }
                
                hideQuestionDialog();
                $('body').find('.confirm-dialog-overlay').remove();
                if (tabelKunjungan instanceof $.fn.dataTable.Api) {
                    tabelKunjungan.ajax.url(baseUrl + "pendaftaran/daftar/get-data-kunjungan-pasien-bayi").draw();
                }
                // tabelKunjungan.ajax.url(baseUrl + "pendaftaran/daftar/get-data-kunjungan-pasien-bayi").draw();
                clearAll();
            },
            error: function() {
                hideQuestionDialog();
                $('body').find('.confirm-dialog-overlay').remove();
                if ($('.antrian-id').val()) {
                    $('#ruangan_id').attr('disabled', true);
                }
            }
        });
    } else {
        docoNotification('error', title, 'Data belum lengkap');
    }

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

    // $('.btn-pemeriksaan-clear').trigger('click')
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

$("#datetime").val(function() {
    var d = new Date();
    return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
});

var $input_date = $('#frm-pasien-tanggal_lahir').pickadate({
    editable: true,
    max: true,
    format:'dd-mm-yyyy',
    formatSubmit:'dd-mm-yyyy',
    selectMonths: true,
    selectYears: true,
    onClose: function() {
        $('.datepicker').focus();
    }
});
var picker_date = $input_date.pickadate('picker');
$('#btn_addon_tgllahir').on('click',function(event){
    if (picker_date.get('open')) {
        picker_date.close();
    } else {
        picker_date.open();
    }
    event.stopPropagation();
});

$("#frm-pasien-tanggal_lahir").on('change', function(){
    var tgl_lahir = $(this).val();
    var new_tgl_lahir = tgl_lahir.split("-").reverse().join("-");
    var umur = getUmur(new_tgl_lahir, new Date());

    $('#frm-pasien-umur').val(umur);
});

function getInfoPasienBayi(pendaftaran_id) {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/get-info-pasien-bayi?pendaftaran_id=' + pendaftaran_id,
        dataType: 'JSON',
        success: function (res) {
            data = res.response;
            countBayi = data.countBayi;
            result = data.result[0];
            form = res.form
            if(data.countBayi > 1) {
                var btn_cari = document.getElementsByClassName("btn-cari-norm")[0];
                var att = document.createAttribute("action");
                att.value = "/pendaftaran/end-point/get-bayi?pendaftaran_id="+pendaftaran_id;
                btn_cari.setAttributeNode(att);
                $(".btn-cari-norm").trigger('click');
            }
            else {
                var kelahiranbayi_id = result.kelahiranbayi_id;
                $(".select-no-rm").hide();
                $("#data-bayi").load("load-data-bayi?pendaftaran_id=" + pendaftaran_id);
                $(".panel-kunjungan").show();
                $(".form-karcis").show();
                $(".toggle-list-pjawab").trigger('click');
                $(".pasien-id").val(data.pasien_id);
                $(".pendaftaran-id").val(pendaftaran_id);
                $('.kelahiranbayi-id').val(kelahiranbayi_id);
                $('#propinsi_id').trigger('change');
                $(".simpan-btn").show();
            }
        },
        error: function (res) {
            //code
        },
    }).done(function () {
        if (tabelKunjungan instanceof $.fn.dataTable.Api) {
            tabelKunjungan.ajax.url(baseUrl + "pendaftaran/daftar/get-data-kunjungan-pasien-bayi?pasien_id="+data.pasien_id).draw();
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
                ajax: baseUrl + "pendaftaran/daftar/get-data-kunjungan-pasien-bayi?pasien_id="+data.pasien_id,
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

function getDataBayi(pendaftaran_id) {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/daftar/get-info-pasien-bayi?pendaftaran_id=' + pendaftaran_id,
        dataType: 'JSON',
        success: function (res) {
            data = res.response;
            $(".select-no-rm").hide();
            $("#data-bayi").load("load-data-bayi?pendaftaran_id=" + pendaftaran_id);
            $(".panel-kunjungan").show();
            $(".panel-pj").show();
            $(".form-karcis").show();
            $(".toggle-list-pjawab").trigger('click');
            $(".pasien-id").val(data.pasien_id);
            $(".pendaftaran-id").val(pendaftaran_id);
            $('#frm-pasien-tanggal_lahir').trigger('change');
            $('#propinsi_id').trigger('change');
            $(".simpan-btn").show();
        },
        error: function (res) {
            //code
        },
    }).done(function () {
        if (tabelKunjungan instanceof $.fn.dataTable.Api) {
            tabelKunjungan.ajax.url(baseUrl + "pendaftaran/daftar/get-data-kunjungan-pasien-bayi?pasien_id="+data.pasien_id).draw();
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
                ajax: baseUrl + "pendaftaran/daftar/get-data-kunjungan-pasien-bayi?pasien_id="+data.pasien_id,
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

$('.btn-pasien-inf-batal').on('click', function () {
    $('.select-no-rm').show();
    $('#no_rekam_medik').val('').trigger('change');
    $(".panel-kunjungan").hide();
    $(".panel-pj").hide();
    $('.inf-pasien').hide();
    $('.form-data-pasien').hide();
    $("#nomor_cari").val("");
    $('input[name=source_peserta][value=2]').prop('checked', false);
    $('.simpan-btn').hide();

    if ($('#ruangan_id').prop('disabled')) {
        focusField("id", "kunjunganform-jeniskasuspenyakit_id", true, "select");
    } else {
        focusField("id", "ruangan_id", true, "select");
    }

    // clearFormKunjungan();

    if (tabelKunjungan instanceof $.fn.dataTable.Api) {
        tabelKunjungan.ajax.url(baseUrl + "pendaftaran/daftar/get-data-kunjungan-pasien?pasien_id=0").draw();
    }
});