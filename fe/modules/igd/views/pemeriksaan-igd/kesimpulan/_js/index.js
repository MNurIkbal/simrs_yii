
window.iteration = 0;
window.list_obat = [];
var tabel_reseptur_kesimpulan;
var racikan_append = 'racikan_append_';
var racikan_append_satuan = 'racikan_append_satuan_';
var racikan_append_satuan_id = 'racikan_append_satuan_id_';
var racikan_append_harga = 'racikan_append_harga_';
var racikan_append_stok_tersedia = 'stok_sisa_r_';
var racikan_append_qty = 'qty_reseptur-';
var racikan_append_obatalkes_nama = 'racikan_append_obatalkes_nama_';
var key_racikan = "racikan";
var key_nonracikan = "nonracikan";


$('#col_kesimpulan_keluar').collapse("hide");
$('#col_kesimpulan_pulang').collapse("hide");
$('.select2').select2();
$('.field-pasienpulangform-tgl_meninggal').hide();
if ($('#pasienpulangform-carakeluar_id').val() == '4') {
    $('.field-pasienpulangform-tgl_meninggal').show();
}

$('#pasienpulangform-infeksi--1').on('click', function () {
    $('#pasienpulangform-infeksi--0').prop('checked', false)
})

$('#pasienpulangform-infeksi--0').on('click', function () {
    $('#pasienpulangform-infeksi--1').prop('checked', false)
})


$('#pasienpulangform-carakeluar_id').on('change', function () {
    var valuedata = $(this).val();
    if (valuedata) {
        $.ajax({
            type: 'GET',
            url: '/igd/end-point/get-data-kondisi-keluar?carakeluar_id=' + valuedata,
            success: function (response) {
                var select = $('#kondisipulang_id');
                select.children().remove();
                $('#kondisipulang_id').append($('<option>', { value: '' }).text('-- Pilih --'));
                $.each(response.result, function (index, item) {
                    $('#kondisipulang_id').append($('<option>', { value: item.id }).text(item.text).attr('data-carakeluar_id', item.parent));
                });
                $("#kondisipulang_id").prop("disabled", false);
            }
        });
        $(".head_kesimpulan_keluar").show();
        $(".head_kesimpulan_pulang").show();
        $(".div_pulang_keluar").show();
        $('#form_rujukan_pasien').prop('hidden', true);
        //di hide karna form jenazah belum digunakan
        if (valuedata == 4) {
            $('.field-pasienpulangform-tgl_meninggal').show();
            $('.field-pasienpulangform-tgl_meninggal').addClass("required");
            // if($('#panel-jenazah').hasClass('hidden')){
            //     $('#panel-jenazah').removeClass('hidden');
            // }
            $('#dokterdpjp-div').addClass('hidden')
            $('#kamarruanganjenis-div').addClass('hidden')
            $('#tempattidurtujuan-div').addClass('hidden')

            $('#catatanlain-div').addClass('hidden')

            $('.keterangan-meninggal').show();
        }else if(valuedata == 5) {
            if(!$('#pasienpulangform-tgl_meninggal').hasClass('required')){
                $('#pasienpulangform-tgl_meninggal').removeClass('required');
            }
            $('#pasienpulangform-tgl_meninggal').val('');
            $('.field-pasienpulangform-tgl_meninggal').hide();

            $('.keterangan-meninggal').hide();

            $('#dokterdpjp-div').removeClass('hidden')
            $('#kamarruanganjenis-div').removeClass('hidden')
            $('#tempattidurtujuan-div').removeClass('hidden')
            $('#catatanlain-div').removeClass('hidden')
            if(konfig_keramat_spri){
                $('#dokterspesialis_id-div').removeClass('hidden')
                $('#catatantindakan-div').removeClass('hidden')
            }

            $('#infeksi-div').removeClass('hidden')
            var newState = new Option(nama_dpjp, id_dpjp, true, true);
            $('#pasienpulangform-dpjp_id').append(newState).trigger('change')
            $.uniform.update()
        }
        else if (valuedata == '2') {
            $('#infeksi-div').addClass('hidden')
            $('#form_rujukan_pasien').prop('hidden', false);
            $(".head_kesimpulan_keluar").hide();
            $('#col_kesimpulan_keluar').collapse("hide");
            $(".head_kesimpulan_pulang").hide();
            $(".div_pulang_keluar").hide();
            $('#col_kesimpulan_pulang').collapse("hide");

            if (!$('#pasienpulangform-tgl_meninggal').hasClass('required')) {
                $('#pasienpulangform-tgl_meninggal').removeClass('required');
            }
            $('#pasienpulangform-tgl_meninggal').val('');
            $('.field-pasienpulangform-tgl_meninggal').hide();
            $('.keterangan-meninggal').hide();

            if (!$('#dokterdpjp-div').hasClass('hidden')) {
                $('#dokterdpjp-div').addClass('hidden')
            }
            $('#pasienpulangform-dpjp_id').val(null).trigger('change')

            if (!$('#kamarruanganjenis-div').hasClass('hidden')) {
              $('#kamarruanganjenis-div').addClass('hidden')
            }

            if (!$('#tempattidurtujuan-div').hasClass('hidden')) {
                $('#tempattidurtujuan-div').addClass('hidden')
            }

            if (!$('#catatanlain-div').hasClass('hidden')) {
                $('#catatanlain-div').addClass('hidden')
            }
            if (!$('#panel-jenazah').hasClass('hidden')) {
                $('#panel-jenazah').addClass('hidden');
            }

            var paramsRujuk = 'id=' + pendaftaran_id;
            $.ajax({
                type: 'GET',
                url: '/igd/pemeriksaan-igd/form-pasien-rujuk?' + paramsRujuk,
                success: function (response) {
                    $("#form_rujukan_pasien .tabbable").html(response);
                }
            });
        }
        else {
            if (!$('#dokterdpjp-div').hasClass('hidden')) {
                $('#dokterdpjp-div').addClass('hidden')
            }
            $('#pasienpulangform-dpjp_id').val(null).trigger('change')

            if (!$('#infeksi-div').hasClass('hidden')) {
                $('#infeksi-div').addClass('hidden')
            }

            if (!$('#kamarruanganjenis-div').hasClass('hidden')) {
              $('#kamarruanganjenis-div').addClass('hidden')
            }

            if (!$('#tempattidurtujuan-div').hasClass('hidden')) {
                $('#tempattidurtujuan-div').addClass('hidden')
            }

            if (!$('#catatanlain-div').hasClass('hidden')) {
                $('#catatanlain-div').addClass('hidden')
            }
            if (!$('#panel-jenazah').hasClass('hidden')) {
                $('#panel-jenazah').addClass('hidden');
            }
            if (!$('#pasienpulangform-tgl_meninggal').hasClass('required')) {
                $('#pasienpulangform-tgl_meninggal').removeClass('required');
            }
            $('#pasienpulangform-tgl_meninggal').val('');
            $('.field-pasienpulangform-tgl_meninggal').hide();
            $('.keterangan-meninggal').hide();

        }
    }
});

