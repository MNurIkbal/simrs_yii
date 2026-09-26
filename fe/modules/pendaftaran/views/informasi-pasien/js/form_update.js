const progress = $(".progress");
var timeoutId = null;
var startTime = 0;
var elapsedTime = 0;
var counting = 0;

function pilihKamar(identifier) {
    let link = document.URL;
    let patternAction = link.match(/pemesanan-kamar/g);
    const jenisTempatTidur = $(identifier).data('kettempattidur_id');
    const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
    const kamarruangan_id = $(identifier).data('kamarruangan_id');
    var jk_kamar;
    var allow_jk;
    var attr = $(identifier).data('allow_jk');
    var jk = $("#jeniskelamin_id").val();
    var _keteranganKamar;

    if (typeof attr !== typeof undefined && attr !== false) {
        allow_jk = $(identifier).data('allow_jk');
    }

    if (parseInt(jenisTempatTidur) == 1) {
      jk_kamar = 16;
    } else if (parseInt(jenisTempatTidur) == 2) {
      jk_kamar = 15;
    } else {
      jk_kamar = jk;
    }

    if (allow_jk && allow_jk != jk) {
      docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar fleksibel tidak sesuai dengan jenis kelamin'));
      return false;
    }

    if (jk != jk_kamar) {
      docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
      return false;
    }

    $.ajax({
      url: '/ranap/end-point/cek-ruangan',
      data: {
        ruangan_id: $(identifier).data('ruangan_id'),
        kelaspelayanan_id: $(identifier).data('kelaspelayanan_id'),
        penjamin_id: $("#penjamin_id").val()
      },
      method: 'POST',
      success: () => {
        if ($(document).find('#ruanganIdHidden').val() !== $(identifier).data('ruangan_id')) {
          if ($('#pegawai_id').hasClass("select2-hidden-accessible")) {
            $('#pegawai_id').select2('destroy');
          }
          // ajax to get data pegawai_id by ruangan
          $.ajax({
            url: '/ranap/end-point/get-dokter-by-ruangan',
            data: {
              ruangan_id: $(identifier).data('ruangan_id')
            },
            method: 'GET',
            success: ({ data }) => {
              const dataDropDownPegawai = []
              Object.keys(data).map((itemOutput) => {
                dataDropDownPegawai.push({
                  id: itemOutput,
                  text: data[itemOutput]
                })
              })
              refreshOptionSelect2($('#pegawai_id'), dataDropDownPegawai)
            },
            complete: () => {
              $('#pegawai_id').prop('disabled', false)
            }
          })
        }

        $(document).find('#kamartempattidur_id').val($(identifier).data('kamartempattidur_id'));
        $(document).find('#kamarruangan_id').val($(identifier).data('kamarruangan_id'));
        $(document).find('#nokamar').val($(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));
        $(document).find('#ruanganLabelValue').html($(identifier).data('ruangan_nama'));
        $(document).find('#ruanganIdHidden').val($(identifier).data('ruangan_id'));

        $("#kelasPelayananSelected").val($(identifier).data('kelaspelayanan_id'));
        // if($("#pasienTitipanValue").val() == "1") {
        //       _keteranganKamar = ' - KAMAR TITIPAN';
        // } else if($("#pasienApsValue").val() == "1") {
        //       _keteranganKamar = ' - KAMAR APS';
        // } else {
        //       _keteranganKamar = '';
        // }

        if (typeof $("#bpjsKelas").val() !== 'undefined' && $("#bpjsKelas").val() !== '' && parseInt($("#bpjsKelas").val()) !== parseInt($(identifier).data('kelaspelayanan_id'))) {
            $(document).find('#ruanganLabelValue').append(` ${$(identifier).data('kelaspelayanan_nama')} ` + _keteranganKamar + ` (${($("#bpjsKelas").val() < $(identifier).data('kelaspelayanan_id') && $(identifier).data('kelaspelayanan_id') <= 3) ? 'TURUN KELAS' : 'NAIK KELAS'})`)
        } else if (typeof $("#bpjsKelas").val() === 'undefined' && parseInt($("#kelaspelayanan_id").val()) !== parseInt($(identifier).data('kelaspelayanan_id'))) {
            $(document).find('#ruanganLabelValue').append(` ${$(identifier).data('kelaspelayanan_nama')} ` + _keteranganKamar)
        }

        $("#btnCariKamar").focus();
        $('#modalTempatTidur').modal('hide');
      },
      error: ({ responseJSON }) => {
        docoNotification('error', responseJSON.meta.message, '')
      },
      complete: () => {
        hideLoader()
        $.unblockUI()
      }
    });
}

