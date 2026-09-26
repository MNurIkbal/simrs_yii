/*
    Author : Budi
*/
var tabAktif = '';
var table;
var tableKeluar;
var countMasuk = 0;
var countKeluar = 0;
var baseprice = 0;
var pegMengetahuiOpt;
var { phpform } = typeof phpVariables != 'undefined' ? phpVariables : {};

$(document).on('keyup','#adjusmenobatmasukform-qty', function(event) {
    event.preventDefault();
    showKonversi();
});

// $(document).on('keyup','#adjusmenobatmasukform-harga_netto', function(event) {
//     event.preventDefault();
//     showKonversi();
// });

function showKonversi() {
    var _val_qty = $('#adjusmenobatmasukform-qty').val();
    var _val_qty_toAngka =  parseInt(docoHelper.convertToAngka(_val_qty));
    var nilai_konversi = $('.nilai_konversi').val();
    var satuanunit_nama_besar = $('.satuanunit_nama_besar').val();
    var satuanunit_nama_kecil = $('.satuanunit_nama_kecil').val();
    var total_konversi = _val_qty_toAngka * nilai_konversi;
    var text = docoHelper.convertToRupiah(total_konversi) + " " + satuanunit_nama_kecil;

    var _val_tot_harga = $('#adjusmenobatmasukform-harga_netto').val();
    if (!Number.isInteger(_val_tot_harga)){
        total_harga = Number(_val_tot_harga).toFixed(2)
    }
    var total_harga_netto =  parseInt(docoHelper.convertToAngka(total_harga));
    var harga_netto_satuan;
    
    if(total_konversi == 0) {
        harga_netto_satuan = 0;
    } else {
        harga_netto_satuan = total_harga_netto / total_konversi;
    }

    // var textHarga = docoHelper.convertToRupiah(harga_netto_satuan);
    var harga_konversi = baseprice / nilai_konversi;
    var textHarga = docoHelper.convertToRupiah(harga_konversi);
    if(textHarga == null || textHarga == "" || textHarga == undefined) {
        textHarga = 0;
    }

    $('.info-konversi-masuk').show();
    $('.total_konversi_masuk').html("<strong> Total Konversi : "+ text +" </strong>");
    $('.harga_netto_satuan').html("<strong> Baseprice Sekarang : Rp"+ textHarga +" /"+ satuanunit_nama_kecil +" </strong>");
}

function pegMengetahuiDefaultValue()
{
    if (typeof phpform != 'undefined') {
        if (phpform.defaultuser) {
            var $currUserOpt = $("<option selected></option>").val(phpform.user.id).text(phpform.user.name);
            pegMengetahuiOpt.append($currUserOpt).trigger('change');
        } else {
            pegMengetahuiOpt.val('').trigger('change');
        }
    } else {
        pegMengetahuiOpt.val('').trigger('change');
    }
}