// mencegah karakter lain selain angka desimal
$(document).on('input', '.doco-decimal', function () {
    match = (/(\d{0,9})[^.]*((?:\.\d{0,9})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
    this.value = match[1] + match[2];
});

$(document).on("click", ".btn-deletes", function (e) {
    $('#list_racikan').find('.child-' + $(this).attr('data-iteration')).remove();
});
$(document).on("change", "#depdrop_reseptur_nr", function (event) {
    let parent = $(this);
    let selected = $(this).find(":selected");
    let harga = selected.data("hargajual");
    let satuankecil = selected.data("satuankecil_nama");
    let satuankecil_id = selected.data("satuankecil_id");
    let obatalkes_id = selected.val();
    let qty_tersedia = parseInt(selected.text().split(" - ").pop());
    let qty_dihapus = 0;
    let temp_qty_tersedia = 0;
    let flag = 0;

    tabel_reseptur_kesimpulan.rows().every(function () {
        var data = this.data();
        var stok_tersedia = !isNaN(qty_tersedia) ? qty_tersedia : 0;
        var qty = parseInt(data[7]);

        if ($(data[11]).val() == parent.val()) {
            qty_tersedia = stok_tersedia - qty;
        }
    });

    $("#satuan_reseptur_nr").val(satuankecil);
    $("#harga_reseptur_nr").val(harga);
    $("#harga_reseptur_nr_view").val(docoHelper.convertToRupiah(harga));
    $("#reseptur_non_racikan_satuankecil_id").val(satuankecil_id);

    $(".stok_tersedia_" + obatalkes_id).each(function (index, object) {
        temp_qty_tersedia = parseInt($(object).text());
        flag = flag + 1;
    });

    if (flag > 0) {
        $("#stok_sisa_nr").val(!isNaN(temp_qty_tersedia) ? temp_qty_tersedia : 0);
    } else {
        $("#stok_sisa_nr").val(!isNaN(qty_tersedia) ? qty_tersedia : 0);
    }
});

$('#depdrop_reseptur_nr').on('depdrop:afterChange', function (event, id, value, jqXHR, textStatus) {
    let ajaxResults = $('#depdrop_reseptur_nr').depdrop('getAjaxResults');
    list_obat = ajaxResults['output'];
});

$(document).ready(function () {

    var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
    $('.pickadate').pickadate({
        format: 'dd mmmm yyyy',
        // onStart: function () {
        //     var date = new Date()
        //     this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
        // },
        disable: [
            { from: [0, 0, 0], to: yesterday }
        ]
    });

    if ($('#kondisipulang_id').val() != '') {
        $("#kondisipulang_id").prop("disabled", false);
    }

    tabel_reseptur_kesimpulan = $('#tabel-reseptur-kesimpulan').DataTable({
        "columnDefs": [
            { className: "hide_column_dt", "targets": [11, 12, 13, 14, 15, 16, 17] }
        ]
    });
    $('#pasienpulangform-dpjp_id').select2InfinityScroll({
        url: '/igd/end-point/doctor-list'
    });
    $('#pasienpulangform-kamarruangan_jenis').select2InfinityScroll({
        url: '/igd/end-point/get-jenis-kamar',
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                    // status_isi: false,
                }
            }
        }
    });
    $('#pasienpulangform-kamarruangan_jenis').on('change', function(){
        let _this = $(this).val();
        $('#pasienpulangform-tempattidurtujuan_id').val('').trigger('change')
        $('#pasienpulangform-tempattidurtujuan_id').trigger({
            type: 'select2:select',
            params: {
                kamarruangan_jenis: _this,
                status_isi: false,
            }
        });
    })
    $('#pasienpulangform-tempattidurtujuan_id').select2InfinityScroll({
        url: '/igd/end-point/get-kamar-tempat-tidur',
        callbackData: (param) => {
            let kamarruangan_jenis = $('#pasienpulangform-kamarruangan_jenis').val() !== null ? $('#pasienpulangform-kamarruangan_jenis').val() : null;
            console.log(param)
            return {
      
                payload: {
                    ...param,
                    status_isi: false,
                    kamarruangan_jenis: kamarruangan_jenis,
                }
            }
        }
    })

    $('#pasienpulangform-dokterspesialis_id').select2InfinityScroll({
        url: '/igd/end-point/get-dokter-spesialis',
        callbackData: (param) => {
            let spesialis = $('#pasienpulangform-dokterspesialis_id').val() !== null ? $('#pasienpulangform-dokterspesialis_id').val() : null;
            return {
                payload: {
                    ...param,
                    instalasi_id: 3,
                    ruangan_id: $('#pasienpulangform-tempattidurtujuan_id :selected').data().data['ruangan_id'] !== undefined ? $('#pasienpulangform-tempattidurtujuan_id :selected').data().data['ruangan_id'] : ''
                }
            }
        }
    })

    $('#pasienpulangform-dokterspesialis_id').on('change', function(){
        let _this = $(this).val();
        $('#pasienpulangform-dokterspesialis_id').trigger({
            type: 'select2:select',
            params: {
                instalasi_id: 3,
            }
        });
    })

    // localStorage.clear();
    $('.keterangan-meninggal').hide();
});