$('.pickadate').pickadate({
    format: 'dd mmm yyyy',
    formatSubmit: 'yyyy-mm-dd',
    onStart: function () {
        var date = new Date();
        this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
    }
});
$('.detail_peserta').hide();
$('.detail_asuransi').hide();
$('.panel-rujukan').hide();

$(document).ready(function() {
    validasiStatusBayar()
    $('#selectCarabayar').trigger('change');
    $('#addSelectCarabayar2').trigger('change');
    $('#editpendaftaranform-is_multi_payer').trigger('change');
    var _groupCaraBayar = $("#selectCarabayar").find(':selected').attr('data-id');
    if (_groupCaraBayar != 418) {
        $('.field-no_sep').hide();
        $('#btn-create-sep').hide();
    } else if(_groupCaraBayar != 419) {
        $('.field-asuransiform-nokartuasuransi').hide();
    }
});

function validasiStatusBayar () {
    if (disabledCaraBayarPenjamin) {
        var caraBayarId = $('#selectCarabayar')
        caraBayarId.select2();
        $('<input>').attr({
            type: 'hidden',
            id: 'caraBayarIdHIdden',
            name: 'EditPendaftaranForm[carabayar_id]'
        }).appendTo('form');
        $('#caraBayarIdHIdden').val(caraBayarId.val());
        caraBayarId.prop('disabled', true);

        var penjaminId = $('#penjamin_id')
        penjaminId.select2();
        $('<input>').attr({
            type: 'hidden',
            id: 'penjaminIdHIdden',
            name: 'EditPendaftaranForm[penjamin_id]'
        }).appendTo('form');
        $('#penjaminIdHIdden').val(penjaminId.val());
        penjaminId.prop('disabled', true);

        var kelasTanggunganId = $('#selectKelasTanggungan')
        kelasTanggunganId.select2();
        $('<input>').attr({
            type: 'hidden',
            id: 'kelasTanggunganIdHidden',
            name: 'AsuransiForm[kelastanggungan_id]'
        }).appendTo('form');
        $('#kelasTanggunganIdHidden').val(kelasTanggunganId.val());
        kelasTanggunganId.prop('disabled', true);

        $('input[name="AsuransiForm[status_konfirmasi]"]').on('click', function(event) {
            event.preventDefault();
        });

        $('#limit_tagihan').prop('readonly', true);       
    }
}

$('.selectAsalRujukan').change(function(){
    $('.rujukan-panel').slideUp();
    var cb_id = $('#selectCarabayar').val();
    if($(this).val() != '1' && $(this).val() != '' && cb_id != '2' && cb_id != '6'){
        $('.rujukan-panel').slideDown();
    }
});

$(document).on("change", "#selectCarabayar", function(){
    var carabayar_group = $("#selectCarabayar").find(':selected').attr('data-id');
    $("#editpendaftaranform-group_carabayar").val(carabayar_group);

    if (disabledCaraBayarPenjamin) {
        $('input[name="EditPendaftaranForm[is_multi_payer]"]').prop('disabled', true);
    } else {
        if (carabayar_group == caraBayarGroupUmum && supportMultipayer) {
            $('input[name="EditPendaftaranForm[is_multi_payer]"]').prop('disabled', true);
            $('input[name="EditPendaftaranForm[is_multi_payer]"]').prop("checked", false).change();
            $('#multiPayerInfoMessage').show();
        } else {
            $('input[name="EditPendaftaranForm[is_multi_payer]"]').prop('disabled', false);
            $('#multiPayerInfoMessage').hide();
        }
    }

    if(carabayar_group == 418) {
        $('.field-no_sep').show();
        if($('#no_sep').val() != '') {
            $('.detail_peserta').show();
            $('#btn-create-sep').hide();
        }
        else {
            $('.detail_peserta').hide();
            $('#btn-create-sep').show();
        }
    }
    else {
        $('.field-no_sep').hide();
        $('.detail_peserta').hide();
        $('#btn-create-sep').hide();
    }

    if(carabayar_group == 419) {
        $('.field-asuransiform-nokartuasuransi').show();
        $(".detail_asuransi").show();
    }
    else {
        $('.field-asuransiform-nokartuasuransi').hide();
        $(".detail_asuransi").hide();
    }
});

