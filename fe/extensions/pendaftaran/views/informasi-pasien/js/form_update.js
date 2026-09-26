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

    var defaultPenjamin = 1;
    
    $.ajax({
      url: '/ranap/end-point/cek-ruangan',
      data: {
        ruangan_id: $(identifier).data('ruangan_id'),
        kelaspelayanan_id: $(identifier).data('kelaspelayanan_id'),
        penjamin_id: defaultPenjamin,
        kamarruanganId: kamarruangan_id
      },
      method: 'POST',
      success: () => {
        if ($(document).find('#ruanganIdHidden').val() !== $(identifier).data('ruangan_id')) {
        //   if ($('#pegawai_id').hasClass("select2-hidden-accessible")) {
        //     $('#pegawai_id').select2('destroy');
        //   }
          // ajax to get data pegawai_id by ruangan
        //   $.ajax({
        //     url: '/ranap/end-point/get-dokter-by-ruangan',
        //     data: {
        //       ruangan_id: $(identifier).data('ruangan_id')
        //     },
        //     method: 'GET',
        //     success: ({ data }) => {
        //       const dataDropDownPegawai = []
        //       Object.keys(data).map((itemOutput) => {
        //         dataDropDownPegawai.push({
        //           id: itemOutput,
        //           text: data[itemOutput]
        //         })
        //       })
        //       refreshOptionSelect2($('#pegawai_id'), dataDropDownPegawai)
        //     },
        //     complete: () => {
        //       $('#pegawai_id').prop('disabled', false)
        //     }
        //   })
        }
        
        $(document).find('#kamartempattidur_id').val($(identifier).data('kamartempattidur_id'));
        $(document).find('#kamarruangan_id').val($(identifier).data('kamarruangan_id'));
        $(document).find('#nokamar').val($(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));
        $(document).find('#ruanganLabelValue').html($(identifier).data('ruangan_nama'));
        $(document).find('#ruanganIdHidden').val($(identifier).data('ruangan_id'));
        
        $("#kelasPelayananSelected").val($(identifier).data('kelaspelayanan_id'));
        if($("#pasienTitipanValue").val() == "1") {
              _keteranganKamar = ' - KAMAR TITIPAN';
        } else if($("#pasienApsValue").val() == "1") {
              _keteranganKamar = ' - KAMAR APS';
        } else {
              _keteranganKamar = '';
        }

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
$('.detail_penanggung').hide();

$(document).ready(function() {
    $('#selectCarabayar').trigger('change');
    var _groupCaraBayar = $("#selectCarabayar").find(':selected').attr('data-id');
    if (_groupCaraBayar != 418) {
        $('.field-no_sep').hide();
        $('#btn-create-sep').hide();
    } else if(_groupCaraBayar != 419) {
        $('.field-asuransiform-nokartuasuransi').hide();
    }
    $('#editpendaftaranform-pegawai_id').prepend("<option value=''>--Pilih--</option>");

    var dokter_pengganti = $('#dokterpengganti_id').val();
    var dd_dokter = $('#editpendaftaranform-pegawai_id')
    if(dokter_pengganti) {
        dd_dokter.val('').trigger('change')
        dd_dokter.prop('disabled', true)
    } else {
        dd_dokter.prop('disabled', false)
    }

    if(_jenis_pendaftaran == 'ranap'){
        // getListDokterAll();
    }
});

$('.selectAsalRujukan').change(function(){
    $('.rujukan-panel').slideUp();
    var cb_id = $('#selectCarabayar').val();
    if($(this).val() != '1' && $(this).val() != '' && cb_id != '2' && cb_id != '6'){
        $('.rujukan-panel').slideDown();
    }
});

$(document).on("change", "#selectCarabayar", function(){
    var carabayar_group = $("#selectCarabayar").find(':selected').attr('data-id');
    var caraBayar = $('#selectCarabayar').val();
    $("#editpendaftaranform-group_carabayar").val(carabayar_group);

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
    if(carabayar_group == 417) {
        if (caraBayar == 44) {
            $(".detail_penanggung").show();
            $('.field-penanggungbiayaform-instansi').show();
            $('.field-penanggungbiayaform-noindukkaryawan').show();
            $('.field-penanggungbiayaform-namabagian').show();
            $('.field-ruangcarabayar_id').hide();
            $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Pegawai");
            $('#ruangcarabayar_id').val().trigger("change").trigger("depdrop:change");
        } else if (caraBayar == 45) {
            $(".detail_penanggung").show();
            $('.field-penanggungbiayaform-instansi').hide();
            $('.field-penanggungbiayaform-noindukkaryawan').hide();
            $('.field-penanggungbiayaform-namabagian').hide();
            $('.field-ruangcarabayar_id').show();
            $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
            $('#ruangcarabayar_id').val(_ruangcarabayar_id).trigger("change").trigger("depdrop:change");
        } else if (caraBayar == 41) {
            $(".detail_penanggung").show();
            $('.field-penanggungbiayaform-instansi').hide();
            $('.field-penanggungbiayaform-noindukkaryawan').show();
            $('.field-penanggungbiayaform-namabagian').hide();
            $('.field-ruangcarabayar_id').show();
            $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
            $('#ruangcarabayar_id').val(_ruangcarabayar_id).trigger("change").trigger("depdrop:change");
        } else {
            $(".detail_penanggung").hide();
        }
    }
    else {
        $(".detail_penanggung").hide();
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
        $('.detail_penanggung').slideUp();
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
    $("#btn-save").on('click', function (event) {
        event.preventDefault();

        let dokterPengganti = $('#dokterpengganti_id').val()
        let dokter = $('#editpendaftaranform-pegawai_id').val()

        if(!dokter && !dokterPengganti) {
            return new PNotify({
                title: 'Perhatian',
                text: 'Dokter melayani belum dipilih!',
                addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                type: 'warning'
            });
        }

        var dataPost = $("#form-update-rajal").serializeArray();
        $(this).docoForm("click", {
            data: dataPost,
            method: 'post',
            success: function (data) {
                // setTimeout(function () {
                //     window.location.href = "/pendaftaran/informasi-pasien"
                // }, 1000);

                location.reload();
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
    $('#asuransiform-namaperusahaan').val('');
    $('#masaberlakukartu').val(null).trigger('change');

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

$(".field-no_sep").addClass('required');
$("#penjamin_id").on("change", function(){
    var carabayar_group = $("#selectCarabayar").find(':selected').attr('data-id');
    var _value = $(this).val();

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
        }
        else {
            $("#asuransiform-nokartuasuransi").val(_noasuransi);
            $("#asuransiform-nokartuasuransi").prop("readonly", true);
            $("#asuransiform-namapemilikasuransi").val(_namapemilik);
            $("#asuransiform-nomorpokokperusahaan").val(_nomorpokokperusahaan);
            $("#masaberlakukartu").val(_masaberlakukartu).trigger("change");
            $("#asuransiform-namaperusahaan").val(_namaperusahaan);
        }
    }
});

$(document).ready(function() {
    $("#editpendaftaranform-tgl_admisi").AnyTime_noPicker();
    $("#editpendaftaranform-tgl_admisi").AnyTime_picker({
      format: "%d-%m-%Y %H:%i",
      latest: new Date(),
    });
    $("#editpendaftaranform-tgl_admisi").removeAttr('readonly');

    $("#editpendaftaranform-tgl_pendaftaran").AnyTime_noPicker();
    $("#editpendaftaranform-tgl_pendaftaran").AnyTime_picker({
      format: "%d-%m-%Y %H:%i",
      latest: new Date(),
    });
    $("#editpendaftaranform-tgl_pendaftaran").removeAttr('readonly');
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

function getListDokterAll() {
    if ($('#pegawai_id_ranap').hasClass("select2-hidden-accessible")) {
      $('#pegawai_id_ranap').select2('destroy');
    }
    $.ajax({
      url: '/pendaftaran/end-point/search-dokter',
      method: 'GET',
      dataType: 'json',
      success: (data) => {
        const dataDropDownDokter = []
        var placeholder = { id: "", text: "-- Pilih --" }
        dataDropDownDokter.push(placeholder);
  
        for (var i = 0; i < data.results.length; i++) {
          dataDropDownDokter.push(data.results[i]);
        }
        refreshOptionSelect2($('#pegawai_id_ranap'), dataDropDownDokter)
      },
      complete: () => {
        $('#pasienadmisiform-dokterkonsul_id').prop('disabled', false)
        $('#pasienadmisiform-dokterpengirim_id').prop('disabled', false)
        $('#pegawai_id_ranap').prop('disabled', false)
        $('#pegawai_id_ranap').val(pegawai_id).trigger('change');
      }
    })
}  

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

$('#btn-create-sep').on('click', function() {
    var pendaftaran_id = getUrlParam('id');
    window.open('/pendaftaran/informasi-pasien/create-sep?id=' + pendaftaran_id + '&jenis=' + _jenis_pendaftaran, '_blank');

    var getSession = setInterval(function() {
        if (localStorage.getItem("createSep-" + pendaftaran_id)) {
            var nosep = localStorage.getItem("createSep-" + pendaftaran_id);
            docoNotification('success', 'Pembuatan SEP Berhasil!', 'SEP berhasil diterbitkan dengan nomor ' + nosep, false)
            
            localStorage.removeItem("createSep-" + pendaftaran_id);
            clearInterval(getSession);
        }
    },1500);
});