$('#save-kesimpulan').on('click', function () {

    // Validation on pasien rujuk
    if ($('#pasienpulangform-carakeluar_id').val() == '2') {
        // Mandatory Validation
        let _hasError = false;

        if ( $('#rujukanpulangform-rujukan_dituju').val() == '' ) {
            $('#rujukanpulangform-rujukan_dituju').closest('.col-sm-8').append('<p class="has-error" style="color: red">RS Yang Dituju Tidak Boleh Kosong!</p>');
            _hasError = true;
        } else if ( $('#rujukanpulangform-rujukan_dituju').val().length > 200 ) {
            $('#rujukanpulangform-rujukan_dituju').closest('.col-sm-8').append('<p class="has-error" style="color: red">RS Yang Dituju Tidak Boleh Lebih dari 200 karakter!</p>');
            _hasError = true;
        }

        if ( $('#rujukanpulangform-pic_rujukan_dituju').val() == '' ) {
            $('#rujukanpulangform-pic_rujukan_dituju').closest('.col-sm-8').append('<p class="has-error" style="color: red">PIC RS Yang Dituju Tidak Boleh Kosong!</p>');
            _hasError = true;
        } else if ( $('#rujukanpulangform-pic_rujukan_dituju').val().length > 200 ) {
            $('#rujukanpulangform-pic_rujukan_dituju').closest('.col-sm-8').append('<p class="has-error" style="color: red">PIC RS Yang Dituju Tidak Boleh Lebih dari 200 karakter!</p>');
            _hasError = true;
        }

        if ( $('#rujukanpulangform-diagnosa_masuk').val() == '' ) {
            $('#rujukanpulangform-diagnosa_masuk').closest('.col-sm-8').append('<p class="has-error" style="color: red">Diagnosa Masuk RS Tidak Boleh Kosong!</p>');
            _hasError = true;
        } else if ( $('#rujukanpulangform-diagnosa_masuk').val().length > 500 ) {
            $('#rujukanpulangform-diagnosa_masuk').closest('.col-sm-8').append('<p class="has-error" style="color: red">Diagnosa Masuk RS Tidak Boleh Lebih dari 500 karakter!</p>');
            _hasError = true;
        }

        if ( $('#rujukanpulangform-diagnosa_keluar').val() == '' ) {
            $('#rujukanpulangform-diagnosa_keluar').closest('.col-sm-8').append('<p class="has-error" style="color: red">Diagnosa Keluar RS Tidak Boleh Kosong!</p>');
            _hasError = true;
        } else if ( $('#rujukanpulangform-diagnosa_keluar').val().length > 500 ) {
            $('#rujukanpulangform-diagnosa_keluar').closest('.col-sm-8').append('<p class="has-error" style="color: red">Diagnosa Keluar RS Tidak Boleh Lebih dari 500 karakter!</p>');
            _hasError = true;
        }

        if ( _hasError ) {
            docoNotification("warning", "Peringatan", "Harap Cek Kembali Inputan!");
            setTimeout( () => {
                $('p.has-error').remove()
            }, 3000);
            return false
        }
    }

    var form_data = [];
    var form_pasien_pulang = $('div.form_pasien_pulang').find(':input').serializeArray();
    if ($('#checked_kesimpulan_keluar').is(':checked')) {
        var form_kesimpulan_keluar = $('div.form_kesimpulan_keluar').find(':input').serializeArray();
        form_data = $.merge(form_data, form_kesimpulan_keluar);
    }
    if ($('#checked_kesimpulan_pulang').is(':checked')) {
        var form_kesimpulan_pulang = $('div.form_kesimpulan_pulang').find(':input').serializeArray();
        form_data = $.merge(form_data, form_kesimpulan_pulang);
    }
    if ($('#checked_kesimpulan_obat').is(':checked')) {
        var form_obat_pulang = $('div.form_obat_pulang').find(':input').serializeArray();
        form_data = $.merge(form_data, form_obat_pulang);

        var resep_detail = $('#tabel-reseptur-kesimpulan').find(':input').serializeArray();
        form_data = $.merge(form_data, resep_detail);
    }
    if ($('#checked_kesimpulan_jenazah').is(':checked')) {
        var form_kondisi_pasien = $('#kondisipasien').find(':input').serializeArray();
        form_data = $.merge(form_data, form_kondisi_pasien);
    }
    var formData = $.merge(form_data, form_pasien_pulang);

    var _instruksi = $(this).data('instruksi');
    _messages = 'Apakah anda yakin untuk menyimpan data ini ?'
    if (_instruksi) {
        _messages = $(this).data('messages');
    }

    // var dataPasienRujuk = $("#pasien-rujuk-form").serialize();
    var dataPasienRujuk = $("#pasien-rujuk-form").find(':input').serializeArray();

    // if (typeof dataPasienRujuk != 'undefined' && $('#pasienpulangform-carakeluar_id').val() == '2') {
    //     $.ajax({
    //         url: '/igd/pemeriksaan-igd/save-rujukan',
    //         method: "POST",
    //         dataType: "json",
    //         // contentType: "application/json",
    //         data: dataPasienRujuk,
    //         success : function(response) {
    //             PNotify.prototype.options.styling = "bootstrap3";
    //             (new PNotify({
    //                 title: "Berhasil",
    //                 text: "Data berhasil disimpan, apakah Anda ingin melakukan cetak?",
    //                 addclass: "alert alert-success alert-arrow-right alert-styled-right",
    //                 type: "success",
    //                 buttons: {
    //                     closer: false,
    //                     sticker: false
    //                 },
    //                 hide: false,
    //                 confirm: {
    //                     confirm: true
    //                 },
    //                 history: {
    //                     history: false
    //                 }
    //             })).get().on('pnotify.confirm', function() {
    //                 // Print
    //                 window.open("/igd/pemeriksaan-igd/cetak-rujukan?id="+pendaftaran_id);
    //                 setTimeout(function () {
    //                     window.location.href = "/igd/pemeriksaan-igd/periksa?id="+pendaftaran_id
    //                 }, 3000);
    //             }).on('pnotify.cancel', function() {
    //                 setTimeout(function () {
    //                     window.location.href = "/igd/pemeriksaan-igd/periksa?id="+pendaftaran_id
    //                 }, 1000);
    //             });
    //         }
    //     });
    // } else {
    formData = $.merge(form_data, dataPasienRujuk);
    $(this).docoForm('click', {
        data: formData,
        confirmMessage: _messages,
        url: "/igd/pemeriksaan-igd/save-kesimpulan?id=" + pendaftaran_id,
        success: function (result) {
            if (typeof dataPasienRujuk != 'undefined' && $('#pasienpulangform-carakeluar_id').val() == '2') {
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
                })).get().on('pnotify.confirm', function () {
                    // Print
                    window.open("/igd/pemeriksaan-igd/cetak-rujukan?id=" + pendaftaran_id);
                    setTimeout(function () {
                        window.location.href = "/igd/pemeriksaan-igd/periksa?id=" + pendaftaran_id
                    }, 3000);
                }).on('pnotify.cancel', function () {
                    setTimeout(function () {
                        window.location.href = "/igd/pemeriksaan-igd/periksa?id=" + pendaftaran_id
                    }, 1000);
                });
            }
            if (typeof result.response.nomor != 'undefined') {
                (new PNotify({
                    title: "Berhasil",
                    text: "Pelayanan Jenazah dengan nomor antrian " + "<strong>" + result.response.nomor + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
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
                })).get().on('pnotify.confirm', function () {
                    // Print
                    window.open("/jenazah/informasi-pasien-meninggal/cetak-belum-diterima?pendaftaran_id=" + pendaftaran_id);
                }).on('pnotify.cancel', function () {

                });
            }
            $('#content-kesimpulan').docoLoad({
                url: '/igd/pemeriksaan-igd/kesimpulan?id=' + pendaftaran_id,
                dataType: 'html',
                success: function (data) {
                }
            });
        }
    });
    // }

});