$(document).on("change", "#addSelectCarabayar2", function(){
    var carabayar_group = $("#addSelectCarabayar2").find(':selected').attr('data-id');
    // $("#editpendaftaranform-group_carabayar").val(carabayar_group);

    if(carabayar_group == 419) {
        $('.field-multicarabayarform-add_no_asuransi_1').show();
        $(".detail_asuransi-2").show();
    }
    else {
        $('.field-multicarabayarform-add_no_asuransi_1').hide();
        $(".detail_asuransi-2").hide();
    }
});

$(document).on('change','#carabayar_id',function () {
    var carabayar_group = $(this).find(':selected').attr('data-id');
    $("#editpendaftaranform-group_carabayar").val(carabayar_group);
    var rujukan_id = $('.selectAsalRujukan').val();
    if($(this).val() !== ''){
        $('.detail_peserta').slideUp();
        $('.detail_asuransi').slideUp();
        $('.panel-rujukan').slideUp();
        switch($(this).val()){
            case '2':
                $('.detail_asuransi').slideDown();
                break;
            case '6':
                $('.detail_peserta').slideDown();
                break;
            default:
                if(carabayar_group == '417' && rujukan_id != '1' && rujukan_id != ''){
                    $('.panel-rujukan').slideDown();
                }
        }
    }
});
$('.btn-simpan-perubahan').on('click',function(e){
    var carabayar_id = $('#carabayar_id').val();
    var carabayar_group = $('#carabayar_id').find(':selected').attr('data-id');
    var rujukan_id = $('.selectAsalRujukan').val();
    if(carabayar_id !== ''){
        switch(carabayar_id){
            case '2':
                    $('#asuransi-form').submit();
                break;
            case '6':
                    $('#form-update-ranap').submit();
                break;
            default:
                if(carabayar_group == '417' && rujukan_id != '1' && rujukan_id != ''){
                    $('#rujukan-form').submit();
                }else{
                    $('#form-update-ranap').submit();
                }
        }

    }
});
$('#rujukan-form').on('beforeSubmit', function(e){
    var asalrujukan_id = $('#pendaftaranform-asalrujukan_id').val();
    var pasien_id = $('#pendaftaranform-pasien_id').val();
    var pendaftaran_id = $('#pendaftaranform-pendaftaran_id').val();
    var penjamin_id = $('#penjamin_id').val();
    var carabayar_id = $('#carabayar_id').val();
    var form = $(this);
    var formData = form.serializeArray();
    formData.push({name:'RujukanForm[asalrujukan_id]', value:asalrujukan_id});
    formData.push({name:'RujukanForm[pasien_id]', value:pasien_id});
    formData.push({name:'RujukanForm[penjamin_id]', value:penjamin_id});
    formData.push({name:'RujukanForm[carabayar_id]', value:carabayar_id});
    formData.push({name:'RujukanForm[pendaftaran_id]', value:pendaftaran_id});
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        beforeSend: function(){
        },
        success: function(data){
            $('#form-update-ranap').submit();
        },
        error: function(data){
            txt = "Kesalahan Internal";
            console.log(data);
            if(data.responseJSON){
                if(data.response != undefined){
                    if(data.response.text != undefined){
                        txt = data.response.text;
                    }
                }
            }
            $(function(){
                new PNotify({
                    title: "Terjadi Kesalahan",
                    text: txt,
                    addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                    type: "warning"
                });
            });
        },
        complete: function(){

        }
    });
}).on('submit',function(e){
    e.preventDefault();
});
$('#asuransi-form').on('beforeSubmit', function(e){
    var asalrujukan_id = $('#pendaftaranform-asalrujukan_id').val();
    var pasien_id = $('#pendaftaranform-pasien_id').val();
    var pendaftaran_id = $('#pendaftaranform-pendaftaran_id').val();
    var penjamin_id = $('#penjamin_id').val();
    var carabayar_id = $('#carabayar_id').val();
    var form = $(this);
    var formData = form.serializeArray();
    formData.push({name:'AsuransiForm[asalrujukan_id]', value:asalrujukan_id});
    formData.push({name:'AsuransiForm[pasien_id]', value:pasien_id});
    formData.push({name:'AsuransiForm[penjamin_id]', value:penjamin_id});
    formData.push({name:'AsuransiForm[carabayar_id]', value:carabayar_id});
    formData.push({name:'AsuransiForm[pendaftaran_id]', value:pendaftaran_id});
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        beforeSend: function(){
        },
        success: function(data){
            $('#form-update-ranap').submit();
        },
        error: function(data){
            txt = "Kesalahan Internal";
            if(data.responseJSON){
                if(data.response != undefined){
                    if(data.response.text != undefined){
                        txt = data.response.text;
                    }
                }
            }
            $(function(){
                new PNotify({
                    title: "Terjadi Kesalahan",
                    text: txt,
                    addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                    type: "warning"
                });
            });
        },
        complete: function(){

        }
    });
}).on('submit',function(e){
    e.preventDefault();
});
$('#form-update-ranap').on('beforeSubmit', function(e){
    // e.preventDefault();
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        beforeSend: function(){
            var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay"></div>';
            var dialogTemplate = '<div id="confirm-dialog">';
                    dialogTemplate += '<div class="dialog-content">';
                        dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p class=\"confirm-message-text\"></p>';
                    dialogTemplate += '</div>';
                dialogTemplate += '</div>';

                $('body').append(overlayTemplate);
                $('body').append(dialogTemplate);
                $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw" style="margin:18px 0 19px 0;"></i>&nbsp;Sedang memproses . . .');
            $('.form-group').removeClass('has-error');
            $('span.help-block.error').remove();
            $('div.help-block.error').remove();
        },
        success: function(data){
            var succTitle = 'Proses Berhasil';
            var succMsg = 'Data Berhasil Disimpan!';
            $(function(){
                new PNotify({
                    title: succTitle,
                    text: succMsg,
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: 'success',
                });
            });
        },
        error: function(data){
            txt = "Kesalahan Internal";
            if(data.responseJSON){
                txt = data.response.text;
            }
            $(function(){
                new PNotify({
                    title: "Terjadi Kesalahan",
                    text: txt,
                    addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                    type: "warning"
                });
            });
        },
        complete: function(){
            hideQuestionDialog();
            $('body').find('.confirm-dialog-overlay').remove();

        }
    });
}).on('submit',function(e){
    e.preventDefault();
});

