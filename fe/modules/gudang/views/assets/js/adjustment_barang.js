/* 
    Author : Budi
*/
var tabAktif = '';
var table;
var tableKeluar;
var countMasuk = 0;
var countKeluar = 0;
var pegMengetahuiOpt;
var { phpform } = typeof phpVariables != 'undefined' ? phpVariables : {};

$(document).on('keyup','#adjusmenbarangmasukform-qty', function(event) {
    event.preventDefault();
    var _val = $(this).val();
    var _val_toAngka =  parseInt(docoHelper.convertToAngka(_val));
    var nilai_konversi = $('.nilai_konversi').val();
    var satuanunit_nama_besar = $('.satuanunit_nama_besar').val();
    var satuanunit_nama_kecil = $('.satuanunit_nama_kecil').val();
    var total_konversi = _val_toAngka * nilai_konversi;
    var text = docoHelper.convertToRupiah(total_konversi) + " " + satuanunit_nama_kecil;
    $('.info-konversi-masuk').show();
    $('.total_konversi_masuk').html("<strong> Total Konversi : "+ text +" </strong>");
});

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
    var baseController = "/gudang/adjustment-barang/";


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
                    console.log(val);
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
                    console.log(val);
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
                console.log(primaryId);
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
                                console.log(data)
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

    $('#content-masuk').docoLoad({
        url: baseController+'masuk',
        dataType: 'html',
        success : function(data) {
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
                ajax: baseUrl+"gudang/adjustment-barang/get-list-item-masuk",
                columns: [
                    {
                        title: "No",
                        data: "rowNum",
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Nama Barang', 
                        data: "barang_nama",
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
                    /*{
                        title: "Qty", 
                        data: "qty_besar",
                        orderable: false,
                        class: "text-right",
                    },
                    {
                        title: "Satuan Besar",
                        data: "satuanunit_nama_besar",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: "Qty", 
                        data: "qty_kecil",
                        orderable: false,
                        class: "text-right",
                    },
                    {
                        title: "Satuan Kecil",
                        data: "satuanunit_nama_kecil",
                        searchable: false,
                        orderable: false,
                    },*/
                    {
                        title: "Tanggal Kadaluarsa",
                        data: "tgl_kadaluarsa",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: "Harga Netto",
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
                        $("#barang_id").val('').trigger('change');
                        $("#satuankonversi_id").val('').trigger('change');
                        $("#adjusmenbarangmasukform-harga_netto").val('');
                        $("#adjusmenbarangmasukform-qty").val('');
                        $('#adjusmenbarangmasukform-total_konversi').val('');
                        $('#adjusmenbarangmasukform-no_batch').val('');
                        $("#tab-keluar").css("pointer-events", "none");
                        $('.info-konversi-masuk').hide();
                        countMasuk = countMasuk + 1;
                        setTimeout(function(){
                            $("#simpan-adjustment").prop('disabled', false);
                            $("#barang_id").focus();
                        }, 100);
                        table.draw();
                    },
                    done: function() {
                        setTimeout(function(){
                            $("#simpan-adjustment").prop('disabled', false);
                            $("#barang_id").focus();
                        }, 100);
                    }
                });
            });

            $('#satuankonversi_id').on('select2:select', function (e) {
                var _val = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: '/gudang/adjustment-barang/get-satuan-konversi?satuankonversibrg_id=' + _val,
                    dataType: 'json',
                    success: function(data) {
                        var response = data.response;
                        var selectedBarang = $('#barang_id').find(":selected").data('data');
                        console.log(selectedBarang);
                        if(selectedBarang.harganetto_ygdipakai === null) {
                            selectedBarang.harganetto_ygdipakai = 0;
                        }
                        console.log(selectedBarang.harganetto_ygdipakai);
                        $('.satuanunit_nama_besar').val(response.besar);
                        $('.satuanunit_nama_kecil').val(response.kecil);
                        $('.nilai_konversi').val(response.nilai_konversi);
                        $("#adjusmenbarangmasukform-harga_netto").val(selectedBarang.harganetto_ygdipakai * response.nilai_konversi);
                        $('#adjusmenbarangmasukform-qty').trigger('keyup')
                    }
                });
            });
        }
    });
    
    $('#tab-masuk').on('click', function (e) {
        tabAktif = 'masuk';
    });

    $('#tab-keluar').on("click", function(e){
        if (!keluar){
            tabAktif = 'keluar';
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

                    $("#barang_id_keluar").select2({
                        placeholder: "Pilih Nama Barang",
                        minimumInputLength: 3, 
                        ajax : {
                            url: baseController+"search-barang?tipe=1",
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
                        $(".barang_nama").val(data.text);
                        $('.info-obat').show();
                        // $('.satuankecil').html("<strong> Satuan Kecil : "+ data.satuankecil_nama +" </strong>");
                        var qty_tersedia = docoHelper.convertToRupiah(data.qty_tersedia);
                        $('.stok').html("<strong> Stok Tersedia : "+ qty_tersedia + " " + data.satuankecil_nama +" </strong>");
                        $("#barang_id_keluar").focus();
                    });

                    tableKeluar = $("#adjus-keluar").docoTabel({
                        filter: false,
                        displayLength: 10,
                        processing: true,
                        serverSide: true,
                        stateSave: true,
                        ajax: baseUrl+"gudang/adjustment-barang/get-list-item-keluar",
                        columns: [
                            {
                                title: "No",
                                data: "rowNum",
                                searchable: false,
                                orderable: false
                            },
                            {
                                title: 'Nama Barang', 
                                data: "barang_nama",
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
                            /*{
                                title: "Qty", 
                                data: "qty_besar",
                                orderable: false
                            },
                            {
                                title: "Satuan Besar",
                                data: "satuanunit_nama_besar",
                                searchable: false,
                                orderable: false,
                                class: "text-center"
                            },
                            {
                                title: "Qty", 
                                data: "qty_kecil",
                                orderable: false
                            },
                            {
                                title: "Satuan Kecil",
                                data: "satuanunit_nama_kecil",
                                searchable: false,
                                orderable: false,
                                class: "text-center"
                            },*/
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
                            url: '/gudang/adjustment-barang/get-satuan-konversi?satuankonversibrg_id=' + _val,
                            dataType: 'json',
                            success: function(data) {
                                var response = data.response;
                                console.log(response);
                                $('.satuanunit_nama_besar_keluar').val(response.besar);
                                $('.satuanunit_nama_kecil_keluar').val(response.kecil);
                                $('.nilai_konversi_keluar').val(response.nilai_konversi);
                            }
                        });
                    });

                    $('#adjusmenbarangkeluarform-qty').on('keyup', function(){
                        var _val = $(this).val();
                        var _val_toAngka =  docoHelper.convertToAngka(_val);
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
                                $("#barang_id_keluar").val('').trigger('change');
                                $("#satuankonversi_id_keluar").val('').trigger('change');
                                $("#adjusmenobatkeluarform-qty").val('');
                                $("#adjusmenobatkeluarform-alasan").val('');
                                $('#adjusmenobatkeluarform-total_konversi').val('');
                                $('#adjusmenbarangkeluarform-qty, #adjusmenbarangkeluarform-alasan').val('');
                                $('#adjusmenbarangkeluarform-no_batch').val('');
                                $("#tab-masuk").css("pointer-events", "none");
                                $('.info-obat').hide();
                                $('.total_konversi_keluar').hide();
                                countKeluar = countKeluar + 1;
                                setTimeout(function(){
                                    $("#simpan-adjustment").prop('disabled', false);
                                    $("#barang_id_keluar").focus();
                                }, 100);
                                table.draw();
                                tableKeluar.draw();
                            },
                            done: function() {
                                setTimeout(function(){
                                    $("#simpan-adjustment").prop('disabled', false);
                                    $("#barang_id_keluar").focus();
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
        url : "/gudang/adjustment-barang/save",
        method : "POST",
        type : "json",
        data: $("#ajax-form").serializeArray(),
        success : function (data) {
            var no_adjusmen = data.response.no_adjusmen;
            $("#adjusmenobatform-no_adjusmen").val();
            $("#barang_id").val('').trigger('change');
            $("#satuankecil_id").val('').trigger('change');
            $("#barang_id_keluar").val('').trigger('change');
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
                window.open("/gudang/adjustment-barang/cetak?no_adjusmen="+data.response.no_adjusmen+"&tipe="+$tipe);
            }).on('pnotify.cancel', function() {

            });
        }
    });
});