$('#cetak-kesimpulan').on('click', function (e) {
    e.preventDefault();
    var url = "/igd/pemeriksaan-igd/cetak-pdf-kesimpulan?id=" + pendaftaran_id;
    window.open(url, '_blank');
});

$('#cetak-keterangan-meninggal').on('click',function(e){
    e.preventDefault();
    var url="/igd/pemeriksaan-igd/cetak-pdf-surat-kematian?id="+pendaftaran_id;
    window.open(url, '_blank');
});

$('#checked_kesimpulan_keluar').on('change',function(){
    if($(this).is(':checked')){
        $('#col_kesimpulan_keluar').collapse("show");
    } else {
        $('#col_kesimpulan_keluar').collapse("hide");
    }
});
$('.head_kesimpulan_keluar').on('click', function () {
    if ($('#checked_kesimpulan_keluar').is(':checked')) {
        $('#checked_kesimpulan_keluar').prop('checked', false);
        $('#col_kesimpulan_keluar').collapse("hide");
    } else {
        $('#checked_kesimpulan_keluar').prop('checked', true);
        $('#col_kesimpulan_keluar').collapse("show");
    }
});

$('#checked_kesimpulan_pulang').on('change', function () {
    if ($(this).is(':checked')) {
        $('#col_kesimpulan_pulang').collapse("show");
    } else {
        $('#col_kesimpulan_pulang').collapse("hide");
    }
});
$('.head_kesimpulan_pulang').on('click', function () {
    if ($('#checked_kesimpulan_pulang').is(':checked')) {
        $('#checked_kesimpulan_pulang').prop('checked', false);
        $('#col_kesimpulan_pulang').collapse("hide");
    } else {
        $('#checked_kesimpulan_pulang').prop('checked', true);
        $('#col_kesimpulan_pulang').collapse("show");
    }
});

$('.head_obat_dibawa_pulang').on('click', function () {
    if($('#col_obat_dibawa_pulang').collapse("show")){
        $('#col_obat_dibawa_pulang').collapse("hide")
    }else{
        $('#col_obat_dibawa_pulang').collapse("show")
    }
});

$('#checked_kesimpulan_obat').on('change', function () {
    if ($(this).is(':checked')) {
        $('#col_kesimpulan_obat').collapse("show");
    } else {
        $('#col_kesimpulan_obat').collapse("hide");
    }
});

$('#checked_kesimpulan_jenazah').on('change', function () {
    if ($(this).is(':checked')) {
        $('#col_kesimpulan_jenazah').collapse("show");
    } else {
        $('#col_kesimpulan_jenazah').collapse("hide");
    }
});

$('.head_kesimpulan_obat').on('click', function () {
    if ($('#checked_kesimpulan_obat').is(':checked')) {
        $('#checked_kesimpulan_obat').prop('checked', false);
        $('#col_kesimpulan_obat').collapse("hide");
    } else {
        $('#checked_kesimpulan_obat').prop('checked', true);
        $('#col_kesimpulan_obat').collapse("show");
    }
});

$('.head_kesimpulan_jenazah').on('click', function () {
    if ($('#checked_kesimpulan_jenazah').is(':checked')) {
        $('#checked_kesimpulan_jenazah').prop('checked', false);
        $('#col_kesimpulan_jenazah').collapse("hide");
    } else {
        $('#checked_kesimpulan_jenazah').prop('checked', true);
        $('#col_kesimpulan_jenazah').collapse("show");
    }
});