$(document).ready(function () {
    progress.css("display", "none")
    $("#btn-save").on('click', function (event) {
        event.preventDefault();
        var dataPost = $("#form-update-rajal").serializeArray();
        $(this).docoForm("click", {
            data: dataPost,
            method: 'post',
            // skipSuccessNotif: true,
            success: function (data) {
                let response = data.response;
                if (response.data.is_edited_tindakan != undefined && response.data.is_edited_tindakan == true) {
                    randString = pendaftaranId+':edit_pendaftaran';
                    // $('#modal_progress').modal('toggle');
                    // showProgressBar(randString)
                    $('#btn-kembali').trigger('click')
                } else {
                    $('#btn-kembali').trigger('click')
                }
            },
            error: function(data) {
                var response = data.responseJSON.response;
                if(typeof response.is_bpjs != 'undefined') {
                    var is_bpjs = response.is_bpjs;
                    var title = response.title;
                    var message = response.text;

                    if(is_bpjs) {
                        (new PNotify({
                            title: title,
                            text: message,
                            addclass: "alert alert-success alert-arrow-right alert-styled-right",
                            type: "success",
                            buttons: {
                                closer: false,
                                sticker: false
                            },
                            hide: false,
                            confirm: {
                                confirm: true,
                                buttons: [{
                                        text: 'Ya',
                                        addClass: 'btn btn-xs btn-success',
                                    },
                                    {
                                        text: 'Tidak',
                                        addClass: 'btn btn-xs btn-danger',
                                    }
                                ]
                            },
                            history: {
                                history: false
                            }
                        })).get().on('pnotify.confirm', function(){
                            $("#allow-bpjs").val(1);

                        }).on('pnotify.cancel', function() {
                            $("#allow-bpjs").val(0);
                        });
                    }
                }

                if(response?.is_integrasi != undefined && response?.is_integrasi == true) {
                    $("#modal_validation").modal('show');
                }
            }
        });
    });
});