$(document).ready(function(){
    var attributes = {};
    var masuk = true;
    var keluar = false;
    var baseController = "/gudang/adjustment-obat-alkes/";

    pegMengetahuiOpt = $("#peg_mengetahui_id").select2({
        placeholder: "Pilih Pegawai Mengetahui",
        minimumInputLength: 3,
        ajax : {
            url: baseController+"search-pegawai",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
              var query = {
                search: params,
              }
              return params;
            },
            processResults: function (data) {
                $.each(data.result, function (key,val) {
                    $(".satuanunit_nama").val(val.text);
                });
              return {
                results: data.result
              };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });
    pegMengetahuiDefaultValue();

    $("#peg_menyetujui_id").select2({
        placeholder: "Pilih Pegawai Menyetujui",
        minimumInputLength: 3,
        ajax : {
            url: baseController+"search-pegawai",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
              var query = {
                search: params,
              }
              return params;
            },
            processResults: function (data) {
                $.each(data.result, function (key,val) {
                    $(".satuanunit_nama").val(val.text);
                });
              return {
                results: data.result
              };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });

    $(document).on('click', '.spa', function(e) {
        e.preventDefault();
        var type = $(this).attr('data-type');
        var render = $(this).attr('data-render');
        var target = $(this).attr('data-target');
        var tab = $(this).attr('data-tab');
        var action = $(this).attr('action');
        var form_id = $(this).attr('form-id');
        var contentTarget = $(target + " div").attr("id");
        $("#" + tab).trigger("click");
        var urlRender;
        if (type == 'wp') {
            var tableId = $(this).attr('data-table');
            var table = $(tableId).DataTable();
            var tableData = table.row(".selected").data();
            if (typeof tableData !== 'undefined') {
                var primaryId = tableData.primary;
                if (typeof primaryId !== 'undefined') {
                    urlRender = baseController + render + primaryId;
                }
            } else {
                docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
                return false;
            }
        } else {
            urlRender = baseController + render;
        }

        if (typeof action !== 'undefined') {
            var data_form = $('#' + form_id).serializeArray();
            $(this).docoForm("click", {
                data: data_form,
                url: action,
                method: 'POST',
                success: function (data) {
                    setTimeout(function () {
                        $('#' + contentTarget).docoLoad({
                            url: urlRender,
                            dataType: 'html',
                            success : function(data) {
                                // $(" .select2 ").select2();
                            }
                        });
                    }, 1000);
                }
            });
        } else {
            $('#' + contentTarget).docoLoad({
                url: urlRender,
                dataType: 'html',
                success : function(data) {
                    // $(" .select2 ").select2();
                }
            });
        }
    });

    $(document).on('keydown', null, 'alt+s', function (event) {
        $("#simpan-adjustment").click();
    });

    $('#content-masuk').docoLoad({
        url: baseController+'masuk?trace=1',
        dataType: 'html',
        success : function(data) {
            var harga_satuan_input = 0;
            var total_harga_input = 0;

            tabAktif = 'masuk';
            $(".pickadate").pickadate({
                format: "dd-mm-yyyy",
                formatSubmit: "yyyy-mm-dd",
                onStart: function() {
                    var date = new Date();
                    this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
                }
            });

            table = $("#adjus-masuk").docoTabel({
                filter: false,
                displayLength: 10,
                processing: true,
                serverSide: true,
                stateSave: true,
                paging: false,
                info: false,
                scrollY: "450px",
                ajax: baseUrl+"gudang/adjustment-obat-alkes/get-list-item-masuk",
                columns: [
                    {
                        title: "No",
                        data: "rowNum",
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Kode Obat Alkes',
                        data: "obatalkes_kode",
                        orderable: false
                    },
                    {
                        title: 'Nama Obat Alkes',
                        data: "obatalkes_nama",
                        orderable: false
                    },
                    {
                        title: "Qty Penerimaan",
                        data: "qty_penerimaan",
                        orderable: false,
                        class: "text-right"
                    },
                    {
                        title: "Qty Konversi",
                        data: "qty_konversi",
                        orderable: false,
                        class: "text-right"
                    },
                    {
                        title: "Tanggal Kadaluarsa",
                        data: "tgl_kadaluarsa",
                        searchable: false,
                        orderable: false,
                        class: "text-center"
                    },
                    {
                        title: "Total Harga (Rp.)",
                        data: "harga_netto",
                        searchable: false,
                        orderable: false,
                        class: "text-right"
                    },
                    {
                        title: "No. Batch",
                        data: "no_batch",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: "Keterangan",
                        data: "keterangan",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: "Aksi",
                        data: "aksi",
                        searchable: false,
                        orderable: false,
                        class: "text-center"
                    }
                ]
            });

            $(document).on('click','.delete', function(event) {
                event.preventDefault();
                $(this).docoForm('delete',{
                    skipConfirm : true,
                    success : function (data) {
                        table.draw();
                        countMasuk = countMasuk - 1;
                        if(countMasuk == 0) {
                            setTimeout(function(){
                                $("#simpan-adjustment").prop('disabled', true);
                            }, 100);
                            $("#tab-keluar").css("pointer-events", "auto");
                        }
                        else {
                            setTimeout(function(){
                                $("#simpan-adjustment").prop('disabled', false);
                            }, 100);
                        }
                    }
                });
            });

            $("#masuk-form").submit(function(event){
                event.preventDefault();
                var _value = $(this).serializeArray();
                if (Object.keys(attributes).length) {
                    $.each(attributes, function (key, val) {
                        _value.push({
                            name : key,
                            value : val
                        });
                    });
                }
                $(this).docoForm("submit",{
                    data : _value,
                    success : function (data) {
                        $("#obatalkes_id").val('').trigger('change');
                        $("#satuankonversi_id").val('').trigger('change');
                        $("#adjusmenobatmasukform-harga_netto_satuan").val('');
                        $("#adjusmenobatmasukform-harga_netto").val('');
                        $("#adjusmenobatmasukform-qty").val('');
                        $('#adjusmenobatmasukform-total_konversi').val('');
                        $('#adjusmenobatmasukform-no_batch').val('');
                        $('#adjusmenobatmasukform-keterangan').val('');
                        $('#adjusmenobatmasukform-tgl_kadaluarsa').val('');
                        $("#tab-keluar").css("pointer-events", "none");
                        $('.info-konversi-masuk').hide();
                        countMasuk = countMasuk + 1;
                        setTimeout(function(){
                            $("#simpan-adjustment").prop('disabled', false);
                            $("#obatalkes_id").focus();
                        }, 100);
                        table.draw();
                    },
                    done: function() {
                        setTimeout(function(){
                            $("#simpan-adjustment").prop('disabled', false);
                            $("#obatalkes_id").focus();
                        }, 100);
                    }
                });
            });

            $('#satuankonversi_id').on('select2:select', function (e) {
                var _val = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: '/gudang/adjustment-obat-alkes/get-satuan-konversi?satuankonversi_id=' + _val,
                    dataType: 'json',
                    success: function(data) {
                        var response = data.response;
                        var selectedObat = $('#obatalkes_id').find(":selected").data('data');

                        if(selectedObat.harganetto_ygdipakai === null) {
                            selectedObat.harganetto_ygdipakai = 0;
                        }

                        var harga_baseprice = selectedObat.harganetto_ygdipakai * response.nilai_konversi;
                        baseprice = harga_baseprice;

                        $('.satuanunit_nama_besar').val(response.besar);
                        $('.satuanunit_nama_kecil').val(response.kecil);
                        $('.nilai_konversi').val(response.nilai_konversi);
                        $('#adjusmenobatmasukform-qty').val(1);
                        $("#adjusmenobatmasukform-harga_netto_satuan").val(docoHelper.convertToRupiah(harga_baseprice));
                        $("#adjusmenobatmasukform-harga_netto").val(docoHelper.convertToRupiah(harga_baseprice));
                        $('#adjusmenobatmasukform-qty').trigger('keyup');

                        $(".harga_netto_wrapper span.input-group-addon").text(' / ' + response.besar);
                    }
                });
            });

            $('#adjusmenobatmasukform-qty').on('keyup', function() {
                // var str = $(this).val()                
                // var purgeCommaQty  = str.replaceAll('.', '')

                // var _qty = parseInt(purgeCommaQty);
                var _qty = parseInt($(this).val());
                var _harga = baseprice;
                var display_price = 0;

                if(isNaN(_qty)) {
                    _qty = 0;
                }

                if(harga_satuan_input != 0 && harga_satuan_input != baseprice) {
                    _harga = harga_satuan_input;
                }

                // var harga_netto = _harga * _qty
                // if (!Number.isInteger(harga_netto)){
                //     harga_netto = Number(harga_netto).toFixed(2)
                // }
                
                // $("#adjusmenobatmasukform-harga_netto").val(harga_netto);
                display_price = _harga * _qty;

                $("#adjusmenobatmasukform-harga_netto").val(docoHelper.convertToRupiah(display_price));
            });

            $('#adjusmenobatmasukform-harga_netto_satuan').on('keyup', function() {
                var harga_input = $(this).val();
                var _qty = parseInt($('#adjusmenobatmasukform-qty').val());

                harga_satuan_input = parseInt(docoHelper.convertToAngka(harga_input));
                
                $("#adjusmenobatmasukform-harga_netto_satuan").val(docoHelper.convertToRupiah(harga_satuan_input));
                $("#adjusmenobatmasukform-harga_netto").val(
                    docoHelper.convertToRupiah(harga_satuan_input * _qty)
                );
            });

            $('#adjusmenobatmasukform-harga_netto').on('keyup', function() {
                var total_input = $(this).val();
                var _qty = parseInt($('#adjusmenobatmasukform-qty').val());

                total_satuan_input = parseInt(docoHelper.convertToAngka(total_input));

                $("#adjusmenobatmasukform-harga_netto_satuan").val(
                    docoHelper.convertToRupiah(total_satuan_input / _qty)
                );
                $("#adjusmenobatmasukform-harga_netto").val(docoHelper.convertToRupiah(total_satuan_input));
            });
        }
    });

    $('#tab-masuk').on("click", function() {
        tabAktif = 'masuk';
    });

    $('#tab-keluar').on("click", function(e){
        tabAktif = 'keluar';
        if (!keluar){
            keluar = true;
            $('#content-keluar').docoLoad({
                url: baseController+'keluar',
                dataType: 'html',
                success : function(data) {
                    $(".pickadate").pickadate({
                        format: "dd-mm-yyyy",
                        formatSubmit: "yyyy-mm-dd",
                        onStart: function() {
                            var date = new Date();
                            this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
                        }
                    });

                    $("#obatalkes_id_keluar").select2({
                        placeholder: "Pilih Obat Alkes",
                        minimumInputLength: 3,
                        ajax : {
                            url: baseController+"search-obat-alkes?tipe=1",
                            dataType: 'json',
                            quietMillis: 250,
                            data: function (params) {
                              var query = {
                                search: params,
                              }
                              return params;
                            },
                            processResults: function (data) {
                              return {
                                results: data.result
                              };
                            },
                            dropdownCssClass: 'bigdrop',
                            escapeMarkup: function (m) { return m; },
                        },
                    }).on('select2:select', function(e) {
                        var data = e.params.data;
                        $(".obatalkes_kode").val(data.kode);
                        $(".obatalkes_nama").val(data.text);
                        $('.info-obat').show();
                        // $('.satuankecil').html("<strong> Satuan Kecil : "+ data.satuankecil_nama +" </strong>");
                        var qty_tersedia = docoHelper.convertToRupiah(data.qty_tersedia);
                        $('.stok').html("<strong> Stok Tersedia : "+ qty_tersedia + " " + data.satuankecil_nama +" </strong>");
                        $("#obatalkes_id_keluar").focus();
                    });

                    tableKeluar = $("#adjus-keluar").docoTabel({
                        filter: false,
                        displayLength: 10,
                        processing: true,
                        serverSide: true,
                        stateSave: true,
                        paging: false,
                        info: false,
                        scrollY: "450px",
                        ajax: baseUrl+"gudang/adjustment-obat-alkes/get-list-item-keluar",
                        columns: [
                            {
                                title: "No",
                                data: "rowNum",
                                searchable: false,
                                orderable: false
                            },
                            {
                                title: 'Kode Obat Alkes',
                                data: "obatalkes_kode",
                                orderable: false
                            },
                            {
                                title: 'Nama Obat Alkes',
                                data: "obatalkes_nama",
                                orderable: false
                            },
                            {
                                title: "Qty Pengeluaran",
                                data: "qty_pengeluaran",
                                orderable: false,
                                class: "text-right"
                            },
                            {
                                title: "Qty Konversi",
                                data: "qty_konversi",
                                orderable: false,
                                class: "text-right"
                            },
                            {
                                title: "Alasan Pengeluaran",
                                data: "alasan",
                                searchable: false,
                                orderable: false,
                            },
                            {
                                title: "No. Batch",
                                data: "no_batch",
                                searchable: false,
                                orderable: false,
                            },
                            {
                                title: "Keterangan",
                                data: "keterangan",
                                searchable: false,
                                orderable: false,
                            },
                            {
                                title: "Aksi",
                                data: "aksi",
                                searchable: false,
                                orderable: false,
                                class: "text-center"
                            }
                        ],
                    });

                    $(document).on('click','.delete-keluar', function(event) {
                        event.preventDefault();
                        $(this).docoForm('delete',{
                            skipConfirm : true,
                            success : function (data) {
                                tableKeluar.draw();
                                countKeluar = countKeluar - 1;
                                if(countKeluar == 0) {
                                    setTimeout(function(){
                                        $("#simpan-adjustment").prop('disabled', true);
                                    }, 100);
                                    $("#tab-masuk").css("pointer-events", "auto");
                                }
                                else {
                                    setTimeout(function(){
                                        $("#simpan-adjustment").prop('disabled', false);
                                    }, 100);
                                }
                            }
                        });
                    });

                    $('#satuankonversi_id_keluar').on('select2:select', function (e) {
                        var _val = $(this).val();
                        $.ajax({
                            type: 'GET',
                            url: '/gudang/adjustment-obat-alkes/get-satuan-konversi?satuankonversi_id=' + _val,
                            dataType: 'json',
                            success: function(data) {
                                var response = data.response;
                                $('.satuanunit_nama_besar_keluar').val(response.besar);
                                $('.satuanunit_nama_kecil_keluar').val(response.kecil);
                                $('.nilai_konversi_keluar').val(response.nilai_konversi);
                            }
                        });
                    });

                    $('#adjusmenobatkeluarform-qty').on('keyup', function(){
                        var _val = parseInt($(this).val());
                        var _val_toAngka =  parseInt(docoHelper.convertToAngka(_val));
                        var nilai_konversi = $('.nilai_konversi_keluar').val();
                        var satuanunit_nama_besar = $('.satuanunit_nama_besar_keluar').val();
                        var satuanunit_nama_kecil = $('.satuanunit_nama_kecil_keluar').val();
                        var total_konversi = _val_toAngka * nilai_konversi;
                        var text = docoHelper.convertToRupiah(total_konversi) + " " + satuanunit_nama_kecil;

                        $('.total_konversi_keluar').show();
                        $('.total_konversi_keluar').html("<strong> Total Konversi : "+ text +" </strong>");
                    });

                    $("#keluar-form").submit(function(event){
                        event.preventDefault();
                        var _value = $(this).serializeArray();
                        if (Object.keys(attributes).length) {
                            $.each(attributes, function (key, val) {
                                _value.push({
                                    name : key,
                                    value : val
                                });
                            });
                        }
                        $(this).docoForm("submit",{
                            data : _value,
                            success : function (data) {
                                $("#obatalkes_id_keluar").val('').trigger('change');
                                $("#satuankonversi_id_keluar").val('').trigger('change');
                                $("#adjusmenobatkeluarform-qty").val('');
                                $("#adjusmenobatkeluarform-alasan").val('');
                                $('#adjusmenobatkeluarform-total_konversi').val('');
                                $('#adjusmenobatkeluarform-no_batch').val('');
                                $('#adjusmenobatkeluarform-keterangan').val('');
                                $("#tab-masuk").css("pointer-events", "none");
                                $('.info-obat').hide();
                                $('.total_konversi_keluar').hide();
                                countKeluar = countKeluar + 1;
                                setTimeout(function(){
                                    $("#simpan-adjustment").prop('disabled', false);
                                    $("#obatalkes_id_keluar").focus();
                                }, 100);
                                table.draw();
                                tableKeluar.draw();
                            },
                            done: function() {
                                setTimeout(function(){
                                    $("#simpan-adjustment").prop('disabled', false);
                                    $("#obatalkes_id_keluar").focus();
                                }, 100);
                            }
                        });
                    });
                }
            });
        }
    });
});