$(document).on('change', 'select.racikan_append', function () {
    let parent = $(this);
    let selected = $(this).find(":selected");
    let harga = selected.data("hargajual");
    let satuankecil = selected.data("satuankecil_nama");
    let satuankecil_id = selected.data("satuankecil_id");
    let obatalkes_id = selected.val();
    let split_text_name = selected.text().split(" - ");
    let obatalkes_nama = '-';
    if (Object.keys(split_text_name)[0]) {
        obatalkes_nama = split_text_name[Object.keys(split_text_name)[0]];
    }
    let qty_tersedia = parseInt(selected.text().split(" - ").pop());
    let qty_dihapus = 0;
    let temp_qty_tersedia = 0;
    let flag = 0;
    var elmt_parent_input = '';

    tabel_reseptur_kesimpulan.rows().every(function () {
        var data = this.data();
        var stok_tersedia = !isNaN(qty_tersedia) ? qty_tersedia : 0;
        var qty = parseInt(data[7]);

        if ($(data[11]).val() == parent.val()) {
            qty_tersedia = stok_tersedia - qty;
        }
    });

    if ($(this).parent().parent().parent().parent().parent().hasClass('group_input_racikan')) {
        elmt_parent_input = $(this).parent().parent().parent().parent().parent();
        if (satuankecil !== undefined) {
            elmt_parent_input.find('input.field-satuankecil').val(satuankecil);
        }
        if (satuankecil_id !== undefined) {
            elmt_parent_input.find('input.field-satuankecil_id').val(satuankecil_id);
        }
        if (!isNaN(harga)) {
            elmt_parent_input.find('input.field-harga').val(harga);
            elmt_parent_input.find('input.field-harga_view').val(docoHelper.convertToRupiah(harga));
        }
        if (!isNaN(qty_tersedia)) {
            elmt_parent_input.find('input.field-stok-sisa').val(qty_tersedia);
        }
        if (obatalkes_nama !== '-') {
            elmt_parent_input.find('input.field-obatalkesnama').val(obatalkes_nama);
        }
    }

    // $(".stok_tersedia_"+obatalkes_id).each(function(index, object) {
    //     temp_qty_tersedia = parseInt($(object).text());
    //     flag = flag + 1;
    // });

    // if (flag > 0) {
    //     $("#stok_sisa_nr").val(!isNaN(temp_qty_tersedia) ? temp_qty_tersedia : 0);
    // } else {
    //     $("#stok_sisa_nr").val(!isNaN(qty_tersedia) ? qty_tersedia : 0);
    // }
});

function submitNonracikan(element) {
    var form_non_racikan = $(element).closest('div.panel-body').find('div.form_non_racikan').find(':input').serializeArray();
    var is_error = false;
    $('div.help-block.error').remove();
    $('span.help-block.error').remove();
    var obat = '';
    if (form_non_racikan.find(x => x.name === 'ResepturNrDetailForm[obatalkes_id]') == undefined) {
        var field_obatalkes = $('[name="ResepturNrDetailForm[obatalkes_id]"]');
        var _group_field_obatalkes = field_obatalkes.closest('div').find('.select2-container');
        field_obatalkes.parent('div').addClass('has-error');
        _group_field_obatalkes.after('<span class="help-block error">' + '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
            + 'Obat Alkes Harus Dipilih' + '</span>');
        is_error = true;
    } else {
        obat = form_non_racikan.find(x => x.name === 'ResepturNrDetailForm[obatalkes_id]').value;
        if (obat == '') {
            var field_obatalkes = $('[name="ResepturNrDetailForm[obatalkes_id]"]');
            var _group_field_obatalkes = field_obatalkes.closest('div').find('.select2-container');
            field_obatalkes.parent('div').addClass('has-error');
            _group_field_obatalkes.after('<span class="help-block error">' + '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
                + 'Obat Alkes Harus Dipilih' + '</span>');
            is_error = true;
        }
    }
    var stok_tersedia = form_non_racikan.find(x => x.name === 'stok_sisa_nr').value;
    var qty = form_non_racikan.find(x => x.name === 'ResepturNrDetailForm[qty_reseptur]').value;
    var signa = form_non_racikan.find(x => x.name === 'ResepturNrDetailForm[signa_reseptur]').value;

    if (stok_tersedia == '') {
        var field_stok_tersedia = $('[name="stok_sisa_nr"]');
        var _group_field_stok_tersedia = field_stok_tersedia.closest('div.input-group');
        field_stok_tersedia.parent('div').addClass('has-error');
        field_stok_tersedia.after('<span class="help-block error">' + '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
            + 'Obat Alkes Harus Dipilih' + '</span>');
        is_error = true;
    }
    if (signa == '') {
        var field_signa = $('[name="ResepturNrDetailForm[signa_reseptur]"]');
        var _group_field_signa = field_signa.closest('div').find('.select2-container');
        field_signa.parent('div').addClass('has-error');
        _group_field_signa.after('<span class="help-block error">' + '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
            + 'Signa Harus Dipilih' + '</span>');
        is_error = true;
    }
    if (qty == '' || qty == 0) {
        var field_qty = $('[name="ResepturNrDetailForm[qty_reseptur]"]');
        var _group_field_qty = field_qty.closest('div.input-group');
        field_qty.parent('div').addClass('has-error');
        field_qty.after('<span class="help-block error">' + '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp'
            + 'Jumlah Harus Diisi' + '</span>');
        is_error = true;
    }
    if (is_error == true) {
        return;
    }
    var nama_obatalkes = $('[name="ResepturNrDetailForm[obatalkes_id]"] option:selected').text();
    var id_obatalkes = obat;
    var jumlah_qty = $('[name="ResepturNrDetailForm[qty_reseptur]"]').val();
    var nama_satuankecil = $('[name="ResepturNrDetailForm[satuankecil_nama]"]').val();
    var id_satuankecil = $('#reseptur_non_racikan_satuankecil_id').val();
    var hargasatuan = $('[name="ResepturNrDetailForm[hargasatuan_reseptur]"]').val();
    var hargasatuanView = docoHelper.convertToRupiah(parseFloat(hargasatuan), 2);
    var id_signa = $('[name="ResepturNrDetailForm[signa_reseptur]"]').val();
    var nama_signa = $('[name="ResepturNrDetailForm[signa_reseptur]"] option:selected').text();
    var stok_tersedia = $('[name="stok_sisa_nr"]').val();
    var jumlah_harga = jumlah_qty * parseFloat(hargasatuan);
    var jumlah_hargaView = docoHelper.convertToRupiah(parseFloat(jumlah_harga), 2);
    var page_info = tabel_reseptur_kesimpulan.page.info();
    last_page_number = page_info.end;
    rowNumber = last_page_number + 1;

    var localstorage_nonracikan = localStorage.getItem(key_nonracikan);
    if (localstorage_nonracikan != null) {
        localstorage_nonracikan = JSON.parse(localstorage_nonracikan);
        for (var i = 0; i < localstorage_nonracikan.length; ++i) {
            var temp = id_obatalkes;

            if (localstorage_nonracikan[i] == temp) {
                docoNotification("error", "Peringatan!", "Tidak bisa menambahkan obat yang sama, silakan ubah qty pada tabel dibawah!");
                return;
            }
        }
    } else {
        var localstorage_nonracikan = [];
    }

    stok_tersedia = parseInt(stok_tersedia) - parseInt(jumlah_qty);
    $("#stok_sisa_nr").val(stok_tersedia);

    var row_node = tabel_reseptur_kesimpulan.row.add([
        rowNumber,
        'Non Racikan',
        '-',
        nama_obatalkes,
        nama_satuankecil,
        nama_signa,
        stok_tersedia,
        jumlah_qty,
        hargasatuanView,
        jumlah_hargaView,
        '<button type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="fa fa-trash"></i></button>',
        '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_obatalkes_id]" value="' + id_obatalkes + '" />',
        '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_signa_id]"  value="' + id_signa + '" />',
        '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_hargasatuan]"  value="' + hargasatuan + '" />',
        '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_qty_reseptur]"  value="' + jumlah_qty + '" />',
        '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_racikan_id]"  value="2" />',
        '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_satuankecil_id]"  value="' + id_satuankecil + '" />',
        '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_rke]"  value="" />'
    ]).draw(false).node();
    $(row_node).find("td").eq(6).addClass("stok_tersedia_" + id_obatalkes);
    $(row_node).find("td").eq(8).addClass("text-right");
    $(row_node).find("td").eq(9).addClass("text-right");

    $('[name="ResepturNrDetailForm[obatalkes_id]"]').val('').trigger('change');
    $('[name="ResepturNrDetailForm[signa_reseptur]"]').val('').trigger('change');
    $('[name="ResepturNrDetailForm[qty_reseptur]"]').val('');

    localstorage_nonracikan.push(id_obatalkes);
    localStorage.setItem(key_nonracikan, JSON.stringify(localstorage_nonracikan));

    tabel_reseptur_kesimpulan.rows().every(function () {
        var data = this.data();

        if ($(data[11]).val() == id_obatalkes) {
            data[6] = stok_tersedia;
            this.data(data);
        }
    });

    tabel_reseptur_kesimpulan.draw();

    // var td = $('.stok_tersedia_'+id_obatalkes);
    // tabel_reseptur_kesimpulan.cell(td).data(stok_tersedia).draw();
}