$('#btn-kembali').on('click', function () {
    window.location.href = "/pendaftaran/informasi-pasien"
});

$(document).on('click', '.field-asuransiform-nokartuasuransi span.input-group-addon', function (event) {
    event.preventDefault();
    $('#asuransiform-nokartuasuransi').val('');

    $('#asuransiform-namapemilikasuransi').val('');
    $('#asuransiform-nomorpokokperusahaan').val('');
    $('#asuransiform-kelastanggungan_id').val(null).trigger('change');
    $('#asuransiform-namaperusahaan').val('');
    $('#tgl_konfirmasi').val(null).trigger('change');

    $('#asuransiform-nokartuasuransi').prop('readonly', false);
    $('.field-asuransiform-nokartuasuransi').find("#note-asuransi").html("");

});

$('#cari-nosep').click(function() {
    var nosep = $('#no_sep').val();
    if(nosep == "") {
        showErrorSep('No. SEP harus diisi.');
        return false;
    }

    $.ajax({
        type: 'POST',
        url: window.location.origin + '/api/bpjs/cari-sep?additional=hak_kelas',
        data: {
            id: getUrlParam('id'),
            nosep: nosep,
        },
        dataType: 'JSON',
        beforeSend: function () {
            disableButtonCari();
            showErrorSep('');
            showLoader();
        },
        success: function (res) {
            if(res.message !== 'undefined') {
                showErrorSep(res.message);
                enableButtonCari();
            }

            if(typeof res.data !== 'undefined') {
                if(res.data.response == "") {
                    var message = "Maaf Server BPJS sedang mengalami gangguan";
                    showErrorSep(message);
                    enableButtonCari();
                }

                var resbpjs = res.data.metadata;
                if(resbpjs.status == 'undefined') {
                    var message = "Maaf Server BPJS sedang mengalami gangguan";
                    showErrorSep(message);
                    enableButtonCari();
                }
                else if (resbpjs.status != "200") {
                    var message = resbpjs.message;
                    showErrorSep(message);
                    enableButtonCari();
                }
                else {
                    var responseBpjs = res.data.response.metaData;
                    if(responseBpjs.code == 201) {
                        showErrorSep(responseBpjs.message);
                        enableButtonCari();
                    }
                    else if(responseBpjs.message == 'OK') {
                        showErrorSep(responseBpjs.code);
                        enableButtonCari();
                    }
                    else {
                        if(responseBpjs.code == 200) {
                            if(res.data.response !== "") {
                                var nama_pasien = $("#editpendaftaranform-nama_pasien").val();
                                var resData = res.data.response.response;
                                var additional = res.additional;

                                if(nama_pasien.toLowerCase() !== resData.peserta.nama.toLowerCase()) {
                                    (new PNotify({
                                        title: "Peringatan",
                                        text: "Nama Pasien yang diinputkan berbeda dengan Nama BPJS <br>"
                                            +" Nama Pasien BPJS : <strong>" + resData.peserta.nama
                                            +" </strong><br> Nama Pasien yang diinputkan : <strong>" + nama_pasien
                                            +" </strong><br> Apakah Anda yakin akan melanjutkan proses? ",
                                        addclass: "alert alert-success alert-arrow-right alert-styled-right",
                                        type: "success",
                                        buttons: {
                                            closer: false,
                                            sticker: false
                                        },
                                        hide: false,
                                        confirm: {
                                            confirm: true,
                                            buttons: [{
                                                    text: 'Ya',
                                                    addClass: 'btn btn-xs btn-success',
                                                },
                                                {
                                                    text: 'Tidak',
                                                    addClass: 'btn btn-xs btn-danger',
                                                }
                                            ]
                                        },
                                        history: {
                                            history: false
                                        }
                                    })).get().on('pnotify.confirm', function(){
                                        showPeserta(resData, additional);
                                        enableButtonCari();
                                        $("#allow-bpjs").val(1);
                                    }).on('pnotify.cancel', function() {
                                        enableButtonCari();
                                        $(".detail_peserta").hide();
                                        $("#allow-bpjs").val(0);
                                    });
                                }

                                enableButtonCari();
                            }
                        } else if(responseBpjs.code == 404) {
                            var message = res.data.response.metaData.message;
                            showErrorSep(message);
                        }
                    }
                }
            }
        },
        complete: function() {
            enableButtonCari();
            hideLoader();
        }
    });
});