$(".pickadate").pickadate({
    format: "dd-mm-yyyy",
    formatSubmit: "yyyy-mm-dd",
    onStart: function() {
        var date = new Date();
        this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
    }
});

$("#simpan-adjustment").on("click",function (event) {
    event.preventDefault();
    var $form = '';
    var $tabel = '';
    var $tipe = '';
    var $jenis = '';
    $(".tab-aktif").val(tabAktif);
    if(tabAktif == 'masuk') {
        $form = $("#masuk-form").serializeArray();
        $tabel = table;
        $tipe = 0;
        $jenis = 'Masuk ';
    }
    else {
        $form = $("#keluar-form").serializeArray();
        $tabel = tableKeluar;
        $tipe = 1;
        $jenis = 'Keluar ';
    }
    $(this).docoForm("click",{
        url : "/gudang/adjustment-obat-alkes/save?trace=1",
        method : "POST",
        type : "json",
        data: $("#ajax-form").serializeArray(),
        success : function (data) {
            var no_adjusmen = data.response.no_adjusmen;
            var _id_adj = data.response.id_adjustment;
            $("#adjusmenobatform-no_adjusmen").val();
            $("#obatalkes_id").val('').trigger('change');
            $("#satuankecil_id").val('').trigger('change');
            $("#obatalkes_id_keluar").val('').trigger('change');
            $("#satuankecil_id_keluar").val('').trigger('change');
            $("#adjusmenobatmasukform-harga_netto").val('');
            $("#adjusmenobatmasukform-qty").val('');
            pegMengetahuiDefaultValue();
            $("#peg_menyetujui_id").val('').trigger('change');
            $("#adjusmenobatform-no_adjusmen").val(no_adjusmen);
            if($tipe == 0) {
                $("#tab-keluar").css("pointer-events", "auto");
                $('.info-konversi').hide();
            }
            else {
                $("#tab-masuk").css("pointer-events", "auto");
                $('.info-obat').hide();
            }
            setTimeout(function(){
                $("#simpan-adjustment").prop('disabled', true);
            }, 100);

            alertHarga(_id_adj);

            $tabel.draw();
            (new PNotify({
                title: "Berhasil",
                text: "Data Adjustment "+ $jenis + " dengan Nomor " + "<strong>" + no_adjusmen + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
            })).get().on('pnotify.confirm', function() {
                // Print
                window.open("/gudang/adjustment-obat-alkes/cetak?no_adjusmen="+data.response.no_adjusmen+"&tipe="+$tipe);
            }).on('pnotify.cancel', function() {
                location.reload(true);
            });
        }
    });
});
