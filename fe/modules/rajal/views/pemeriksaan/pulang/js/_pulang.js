/*
* @Author: Rizqi Fitrianto
* @Date:   2019-02-26 11:06:40
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2019-03-14 14:59:43
*/
$(document).ready(function () {
    console.log($('#form-pemulanganpasien').attr('action') + '&trace=1',)
    // var hari_ini = new Date();
    // var tahun = hari_ini.getFullYear()
    // var bulan = hari_ini.getMonth() + 1;
    // var hari = hari_ini.getDate();
    // var jam = checkTime(hari_ini.getHours());
    // var menit = checkTime(hari_ini.getMinutes());
    // var detik = checkTime(hari_ini.getSeconds());
    // if (hari < 10) {
    //     hari = '0' + hari;
    // }
    // if (bulan < 10) {
    //     bulan = '0' + bulan;
    // }
    var infeksi = ''
    $('#pasienpulangform-tglpasienpulang').val(defaultDate)
    setTimeout(function () {
        $('.picker').remove();
    }, 300);
    $('#jenazahform-hubungan_keluarga').select2();
    $('#btn-save-pulang').on('click', function (e) {
        e.preventDefault();
        if ($('#infeksi-inf').is(':checked')) {
            infeksi = 'inf'
        } else if ($('#infeksi-non_inf').is(':checked')) {
            infeksi = 'non_inf'
        }
        var dataPost = $("#form-pemulanganpasien").serializeArray().concat([
            { name: "PasienPulangForm[tempattidurtujuan_id]", value: $('#pasienpulangform-tempattidurtujuan_id').val() },
            { name: "PasienPulangForm[infeksi]", value: infeksi },
            { name: "PasienPulangForm[catatan_lain]", value: $('#pasienpulangform-catatan_lain').val() },
            { name: "PasienPulangForm[catatan_tindakan]", value: $('#pasienpulangform-catatan_tindakan').val() },
        ])
        var isOrderJenazah = false;
        if ($('.persetujuan-pelayanan').is(':checked')) {
            isOrderJenazah = true;
            var frm_pelayanan_jenazah = $('#highlighted-justified-tab1 :input').serializeArray();
            $.merge(dataPost, frm_pelayanan_jenazah);
        }

        _messages = $(this).data('messages');

        // var dataPasienRujuk = $("#pasien-rujuk-form").serialize();
        var dataPasienRujuk = $("#pasien-rujuk-form").find(':input').serializeArray();
        dataPost = $.merge(dataPost, dataPasienRujuk);
        if($('#pasienpulangform-is_prb').is(":checked")) {
            dataPost.push({ name: 'reseptur_prb', value: JSON.stringify(resepturData) });
            let pegawai = $('#rujukanpulangform-pegawai_kode_bpjs').select2('data');
            dataPost.push({ name: 'RujukanPulangForm[pegawai_nama]', value: pegawai[0].text});
        }
        // if (typeof dataPasienRujuk != 'undefined' && $('#pasienpulangform-carakeluar_id').val() == '2') {
        //     $.ajax({
        //         url: '/rajal/pemeriksaan/save-rujukan',
        //         method: "POST",
        //         dataType: "json",
        //         // contentType: "application/json",
        //         data: dataPasienRujuk,
        //         success : function(response) {
        //             PNotify.prototype.options.styling = "bootstrap3";
        //             (new PNotify({
        //                 title: "Berhasil",
        //                 text: "Data Rujukan berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
        //                 window.open("/rajal/pemeriksaan/cetak-rujukan?id="+pendaftaranId);
        //                 setTimeout(function () {
        //                     window.location.href = "/rajal/pemeriksaan/periksa?id="+pendaftaranId
        //                 }, 3000);
        //             }).on('pnotify.cancel', function() {
        //                 setTimeout(function () {
        //                     window.location.href = "/rajal/pemeriksaan/periksa?id="+pendaftaranId
        //                 }, 1000);
        //             });
        //         }
        //     });
        // } else {
        $(this).docoForm("click", {
            url: $('#form-pemulanganpasien').attr('action') + '&trace=1',
            data: dataPost,
            confirmMessage: _messages,
            method: 'post',
            success: function (response) {
                $('#btn-save-pulang').attr('disabled', true)
                if (typeof dataPasienRujuk != 'undefined' && $('#pasienpulangform-carakeluar_id').val() == '2') {
                    if($('#pasienpulangform-is_prb').is(":checked")) {
                        setTimeout(function () {
                            window.location.href = "/rajal/pemeriksaan/periksa?id=" + pendaftaranId
                        }, 1000);
                } else {
                        PNotify.prototype.options.styling = "bootstrap3";
                        (new PNotify({
                            title: "Berhasil",
                            text: "Data Rujukan berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                            window.open("/rajal/pemeriksaan/cetak-rujukan?id=" + pendaftaranId);
                            setTimeout(function () {
                                window.location.href = "/rajal/pemeriksaan/periksa?id=" + pendaftaranId
                            }, 3000);
                        }).on('pnotify.cancel', function () {
                            setTimeout(function () {
                                window.location.href = "/rajal/pemeriksaan/periksa?id=" + pendaftaranId
                            }, 1000);
                        });                        
                    }
                } else if (isOrderJenazah) {
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
                        window.open("/jenazah/informasi-pasien-meninggal/cetak-belum-diterima?pendaftaran_id=" + pendaftaranId);
                        setTimeout(function () {
                            window.location.href = "/rajal/pemeriksaan/periksa?id=" + pendaftaranId
                        }, 3000);
                    }).on('pnotify.cancel', function () {
                        setTimeout(function () {
                            window.location.href = "/rajal/pemeriksaan/periksa?id=" + pendaftaranId
                        }, 1000);
                    });
                } else if ($('#pasienpulangform-carakeluar_id').val() == 5) {
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
                        window.open("/igd/pemeriksaan-igd/cetak-spri?id=" + pendaftaranId + "&type=rj",);
                        setTimeout(function () {
                            window.location.href = "/rajal"
                        }, 3000);
                    }).on('pnotify.cancel', function () {
                        setTimeout(function () {
                            window.location.href = "/rajal"
                        }, 1000);
                    });
                }
                else {
                    setTimeout(function () {
                        window.location.href = "/rajal"
                    }, 1000);
                }
            }
        });
        // }
    })
    // var dataForm = function(){
    //     var dataPost = $("#form-pemulanganpasien").serializeArray();
    //     var isOrderJenazah = false;
    //     if($('.persetujuan-pelayanan').is(':checked')){
    //         isOrderJenazah = true;
    //         var frm_pelayanan_jenazah = $('#highlighted-justified-tab1 :input').serializeArray();
    //         $.merge(dataPost,frm_pelayanan_jenazah);
    //     }
    //     console.log(dataPost);
    //     return dataPost;
    // }
    // $("#form-pemulanganpasien").docoForm('submit', {
    //     data: dataForm(),
    //     success: function(response){
    //         console.log(response);
    //     }
    // });
    $('#pasienpulangform-carakeluar_id').on('change', function () {
        var carakeluar = $(this).val();
        $('#form_rujukan_pasien').prop('hidden', true);
        $('.modal-dialog').css('width', '65%');

        var hasFreetext = $(this).find(':selected').data('freetext');
        if (carakeluar == 4) {
            if ($('.form-jenazah').hasClass('hidden')) {
                $('.form-jenazah').removeClass('hidden');
            }
            if (!$('.form-rujuk').hasClass('hidden') || !$('.form-catatan').hasClass('hidden')) {
                $('.form-rujuk').addClass('hidden');
                $('.form-catatan').addClass('hidden');
            }
            $('#pasienpulangform-tempattidurtujuan_id').val(null).trigger('change')
        } else if (carakeluar == 5) {
            $('.modal-dialog').css('width', '80%');
            if ($('.form-rujuk').hasClass('hidden') || $('.form-catatan').hasClass('hidden') || $('.form-infeksi').hasClass('hidden')) {
                $('.form-rujuk').removeClass('hidden');
                $('.form-catatan').removeClass('hidden');
                $('.form-infeksi').removeClass('hidden');
            }
            if (!$('.form-jenazah').hasClass('hidden')) {
                $('.form-jenazah').addClass('hidden');
            }
            if(konfig_keramat_spri){
                $('.form-catatan-tindakan').removeClass('hidden');
            }

            $('#pasienpulangform-tgl_meninggal').val('');
            $('.persetujuan-pelayanan').prop('checked', false).trigger('change');
        } else if (carakeluar == 2) {
            $('.form-catatan').addClass('hidden');
            $('.form-infeksi').addClass('hidden');
            if (!$('.form-jenazah').hasClass('hidden')) {
                $('.form-jenazah').addClass('hidden');
            }
            if (!$('.form-rujuk').hasClass('hidden')) {
                $('.form-rujuk').addClass('hidden');
            }
            $('#form_rujukan_pasien').prop('hidden', false);
            $('.modal-dialog').css('width', '95%');
            var paramsRujuk = 'id=' + pendaftaran_id;
            showLoader();
            $.ajax({
                type: 'GET',
                url: '/rajal/pemeriksaan/form-pasien-rujuk?' + paramsRujuk,
                success: function (response) {
                    $("#form_rujukan_pasien .tabbable-rujukan").html(response);
                },
                complete: function (response) {
                    hideLoader();
                }
            });
        } else if (carakeluar == 6) {
            $('.form-catatan').addClass('hidden');
            $('.form-infeksi').addClass('hidden');
            $('#pasienpulangform-tempattidurtujuan_id').val(null).trigger('change')
        }
        else if (hasFreetext) {
            if ($('.form-catatan').hasClass('hidden')) {
                $('.form-catatan').removeClass('hidden');
            }
            if (!$('.form-jenazah').hasClass('hidden') || !$('.form-rujuk').hasClass('hidden')) {
                $('.form-jenazah').addClass('hidden');
                $('.form-rujuk').addClass('hidden');
            }
            $('#pasienpulangform-tgl_meninggal').val('');
            $('.persetujuan-pelayanan').prop('checked', false).trigger('change');
            $('#pasienpulangform-tempattidurtujuan_id').val(null).trigger('change');
        } else {
            if (!$('.form-rujuk').hasClass('hidden') || !$('.form-catatan').hasClass('hidden') || !$('.form-jenazah').hasClass('hidden')) {
                $('.form-rujuk').addClass('hidden');
                $('.form-catatan').addClass('hidden');
                $('.form-infeksi').addClass('hidden');
                $('.form-jenazah').addClass('hidden');
            }
            $('#pasienpulangform-tgl_meninggal, #pasienpulangform-catatan_lain').val('');
            $('.persetujuan-pelayanan').prop('checked', false).trigger('change');
            $('#pasienpulangform-tempattidurtujuan_id').val(null).trigger('change')
        }
        if(carakeluar == 2 && $('#pasienpulangform-is_prb').data('bpjs-rujukan') == 1) {
            $('#pasienpulangform-is_prb').prop('disabled', false);
        } else {
            $('#pasienpulangform-is_prb').prop('disabled', true);
            $('#pasienpulangform-is_prb').prop('checked', false).trigger('change');
        }
    })

    $('#pasienpulangform-kamarruangan_jenis').select2InfinityScroll({
        url: '/rajal/end-point/get-jenis-kamar',
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
        url: '/rajal/end-point/get-kamar-tempat-tidur',
        callbackData: (param) => {
            let kamarruangan_jenis = $('#pasienpulangform-kamarruangan_jenis').val() !== null ? $('#pasienpulangform-kamarruangan_jenis').val() : null;
            return {
                payload: {
                    ...param,
                    status_isi: false,
                    kamar_icu : false,
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
                    ruangan_id: $('#pasienpulangform-tempattidurtujuan_id :selected').data().data['ruangan_id'] !== undefined ? $('#pasienpulangform-tempattidurtujuan_id :selected').data().data['ruangan_id'] : '',
                    instalasi_id: 3,
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
    $('.persetujuan-pelayanan').on('change', function () {
        if ($('.persetujuan-pelayanan').is(':checked')) {
            if ($('#display_hidden_jenazah').hasClass('hidden')) {
                $('#display_hidden_jenazah').removeClass('hidden');
                table_jenazah_tindakan = $("#tabel-tindakan").docoTabel({
                    destroy: true,
                    scrollX: false,
                    filter: false,
                    sorting: [[1, "asc"]],
                    processing: true,
                    serverSide: true,
                    paging: false,
                    ajax: "get-cache-jenazah?cachetype=tindakan&id=" + pendaftaranId,
                    columnDefs: [
                        {
                            targets: 3,
                            className: 'text-right'
                        }
                    ],
                    columns: [
                        { title: "No", data: "rownum" },
                        { title: "Nama Tindakan", data: "tindakan_jenazah" },
                        { title: "Qty", data: "qty_tindakan" },
                        {
                            title: "Harga",
                            data: "tarif_tindakan",
                            render: function (tarif_satuan) {
                                return 'Rp. ' + docoHelper.convertToRupiah(tarif_satuan);
                            }
                        },
                        {
                            title: "Aksi",
                            data: "aksi",
                            searchable: false,
                            orderable: false,
                        },
                    ],
                });
                table_jenazah_obat = $("#tabel-obat").docoTabel({
                    destroy: true,
                    scrollX: false,
                    filter: false,
                    sorting: [[1, "asc"]],
                    processing: true,
                    serverSide: true,
                    paging: false,
                    ajax: "get-cache-jenazah?cachetype=obat&id=" + pendaftaranId,
                    columnDefs: [
                        {
                            targets: 4,
                            className: 'text-right'
                        }
                    ],
                    columns: [
                        { title: "No", data: "rownum" },
                        { title: "Nama Obat Alkes", data: "obat_jenazah" },
                        { title: "Qty", data: "qty" },
                        { title: "Satuan", data: "obat_satuan" },
                        {
                            title: "Harga",
                            data: "obat_harga",
                            render: function (obat_harga) {
                                return 'Rp. ' + docoHelper.convertToRupiah(obat_harga);
                            }
                        },
                        {
                            title: "Aksi",
                            data: "aksi",
                            searchable: false,
                            orderable: false,
                        },
                    ],
                });

                table_jenazah_linen = $("#tabel-linen").docoTabel({
                    destroy: true,
                    scrollX: false,
                    filter: false,
                    sorting: [[1, "asc"]],
                    processing: true,
                    serverSide: true,
                    paging: false,
                    ajax: "get-cache-jenazah?cachetype=linen&id=" + pendaftaranId,
                    columns: [
                        { title: "No", data: "rownum" },
                        { title: "Nama Linen", data: "linen_jenazah" },
                        { title: "Qty", data: "qty" },
                        {
                            title: "Aksi",
                            data: "aksi",
                            searchable: false,
                            orderable: false,
                        },
                    ],
                });

                table_jenazah_alat = $("#tabel-alat").docoTabel({
                    destroy: true,
                    scrollX: false,
                    filter: false,
                    sorting: [[1, "asc"]],
                    processing: true,
                    serverSide: true,
                    paging: false,
                    ajax: "get-cache-jenazah?cachetype=alat&id=" + pendaftaranId,
                    columns: [
                        { title: "No", data: "rownum" },
                        { title: "Nama Alat", data: "alat_jenazah" },
                        { title: "Qty", data: "qty" },
                        {
                            title: "Aksi",
                            data: "aksi",
                            searchable: false,
                            orderable: false,
                        },
                    ],
                });
            }
        } else {
            $('#display_hidden_jenazah').addClass('hidden');
        }
    })
    // $(".daterange").daterangepicker({
    //     applyClass: "bg-slate-600",
    //     cancelClass: "btn-default",
    //     locale: {
    //         format: "DD-MMMM-YYYY"
    //     }
    // });
    $(".date").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY",
        },
        onStart: function () {
            var date = new Date();
            this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()])
        },
    });

    // date & time
    function getCurrentDate() {
        var d = new Date();
        var date = d.getDate();
        var month = d.getMonth();
        var montharr = ["Jan", "Feb", "Mar", "April", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"];

        month = montharr[month];

        var year = d.getFullYear();
        var day = d.getDay();

        var dayarr = ["Sun", "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat"];

        day = dayarr[day];
        return date + " " + month + " " + year;
    }
    function clock() {
        var d = new Date();
        var month = d.getMonth() + 1;
        var hour = checkTime(d.getHours());
        var min = checkTime(d.getMinutes());
        var sec = checkTime(d.getSeconds());
        var ampm = (hour >= 12) ? 'PM' : 'AM';
        var currentTime = hour + ":" + min + ":" + sec;

        if (document.getElementById("tgl_masukperiksa") != null) {
            document.getElementById("tgl_masukperiksa").innerHTML = getCurrentDate() + '  ' + currentTime;
            $('input[name="PendaftaranForm[tgl_masukperiksa]"]').val(d.getFullYear() + "-" + month + "-" + d.getDate() + " " + hour + ":" + min + ":" + sec);
        }
        // set time
        if (document.getElementById("tglpasienpulang") != null) {
            document.getElementById("tglpasienpulang").innerHTML = getCurrentDate() + '  ' + currentTime;
            $('input[name="PasienPulangForm[tglpasienpulang]"]').val(d.getFullYear() + "-" + month + "-" + d.getDate() + " " + hour + ":" + min + ":" + sec);
        }
    }
    function checkTime(i) {
        if (i < 10) { i = "0" + i; }  // add zero in front of numbers < 10
        return i;
    }

    setInterval(clock, 1000);
    // document.getElementById ("waktu-permintaan").innerHTML = getCurrentDate() + ' ' + clock();

    function startTime() {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);

        // $('#tgl_masukperiksa').innerHTML =  h + ":" + m + ":" + s;
        var t = setTimeout(startTime, 500);
    }

    //function jenazah
    $(document).on('click', '.delete-data', function (e) {
        e.preventDefault();
        var _url = $(this).attr('action');
        $(this).docoForm("click", {
            url: _url,
            skipConfirm: true,
            skipSuccessNotif: true,
            success: function (data) {
                table_jenazah_tindakan.draw();
                table_jenazah_obat.draw();
                table_jenazah_linen.draw();
                table_jenazah_alat.draw();
            }
        });
    })

    $('#btn-add-tindakan-jenazah').on('click', function (e) {
        e.preventDefault();
        var formData = [];
        var dataHarga = 0;
        if ($('#tindakan_jenazah').select2('data')[0]) {
            data_selecttindakan = $('#tindakan_jenazah').select2('data')[0];
            if (data_selecttindakan.id == '') {
                docoNotification('error', 'Terjadi Kesalahan', 'Tindakan Tidak Boleh Kosong');
                return false;
            }
            if ($('#tindakan_qty').val() == '' || $('#tindakan_qty').val() == 0) {
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Kosong');
                return false;
            }

            formData.push({ name: 'tindakan_jenazah', value: $('#tindakan_jenazah option:selected').text() });
            formData.push({ name: 'daftartindakan_id', value: $('#tindakan_jenazah').val() });
            formData.push({ name: 'qty_tindakan', value: $('#tindakan_qty').val() });
            formData.push({ name: 'tarif_satuan', value: data_selecttindakan.tarif_satuan });
            formData.push({ name: 'tarifcyto_tindakan', value: 0 });
            formData.push({ name: 'cyto_tindakan', value: 0 });

            var tarifTindakan = data_selecttindakan.tarif_satuan * $('#tindakan_qty').val();
            formData.push({ name: 'tarif_tindakan', value: tarifTindakan });

            if (data_selecttindakan.list_komponen != '') {
                var arr_listkomponen = JSON.parse(data_selecttindakan.list_komponen);
                var tind_komp = [];
                $.each(arr_listkomponen, function (k, v) {
                    v = JSON.parse(v);
                    arr_listkomponen[k] = {
                        komponentarif_id: v.komponentarif_id,
                        tindakanpelayanan_id: null,
                        tarif_kompsatuan: v.harga_tariftindakan,
                        tarif_tindakankomp: v.harga_tariftindakan,
                        tarifcyto_tindakankomp: 0,
                        subsidiasuransikomp: 0,
                        subsidipemerintahkomp: 0,
                        subsidirumahsakitkomp: 0,
                        iurbiayakomp: 0
                    };
                });
                formData.push({ name: 'additional_data', value: JSON.stringify({ list_komponen: arr_listkomponen }) });
            }
        }
        $(this).docoForm("click", {
            type: 'POST',
            url: 'save-cache-jenazah?id=' + pendaftaranId + '&type=tindakan',
            data: formData,
            dataType: 'json',
            skipConfirm: true,
            skipSuccessNotif: true,
            success: function (data) {
                $('#tindakan_jenazah').val('').trigger('change');
                $('#tindakan_qty').val('');
                table_jenazah_tindakan.draw();
            }
        });

    });
    $('#obat_jenazah').on('change', function () {
        var detailobat = $('#obat_jenazah').select2('data')[0];
        $('.satuanobat-nama').val(detailobat.satuankecil_nama);
        $('#satuan_obat').val(detailobat.satuankecil_id);
    });
    $('#btn-add-obat-jenazah').on('click', function (e) {
        e.preventDefault();
        var formData = [];
        var dataHarga = 0;
        if ($('#obat_jenazah').select2('data')[0]) {
            data_selectobat = $('#obat_jenazah').select2('data')[0];
            dataHarga = data_selectobat.harga;
            if (data_selectobat.id == '') {
                docoNotification('error', 'Terjadi Kesalahan', 'Obat Alkes Tidak Boleh Kosong');
                return false;
            }
            if ($('#qty_obat').val() == '' || $('#qty_obat').val() == 0) {
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Kosong');
                return false;
            }
            var qty = $('#qty_obat').val();
            if (qty > data_selectobat.qty_tersedia) {
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Melebihi Stok Tersedia');
                return false;
            }
            if ($('#satuan_obat').val() == '') {
                docoNotification('error', 'Terjadi Kesalahan', 'Satuan tidak boleh Kosong');
                return false;
            }

            formData.push({ name: 'obatalkes_id', value: $('#obat_jenazah').val() });
            formData.push({ name: 'qty', value: $('#qty_obat').val() });
            formData.push({ name: 'qty_tersedia', value: data_selectobat.qty_tersedia });
            formData.push({ name: 'satuankecil_id', value: data_selectobat.satuankecil_id });
            formData.push({ name: 'harganetto', value: data_selectobat.harganetto });
            formData.push({ name: 'hargajual', value: dataHarga });
            formData.push({ name: 'persendiscount', value: data_selectobat.persendiscount });
            formData.push({ name: 'persenppn', value: data_selectobat.persenppn });
            formData.push({ name: 'persenmargin', value: data_selectobat.persenmargin });
            formData.push({ name: 'jmldiscount', value: data_selectobat.jmldiscount });
            formData.push({ name: 'jmlmargin', value: data_selectobat.jmlmargin });
            formData.push({ name: 'jmlppn', value: data_selectobat.jmlppn });
            formData.push({ name: 'obat_jenazah', value: data_selectobat.text });
            formData.push({ name: 'obat_satuan', value: data_selectobat.satuankecil_nama });
            var obatHarga = dataHarga * $('#qty_obat').val();
            formData.push({ name: 'obat_harga', value: dataHarga });
        }

        $(this).docoForm("click", {
            url: 'save-cache-jenazah?id=' + pendaftaranId + '&type=obat',
            data: formData,
            skipConfirm: true,
            skipSuccessNotif: true,
            success: function (data) {
                $('#obat_jenazah').val('').trigger('change');
                $('#satuan_obat').val('').trigger('change');
                $('#qty_obat').val('');
                table_jenazah_obat.draw();
            }
        });

    });

    $('#btn-add-linen-jenazah').on('click', function (e) {
        e.preventDefault();
        var formData = [];
        if ($('#linen_jenazah').select2('data')[0]) {
            data_selectlinen = $('#linen_jenazah').select2('data')[0];
            if (data_selectlinen.id == '') {
                docoNotification('error', 'Terjadi Kesalahan', 'Linen Tidak Boleh Kosong');
                return false;
            }
            if ($('#qty_linen').val() == '' || $('#qty_linen').val() == 0) {
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Linen Tidak Boleh Kosong');
                return false;
            }
        }
        formData.push({ name: 'linen_jenazah', value: $('#linen_jenazah option:selected').text() });
        formData.push({ name: 'barang_id', value: $('#linen_jenazah').val() });
        formData.push({ name: 'qty', value: $('#qty_linen').val() });

        $(this).docoForm("click", {
            url: 'save-cache-jenazah?id=' + pendaftaranId + '&type=linen',
            data: formData,
            skipConfirm: true,
            skipSuccessNotif: true,
            success: function (data) {
                $('#linen_jenazah').val('').trigger('change');
                $('#qty_linen').val('');
                table_jenazah_linen.draw();
            }
        });
    });

    $('#btn-add-alat-jenazah').on('click', function (e) {
        e.preventDefault();
        var formData = [];
        if ($('#alat_jenazah').select2('data')[0]) {
            data_selectalat = $('#alat_jenazah').select2('data')[0];
            if (data_selectalat.id == '') {
                docoNotification('error', 'Terjadi Kesalahan', 'Alat Tidak Boleh Kosong');
                return false;
            }
            if ($('#qty_alat').val() == '' || $('#qty_alat').val() == 0) {
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Alat Tidak Boleh Kosong');
                return false;
            }
        }
        formData.push({ name: 'alat_jenazah', value: $('#alat_jenazah option:selected').text() });
        formData.push({ name: 'obatalkes_id', value: $('#alat_jenazah').val() });
        formData.push({ name: 'qty', value: $('#qty_alat').val() });

        $(this).docoForm("click", {
            url: 'save-cache-jenazah?id=' + pendaftaranId + '&type=alat',
            data: formData,
            skipConfirm: true,
            skipSuccessNotif: true,
            success: function (data) {
                $('#alat_jenazah').val('').trigger('change');
                $('#qty_alat').val('');
                table_jenazah_alat.draw();
            }
        });
    });
    $('#pasienpulangform-is_prb').on('change', function(e) {
        if($('#pasienpulangform-is_prb').data('bpjs-rujukan') == 1) {
            if($('#pasienpulangform-is_prb').is(":checked")) {
                if(typeof $('#rujukanpulangform-pegawai_kode_bpjs') != 'undefined') {
                    $('#rujukanpulangform-pegawai_kode_bpjs').prop('disabled', false);
                    $('.field-rujukanpulangform-pegawai_nama').addClass('hidden')
                    $('.field-rujukanpulangform-pegawai_kode_bpjs').removeClass('hidden')
                    $('.field-rujukanpulangform-diagnosa_keluar').addClass('hidden')
                    $('.field-rujukanpulangform-diagnosa_prb').removeClass('hidden')
                }
                if(typeof $('.reseptur.hidden') != 'undefined') {
                    $('.reseptur.hidden').removeClass('hidden');

                }
                if(typeof loadDataReseptur == 'function') {
                    loadDataReseptur();
                }
            } else {
                if(typeof $('#rujukanpulangform-pegawai_kode_bpjs') != 'undefined') {
                    $('#rujukanpulangform-pegawai_kode_bpjs').prop('disabled', true);
                    $('.field-rujukanpulangform-pegawai_nama').removeClass('hidden')
                    $('.field-rujukanpulangform-pegawai_kode_bpjs').addClass('hidden')
                    $('.field-rujukanpulangform-diagnosa_keluar').removeClass('hidden')
                    $('.field-rujukanpulangform-diagnosa_prb').addClass('hidden')
                }
                if(typeof $('.reseptur') != 'undefined') {
                    $('.reseptur').addClass('hidden');
                }
            }
        }
    });

})