$('#tabel-reseptur-kesimpulan tbody').on('click', '.btn-delete-row', function () {
    var data = tabel_reseptur_kesimpulan.row($(this).parents("tr")).data();

    var id_obatalkes;
    if (typeof data[11] != "undefined" && data[11] != null) {
        id_obatalkes = $(data[11]).val();
    }

    var id_signa;
    if (typeof data[12] != "undefined" && data[12] != null) {
        id_signa = $(data[12]).val();
    }

    var stok_tersedia = 0;
    if (typeof data[6] != "undefined" && data[6] != null) {
        stok_tersedia = parseInt(data[6]);
    }

    var qty = 0;
    if (typeof data[7] != "undefined" && data[7] != null) {
        qty = parseInt(data[7]);
    }

    stok_tersedia = stok_tersedia + qty;

    if (data[1] == "Non Racikan") {
        var localstorage = localStorage.getItem(key_nonracikan);
        var key = key_nonracikan;
    } else if (data[1] == "Racikan") {
        var localstorage = localStorage.getItem(key_racikan);
        var key = key_racikan;
    } else {
        var localstorage = null;
        var key = null;
    }

    if (localstorage != null) {
        var remove = id_obatalkes;
        localstorage = JSON.parse(localstorage);

        localstorage = jQuery.grep(localstorage, function (value) {
            return value != remove;
        });

        if (key != null) {
            localStorage.setItem(key, JSON.stringify(localstorage));
        }
    }

    tabel_reseptur_kesimpulan
        .row($(this).parents('tr'))
        .remove()
        .draw();

    tabel_reseptur_kesimpulan.rows().every(function () {
        var data = this.data();

        if ($(data[11]).val() == id_obatalkes) {
            data[6] = stok_tersedia;
            this.data(data);
        }
    });

    tabel_reseptur_kesimpulan.draw();

    // var td = $('.stok_tersedia_'+id_obatalkes);
    // tabel_reseptur_kesimpulan.cell(td).data(stok_tersedia).draw();
});

