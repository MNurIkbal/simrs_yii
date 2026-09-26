/* 
    Author : Rizal Faidin
*/
var pemeriksaanlab = {};


$(document).ready(function () {
    var yesterday = new Date((new Date()).valueOf()-1000*60*60*24);
    $('.pickadate').pickadate({
        format: 'dd mmmm yyyy',
        // onStart: function () {
        //     var date = new Date()
        //     this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
        // },
        disable: [
            { from: [0,0,0], to: yesterday }
          ]
    });

    if (instalasi_id) {
        $('#penunjang_instalasi_id').val(instalasi_id).trigger('change').trigger('depdrop:change');
        $('#penunjang_ruangan_id').on('depdrop:afterChange', function (event, id, value) {
            $(this).val(ruangan_id);
        });
    }
});


$('#penunjang_ruangan_id').change(function(){
    if ($(this).val() == '') {
        $('.btn-pemeriksaan-tambah').prop('disabled', true);
    } else {
        $('.btn-pemeriksaan-tambah').prop('disabled', false);
    }
});

$('.penunjang-bedah').hide();
$('#penunjang_instalasi_id').change(function() {
    $('#tgl_permintaan_info').html('-');
    $('#jam_mulai_info').html('-');
    $('#jam_selesai_info').html('-');
    $('#dr_operator_info').html('-');
    $('#dr_anestesi_info').html('-');
    $('#penunjang_has_jadwal').val('0');
    if ($(this).val() == '12') {
        $('.penunjang-bedah').show();
        $('.btn-buka-jadwal').attr('disabled', false);
    } else {
        $('.penunjang-bedah').hide();
        $('.btn-buka-jadwal').attr('disabled', true);
    }

    pemeriksaanlab = {};
    loadpemeriksaan(pemeriksaanlab);
});


$('#save-terapi-penunjang').click(function(e) {
// $('#terapi-penunjang-form').submit(function(e) {
    $('.form-group').removeClass('has-error')
    $('.help-block.error').remove()
    e.preventDefault();
    var catatan = $('#instruksiform-catatan_instruksi').val();
    if (catatan == null || catatan == "") {
        docoNotification("error", "Catatan Belum Diisi!", "mohon isi dulu catatan!");
        var logo =
                '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
        var _field_catatan = $('#instruksiform-catatan_instruksi')
                _field_catatan.parent('div').addClass('has-error')
                _field_catatan.parent('.required').addClass('has-error')
                _field_catatan.after(
                    '<span class="help-block error">' +
                        logo +
                        'Catatan Harus Diisi' +
                        '</span>'
                )
        return false
    }
    var data = $('#terapi-penunjang-form').serializeArray();
    if ($.isEmptyObject(pemeriksaanlab)) {
        new PNotify({
            title: 'Proses Gagal !',
            text: 'Pemeriksaan tidak boleh kosong',
            addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
            type: 'error'
        });
    } else {
        data.push({name:'periksalab', value:JSON.stringify(pemeriksaanlab)});
        data.push({name:'catatan_terapi', value:$('#instruksiform-catatan_instruksi').val()});
        data.push({name:'cppt_id', value:$('#instruksiform-cppt_id').val()});
        data.push({name:'pegawai_id', value:$('#penunjang_pegawai_id').val()});
        data.push({name:'pasien_id_now', value:pasien_id_now});
        data.push({name:'ruangan_now', value:ruangan_now});

        // console.log(data);return;
        $().docoForm('click',{
            data:data,
            url: $('#terapi-penunjang-form').attr('action'),
                success : function (data) {
                pemeriksaanlab = {}
                loadpemeriksaan(pemeriksaanlab)

                var instruksi_id = data.response.instruksi_id;

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
                    window.open("/igd/pemeriksaan-igd/cetak-penunjang?id="+pendaftaran_id+"&instruksi_id="+instruksi_id);
                }).on('pnotify.cancel', function() {

                });

                $("#btn-back-terapi").trigger('click');
            }
        });
    }
    // alert('under construction');
});

$('#muat-ulang').click(function(e) {
    e.preventDefault();

    var date = new Date();
    var picker = $('.pickadate').pickadate('picker');

    if (!$.isEmptyObject(pemeriksaanlab)) {
        var header = "Perhatian !";
        var message = "Apakah Anda yakin untuk muat ulang? Jika yakin maka order terakhir akan terhapus dan tidak tersimpan.";
        var label = { 
            buttons: {
                'No': 'btn btn-danger',
                'Yes': 'btn btn-success btn-yes'
            }
        };
        $.showQuestionDialog(header, message, label, function(reaction) {
            if (reaction == 'Yes') {
                $('#penunjang_instalasi_id2').val('');
                $('#penunjang_ruangan_id2').val('');
                $('#penunjang_instalasi_id').attr('disabled', false).val('').trigger('change.select2').trigger('depdrop:change');
                $('#penunjang_ruangan_id').attr('disabled', false);

                $('#tgl_permintaan_info').html('-');
                $('#jam_mulai_info').html('-');
                $('#jam_selesai_info').html('-');
                $('#dr_operator_info').html('-');
                $('#dr_anestesi_info').html('-');

                $('#penunjang_has_jadwal').val('0');
                
                picker.set('select', date);
                $('.btn-pemeriksaan-clear').click();
            } else {

            }
        });
    } else {
        $('#penunjang_instalasi_id2').val('');
        $('#penunjang_ruangan_id2').val('');
        $('#penunjang_instalasi_id').attr('disabled', false).val('').trigger('change.select2').trigger('depdrop:change');
        $('#penunjang_ruangan_id').attr('disabled', false);

        $('#tgl_permintaan_info').html('-');
        $('#jam_mulai_info').html('-');
        $('#jam_selesai_info').html('-');
        $('#dr_operator_info').html('-');
        $('#dr_anestesi_info').html('-');

        $('#penunjang_has_jadwal').val('0');

        picker.set('select', date);
        $('.btn-pemeriksaan-clear').click();
    }
});