var disableButtonCari = function() {
    var html = i18next.t('Memuat...');
    $('#cari-nosep').html(html).prop('disabled', true);
    setTimeout(function(){
        $("#btn-save").prop("disabled", true);
    }, 500);
}

var enableButtonCari = function() {
    var html = '<i class="fa fa-search"></i> ' + i18next.t('Cari No SEP');
    $('#cari-nosep').html(html).prop('disabled', false);
    setTimeout(function(){
        $("#btn-save").prop("disabled", false);
    }, 500);
}

var showErrorSep = function(html) {
    $('.err-nosep').html(html);
    setTimeout(function(){
        $("#btn-save").prop("disabled", true);
    }, 500);
}

var showPeserta = function(resData, additional) {
    var namaPeserta = resData.peserta.nama;
    var noKartu = resData.peserta.noKartu;
    var tglLahir = resData.peserta.tglLahir;
    var jnsPeserta = resData.peserta.jnsPeserta;
    var hakKelas = resData.peserta.hakKelas;
    var jnsPelayanan = resData.jnsPelayanan;
    var poli = resData.poli;
    var kelasRawat = resData.kelasRawat;

    if(typeof additional != 'undefined') {
        var res_additional = additional.response.response;
        var hakKelas = res_additional.peserta.hakKelas;
        var hakKelas_kode = hakKelas.kode;
        var hakKelas_nama = hakKelas.keterangan;
    }

    $(".detail_peserta").show();
    $("#bpjsnew_detail_nama").html(namaPeserta);
    $("#bpjsnew_detail_no_kartu").html(noKartu);
    $("#bpjsnew_detail_jenis_pelayanan").html(jnsPelayanan);
    $("#bpjsnew_detail_poli").html(poli);
    $("#bpjsnew_detail_tglLahir").html(tglLahir);
    $("#bpjsnew_detail_jnsPeserta").html(jnsPeserta);
    $("#bpjsnew_detail_hakKelas").html(hakKelas_nama);
    $("#bpjsKelas").val(hakKelas_kode);

}

if($('#no_sep').data('required') == 1) {
    $(".field-no_sep").addClass('required');
}
$("#penjamin_id").on("change", function(){
    var carabayar_group = $("#selectCarabayar").find(':selected').attr('data-id');
    var _value = $(this).val();
    $('#add_penjamin_id_1 > option').each(function(i, item){
        if ($(item).val() == _value) {
            $(item).prop('disabled', true);
        } else {
            $(item).prop('disabled', false);
        }
    })
    $('#add_penjamin_id_1').select2();

    if(carabayar_group == 418) {
        if(_penjamin_id != _value) {
            $("#no_sep").prop("readonly", false);
            $("#no_sep").val("").trigger("change");
            $(".detail_peserta").hide();
        }
        else {
            $("#no_sep").val(_nosep).trigger("change");
            $("#no_sep").prop("readonly", true);
            $(".detail_peserta").show();
        }
    } else if(carabayar_group == 419) {
        if(_penjamin_id != _value) {
            $(".field-asuransiform-nokartuasuransi span.input-group-addon").trigger("click");
            $("#asuransiform-nokartuasuransi").prop("readonly", false);
            $("#asuransiform-status_konfirmasi").prop("checked", false);
        }
        else {
            $("#asuransiform-nokartuasuransi").val(_noasuransi);
            $("#asuransiform-nokartuasuransi").prop("readonly", true);
            $("#asuransiform-namapemilikasuransi").val(_namapemilik);
            $("#asuransiform-nomorpokokperusahaan").val(_nomorpokokperusahaan);
            $("#asuransiform-kelastanggungan_id").val(_kelastanggungan_id).trigger("change");
            $("#tgl_konfirmasi").val(_tgl_konfirmasi).trigger("change");
            $("#asuransiform-namaperusahaan").val(_namaperusahaan);
            if(_status_konfirmasi == 1) {
                $("#asuransiform-status_konfirmasi").prop("checked", true);
            }
            else {
                $("#asuransiform-status_konfirmasi").prop("checked", false);
            }
        }
    }
});