var form = false;
function submitRacikan(element) {
    var form_racikan = $(element).parent().parent().closest('div.panel-body').find('div.group_input_racikan').find(':input').serializeArray();
    // console
    var rke = $('[name="ResepturDetailForm[rke]"]').val();
    var id_signa = $('[name="ResepturDetailForm[signa_reseptur]"] option:selected').val();
    var nama_signa = $('[name="ResepturDetailForm[signa_reseptur]"] option:selected').text();
    var cek = [];
    form_racikan[form_racikan.length] = { name: "rke", value: rke };

    $(".cek-racikan").each(function () {
        if ($(this).val() != null) {
            cek.push($(this).val());

            if ($(this).val() == "") {
                $(this).parent().parent().closest("div").addClass("has-error");
            }
            else {
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
        else {
            cek.push("");

            if ($(this).val() == "" || $(this).val() == null) {
                $(this).parent().parent().closest("div").addClass("has-error");
            }
            else {
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
    });

    if (jQuery.inArray("", cek) !== -1) {
        docoNotification('error', "Tidak bisa tambah racikan", "Ada field yang belum diisi");
        return;
    }
    $.ajax({
        url: '/igd/pemeriksaan-igd/kesimpulan-validasi-racikan',
        type: 'post',
        data: form_racikan,
        beforeSend: function () {
            var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay"></div>';
            var dialogTemplate = '<div id="confirm-dialog">';
            dialogTemplate += '<div class="dialog-content">';
            dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p cla' +
                'ss=\"confirm-message-text\"></p>';
            dialogTemplate += '</div>';
            dialogTemplate += '</div>';

            $('body').append(overlayTemplate);
            $('body').append(dialogTemplate);
            $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw" style="margin:18px 0 19px 0;"></i>&nbs' +
                'p;Sedang memproses . . .');
        },
        success: function (res) {
            var page_info = tabel_reseptur_kesimpulan.page.info();
            var last_page_number = page_info.end;
            var rowNumber = last_page_number + 1;
            list_racikan = res.response.data;
            $.each(list_racikan, function (key, racikan) {
                var nama_obatalkes = racikan.obatalkes_nama;
                var nama_satuankecil = racikan.satuankecil_nama;
                var stok_tersedia = racikan.stok_tersedia;
                var jumlah_qty = racikan.qty_reseptur;
                var hargasatuan = racikan.hargasatuan_reseptur;
                var hargasatuanView = docoHelper.convertToRupiah(parseFloat(hargasatuan), 2);
                var jumlah_harga = parseInt(jumlah_qty) * parseFloat(hargasatuan);
                var jumlah_hargaView = docoHelper.convertToRupiah(parseFloat(jumlah_harga), 2);
                var id_obatalkes = racikan.obatalkes_id;
                var id_satuankecil = racikan.satuankecil_id;

                stok_tersedia = parseInt(stok_tersedia) - parseInt(jumlah_qty);

                var localstorage_racikan = localStorage.getItem(key_racikan);
                if (localstorage_racikan != null) {
                    localstorage_racikan = JSON.parse(localstorage_racikan);
                    for (var i = 0; i < localstorage_racikan.length; ++i) {
                        var temp = id_obatalkes;

                        if (localstorage_racikan[i] == temp) {
                            docoNotification("error", "Peringatan!", "Tidak bisa menambahkan obat yang sama, silakan ubah qty pada tabel dibawah!");
                            return;
                        }
                    }
                } else {
                    var localstorage_racikan = [];
                }

                var row_node = tabel_reseptur_kesimpulan.row.add([
                    rowNumber,
                    'Racikan',
                    racikan.rke,
                    nama_obatalkes,
                    nama_satuankecil,
                    nama_signa,
                    stok_tersedia,
                    jumlah_qty,
                    hargasatuanView,
                    jumlah_hargaView,
                    '<button type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="fa fa-trash"></i></button>',
                    '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_obatalkes_id]" value="' + id_obatalkes + '" />',
                    '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_signa_id]"  value="' + id_signa + '" />',
                    '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_hargasatuan]"  value="' + hargasatuan + '" />',
                    '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_qty_reseptur]"  value="' + jumlah_qty + '" />',
                    '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_racikan_id]"  value="1" />',
                    '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_satuankecil_id]"  value="' + id_satuankecil + '" />',
                    '<input type="text" name="ResepturDetail[' + rowNumber + '][dt_rke]"  value="' + racikan.rke + '" />'
                ]).draw(false).node();
                $(row_node).find("td").eq(6).addClass("stok_tersedia_" + id_obatalkes);
                $(row_node).find("td").eq(8).addClass("text-right");
                $(row_node).find("td").eq(9).addClass("text-right");

                localstorage_racikan.push(id_obatalkes);
                localStorage.setItem(key_racikan, JSON.stringify(localstorage_racikan));

                tabel_reseptur_kesimpulan.rows().every(function () {
                    var data = this.data();

                    if ($(data[11]).val() == id_obatalkes) {
                        data[6] = stok_tersedia;
                        this.data(data);
                    }
                });

                tabel_reseptur_kesimpulan.draw();

                // var td = $('.stok_tersedia_'+id_obatalkes);
                // tabel_reseptur_kesimpulan.cell(td).data(stok_tersedia).draw();

                rowNumber++;
            });
            // getListObat();
            // resetForm($('#form-nonracikan'))
            // form = false;


        },
        error: function () {

        },
        complete: function () {
            hideQuestionDialog();
            $('body')
                .find('.confirm-dialog-overlay')
                .remove();
            docoHelper.listen = false;
            form = false;

            $("#list_racikan").find(".child").remove();
            $("#resepturdetailform-0-obatalkes_id").val("").trigger("change.select2");
            $("#resepturdetailform-signa_reseptur").val("").trigger("change.select2");
            $("#resepturdetailform-rke").val("");
            $("#racikan_append_satuan_0").val("");
            $("#racikan_append_harga_0").val("");
            $("#qty_reseptur-0").val("");
            $("#stok_sisa_r_0").val("");
        }
    });
    return;
    // var id_obatalkes;
    // var hargasatuan
    // var jumlah_qty
    // var id_satuankecil
    // var cek = $(document).find('div#last_racikan').find('div.group_input_racikan');
}

function appendRacikan(element) {
    var cek = [];

    $(".cek-racikan").each(function () {
        if ($(this).val() != null) {
            cek.push($(this).val());

            if ($(this).val() == "") {
                $(this).parent().parent().closest("div").addClass("has-error");
            }
            else {
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
        else {
            cek.push("");

            if ($(this).val() == "" || $(this).val() == null) {
                $(this).parent().parent().closest("div").addClass("has-error");
            }
            else {
                $(this).parent().parent().closest("div").removeClass("has-error");
            }
        }
    });

    if (jQuery.inArray("", cek) !== -1) {
        docoNotification('error', "Tidak bisa tambah racikan", "Ada field yang belum diisi");
    } else {
        iteration++;
        let template =
            '<div class="col-md-12 group_input_racikan b child child-' + iteration + '"><hr>' +
            '<div class="row">' +
            '<div class="col-md-6">' +
            '<div class="form-group field-resepturdetailform-obatalkes_id required">' +
            '<label class="text-right control-label col-sm-4" for="resepturdetailform-obatalkes_id">Nama Obat</label>' +
            '<div class="col-sm-8">' +
            '<select id="resepturdetailform-obatalkes_id" data-iteration="' + iteration + '" class="select2 form-control racikan_append ' + racikan_append + iteration + ' cek-racikan" name="ResepturDetailForm[' + iteration + '][obatalkes_id]"></select>' +
            '<div class="help-block"></div>' +
            '</div>' +
            '</div>' +
            '<input type="hidden" id="' + racikan_append_obatalkes_nama + iteration + '" class="' + racikan_append_obatalkes_nama + iteration + ' field-obatalkesnama" name="ResepturDetailForm[' + iteration + '][obatalkes_nama]" readonly="readonly">' +
            '</div>' +
            '<div class="col-md-6">' +
            '<div class="form-group field-resepturdetailform-qty_reseptur required">' +
            '<label class="text-right control-label col-sm-4" for="resepturdetailform-qty_reseptur">Jumlah</label>' +
            '<div class="col-sm-8">' +
            '<input type="text" id="' + racikan_append_qty + iteration + '" class="form-control input-sm qty-reseptur cek-racikan doco-decimal" maxlength="8" name="ResepturDetailForm[' + iteration + '][qty_reseptur]" placeholder="Jumlah">' +
            '<div class="help-block"></div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-md-6">' +
            '<div class="form-group field-resepturdetailform-satuankecil_id">' +
            '<label class="text-right control-label col-sm-4" for="resepturdetailform-satuankecil_id">Satuan kecil</label>' +
            '<div class="col-sm-8">' +
            '<input type="text" id="' + racikan_append_satuan + iteration + '" class="form-control field-satuankecil input-sm" name="ResepturDetailForm[' + iteration + '][satuankecil_nama]" readonly="readonly">' +
            '<div class="help-block"></div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<div class="form-group">' +
            '<label class="text-right control-label col-sm-4">Stok Tersedia</label>' +
            '<div class="col-sm-8">' +
            '<input type="text" id="' + racikan_append_stok_tersedia + iteration + '" class="form-control field-stok-sisa input-sm" name="ResepturDetailForm[' + iteration + '][stok_tersedia]" readonly="readonly">' +
            '<div class="help-block"></div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-md-6">' +
            '<div class="form-group field-resepturdetailform-hargasatuan_reseptur">' +
            '<label class="text-right control-label col-sm-4" for="resepturdetailform-hargasatuan_reseptur">Harga Satuan</label>' +
            '<div class="col-sm-8">' +
            '<input type="text" id="' + racikan_append_harga + iteration + '" class="form-control field-harga input-sm ' + racikan_append_harga + iteration + '" name="ResepturDetailForm[' + iteration + '][hargasatuan_reseptur]" readonly="readonly">' +
            '<div class="help-block"></div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-1">' +
            '<button type="button" class="btn btn-danger btn-sm btn-deletes" data-iteration="' + iteration + '"><i class="fa fa-trash"></i></button>' +
            '</div>' +
            '<input type="hidden" id="' + racikan_append_satuan_id + iteration + '" class="field-satuankecil_id ' + racikan_append_satuan_id + iteration + '" name="ResepturDetailForm[' + iteration + '][satuankecil_id]" readonly="readonly">' +
            '</div>' +
            '</div>';
        $("#list_racikan").append(template);

        if (list_obat.length !== 0) {
            $('.' + racikan_append + iteration).append($("<option></option>")
                .attr("value", "")
                .attr("data-hargajual", 0)
                .attr("data-satuankecil_nama", "")
                .attr("data-satuankecil_id", "")
                .prop("disabled", false)
                .text("-- Pilih Nama Obat --"));

            $.each(list_obat, function (key, value) {
                $('.' + racikan_append + iteration).append($("<option></option>")
                    .attr("value", value.id)
                    .attr("data-hargajual", value.options.hargajual)
                    .attr("data-satuankecil_nama", value.options.satuankecil_nama)
                    .attr("data-satuankecil_id", value.options.satuankecil_id)
                    .prop("disabled", value.options.disabled)
                    .text(value.name));
            });

            $('.' + racikan_append + iteration).select2();
        }
    }
}

$(document).on("change", "#qty_nonracikan_id", function (event) {
    event.preventDefault();

    let stok_tersedia = parseInt($("#stok_sisa_nr").val());
    let qty = parseInt($(this).val());

    if (qty > stok_tersedia) {
        docoNotification("error", "Tidak bisa tambah jumlah obat!", "Jumlah obat tidak boleh lebih dari stok tersedia!");

        $(this).val(0);
    }
});

$(document).on("change", ".qty-reseptur", function (event) {
    event.preventDefault();

    let stok_tersedia = 0;
    let qty = parseInt($(this).val());
    let attr = $(this).attr("id");
    let count = attr.split("-");
    count = count[1];
    stok_tersedia = parseInt($("#" + racikan_append_stok_tersedia + count).val());

    if (qty > stok_tersedia) {
        docoNotification("error", "Tidak bisa tambah jumlah obat!", "Jumlah obat tidak boleh lebih dari stok tersedia!");

        $(this).val(0);
    }
});

$("select").on("select2:close", function () {
    $(this).focus();
});