$("#add_penjamin_id_1").on("change", function(){
    var carabayar_group = $("#addSelectCarabayar2").find(':selected').attr('data-id');
    var _value = $(this).val();

    $('#penjamin_id > option').each(function(i, item){
        if ($(item).val() == _value) {
            $(item).prop('disabled', true);
        } else {
            $(item).prop('disabled', false);
        }
    })
    $('#penjamin_id').select2();

    if(carabayar_group == 419) {
        if(modelMultiPayer.add_penjamin_id_1 != _value) {
            $(".field-multicarabayarform-add_no_asuransi_1 span.input-group-addon").trigger("click");
            $("#multicarabayarform-add_no_asuransi_1").prop("readonly", false);
            $("#multicarabayarform-add_status_konfirmasi_1").prop("checked", false);
        }
        else {
            $("#multicarabayarform-add_no_asuransi_1").val(modelMultiPayer.add_no_asuransi_1);
            $("#multicarabayarform-add_no_asuransi_1").prop("readonly", true);
            $("#multicarabayarform-add_namapemilikasuransi_1").val(modelMultiPayer.add_namapemilikasuransi_1);
            $("#multicarabayarform-add_nomorpokokperusahaan_1").val(modelMultiPayer.add_nomorpokokperusahaan_1);
            $("#multicarabayarform-add_kelastanggungan_id_1").val(modelMultiPayer.add_kelastanggungan_id_1).trigger("change");
            $("#multicarabayarform-add_tgl_konfirmasi").val(modelMultiPayer.add_tgl_konfirmasi_1).trigger("change");
            $("#multicarabayarform-add_namaperusahaan_1").val(modelMultiPayer.add_namaperusahaan_1);
            if(modelMultiPayer.add_status_konfirmasi_1 == 1) {
                $("#multicarabayarform-add_status_konfirmasi_1").prop("checked", true);
            }
            else {
                $("#multicarabayarform-add_status_konfirmasi_1").prop("checked", false);
            }
        }
    }
});

// trigger when parent changed
$('#penjamin_id').on('depdrop:change', function(event) {
    let _value = $('#add_penjamin_id_1').val();
    $('#penjamin_id > option').each(function(i, item){
        if ($(item).val() == _value) {
            $(item).prop('disabled', true);
        } else {
            $(item).prop('disabled', false);
        }
    })
});

// trigger when parent changed
$('#add_penjamin_id_1').on('depdrop:change', function(event) {
    let _value = $('#penjamin_id').val();
    $('#add_penjamin_id_1 > option').each(function(i, item){
        if ($(item).val() == _value) {
            $(item).prop('disabled', true);
        } else {
            $(item).prop('disabled', false);
        }
    })
});

$(document).ready(function() {
    $("#editpendaftaranform-tgl_admisi").AnyTime_noPicker();
    $("#editpendaftaranform-tgl_admisi").AnyTime_picker({
      format: "%d-%m-%Y %H:%i",
      latest: new Date(),
    });
})

$(document).on('change', '#pegawai_id', function () {
    var depdrop_parents = [];
    var depdrop_params = [];
    depdrop_parents.push($(this).val());
    depdrop_params.push(ruangan_id);

    $.ajax({
        url: '/pendaftaran/daftar/get-nomor-urut',
        method: 'POST',
        data: {
            depdrop_parents: depdrop_parents,
            depdrop_params: depdrop_params,
            is_update: true
        },
        success: (response) => {
            refreshOptionSelect2($('#nomor_urut'), response.output);
            $('#nomor_urut').val(response.selected).trigger('change')
        },
        error: ({response}) => {
            docoNotification('error', responseJSON.meta.message, '')
        },
        complete: () => {
            hideLoader()
            $.unblockUI()
        }
    });
});

function getUrlParam(param) {
    var url = window.location.search.substring(1);
    var variable = url.split('&');
    for (i = 0; i < variable.length; i++) {
        paramName = variable[i].split('=');
        if (paramName[0] === param) {
            return paramName[1] === undefined ? true : decodeURIComponent(paramName[1]);
        }
    }
    return false;
};

$('.btn-selesai').on('click', function() {
    $('#btn-kembali').trigger('click');
});

$('#btn-create-sep').on('click', function() {
    var pendaftaran_id = getUrlParam('id');
    window.open('/pendaftaran/informasi-pasien/create-sep?id=' + pendaftaran_id + '&jenis=' + jenisPendaftaran, '_blank');

    var getSession = setInterval(function() {
        if (localStorage.getItem("createSep-" + pendaftaran_id)) {
            var nosep = localStorage.getItem("createSep-" + pendaftaran_id);
            docoNotification('success', 'Pembuatan SEP Berhasil!', 'SEP berhasil diterbitkan dengan nomor ' + nosep, false)

            localStorage.removeItem("createSep-" + pendaftaran_id);
            clearInterval(getSession);
        }
    },1500);
});

$(document).on('change', '#editpendaftaranform-is_multi_payer', function (e) {
    e.preventDefault()
    // _formPendaftaran.resetForm();
    // $('.field-multicarabayarform-add_no_asuransi_1').hide();

    if ($(this).prop("checked")) {
        $('.form-multi-carabayar').show()
    } else {
        $('.form-multi-carabayar').hide()
        resetMultiPayer('MultiCarabayarForm');
    }
})

function resetMultiPayer(formName) {
    $(`[name^="${formName}"]`).each(function(i, item){
        $(item).val('').change();
    })
}

async function showProgressBar(randString) {
    let config = await $.getJSON("./../../json/setup.json")
    if (config.origin == "true") {
        var socket = io.connect(window.location.origin);
    } else {
        var socket = io.connect(config.ip+':'+config.port);
    }
    const channel = `export-excel:`
    startTime = Date.now();
    var counting = 0;
    startRemainingTime();
    socket.on(channel + randString, (message) => {
        counting++;
        const _data = $.parseJSON(message);
        const { status , totaltindakanobat, messageProcess} = _data
        $('.hr1').removeClass('hidden')
        $('.hr2').removeClass('hidden')
        if(typeof totaltindakanobat != 'undefined') {
            if(status == 1) {
                $(".label-total").html(`<p style="font-size:12px;font-weight:bold;">Total Tarif Obat/Tindakan : ${totaltindakanobat}</p>`)
                $(".label-processed").html(`<p style="font-size:12px;font-weight:bold;">Progress Pengupdatean : ${counting}</p>`)
                $(".label-tindakan-obat").html(`<p style="font-size:12px;font-weight:bold;"><span style="color:green">&#10003;</span> ${messageProcess}</p>`)
                if(counting == parseInt(totaltindakanobat)) {
                    $(".label-finish").html(`<p style="font-size:12px;font-weight:bold;">Tarif Obat dan Tindakan sudah terupdate!</p>`)
                    elapsedTime += Date.now() - startTime;
                    clearTimeout(timeoutId);
                    $('.btn-selesai').removeClass('hidden');
                }
            }
            else if(status == 'failed') {
                docoNotification('error','Proses Gagal!', messageProcess)
            }
        }
    });
}

function startRemainingTime() {
    timeoutId = setTimeout(function(){
        const time = Date.now() - startTime + elapsedTime;
        const seconds = parseInt((time/1000)%60)
        const minutes = parseInt((time/(1000*60))%60)
        const hour = parseInt((time/(1000*60*60))%24);

        displayTime(hour, minutes, seconds);
        startRemainingTime()
    }, 100)
}

function displayTime(hour, minutes, seconds) {
    hour = hour < 10 ? '0'+hour : hour ;
    minutes = minutes < 10 ? '0'+minutes : minutes ;
    seconds = seconds < 10 ? '0'+seconds : seconds ;
    $(".label-time").html(`<p style="font-size:12px;font-weight:bold;">Waktu Tunggu : ${hour+':'+minutes+':'+seconds}</p>`)
    // return hour+':'+minutes+':'+seconds;
}

$('#btn-log-pendaftaran').on('click', function() {
    _this = $(this);
    _this.button('loading');
    url = _this.attr('action');
    modal = $('#modal_log_pendaftaran');
    $('.modal-content', modal).empty();
    var _html = '<div class="text-center">';
    _html +=
      '<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>' +
      i18next.t('memuat') +
      ' . . . </b></h3>';
    _html += '</div>';
    modal
      .find('.modal-content')
      .html(_html)
      .load(url, function (responseText, statusText, xhr) {
          if (statusText == 'success') {
            _this.button('reset');
            modal.modal('show');
          }
        });
});