/*
* @Author: Ragnar-Lothbroc
* @Date:   2018-08-14 20:07:00
* @Last Modified by:   Ragnar-Lothbroc
* @Last Modified time: 2018-09-07 11:00:47
*/

var table;
var countPenerimaan = 0;
var is_donasi = false;
let totalNetto = 0;

$(document).ready(function(){
	$('.select-satuan').select2();
    $('.info-konversi').hide();
    $('.info-harga').hide();

    $("[tab-index='0']").focus();

    var attributes = {};
    var baseController = "/gudang/penerimaan-obat-supplier/";
    var setConsignment = false;

    $('input[type=checkbox]').on('change', function(evt) {
    if($('input[type=checkbox]:checked').length >= 2) {
        this.checked = false;
    	docoNotification('warning', 'Terjadi Kesalahan', 'Hanya boleh memilih 1 Jenis !');
    }
    })

    function isConsigmentChecked (attribute, value) {
        return $("#penerimaansupplierform-is_consigment").prop("checked") ? true : false;
    };
    
    $('#penerimaansupplierform-is_consigment').on('change', function() {
        setConsignment = $(this).prop('checked');
        $('.isConsigmentHeader').val(Number(setConsignment));
        console.log($("#penerimaansupplierform-is_consigment").prop("checked"));
        if(setConsignment) {
            $('div.legend-information__text').text("Item Consignment");
            $('div.nonconsignment').hide();
            $('div.isconsignment').show();
        } else {
            $('div.legend-information__text').text("Item Bukan Consignment");
            $('div.isconsignment').hide();
            $('div.nonconsignment').show();
        }
    });


    $(".pickadate").pickadate({
        format: "dd-mm-yyyy",
        formatSubmit: "yyyy-mm-dd",
        onStart: function() {
            var date = new Date();
            this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
    });

    $("#obatalkes_id").select2({
        placeholder: "Pilih Obat Alkes",
        minimumInputLength: 3,
        ajax : {
            url: baseController+"search-obat-alkes",
            dataType: 'json',
            quietMillis: 250,
            data: function (params) {
              var query = {
                search: params,
                tipe : 0,
                consignment: $("#penerimaansupplierform-is_consigment").prop("checked"),
              }
              return query;
            },
            processResults: function (data) {
              return {
                results: data.result
              };
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        },
    }).on('select2:select', function(e){
        var data = e.params.data;
        var itemConsignment = data.is_consigment == null ? false : data.is_consigment;

        $(".obatalkes_nama").val(data.text);
        $(".obatalkes_kode").val(data.kode);
        $('.item_consignment').val(itemConsignment);
    });

    $("#obatalkes").select2({
        placeholder: "Pilih Obat Alkes",
        minimumInputLength: 3,
        ajax : {
            url: baseController+"search-obat-alkes-consig?tipe=0",
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
    }).on('select2:select', function(e){
        var data = e.params.data;
        var itemConsignment = data.is_consigment == null ? false : data.is_consigment;

        $(".obatalkes_nama").val(data.text);
        $(".obatalkes_kode").val(data.kode);
        $('.item_consignment').val(itemConsignment);
    });

    $("#peg_mengetahui").select2({
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
              return {
                results: data.result
              };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });

    $('#satuankonversi_id').on('select2:select', function (e) {
        var _val = $(this).val();
        console.log(_val);
        $.ajax({
            type: 'GET',
            url: '/api/master/get-satuan-konversi?satuankonversi_id=' + _val,
            dataType: 'json',
            success: function(data) {
                var response = data.response;
                $('.satuanunit_nama_besar').val(response.besar);
                $('.satuanunit_nama_kecil').val(response.kecil);
                $('.nilai_konversi').val(response.nilai_konversi);
                $('#penerimaansupplierdetailform-qty_besar').trigger('keyup');
            }
        });
    });

    $('#penerimaansupplierdetailform-qty_besar').on('keyup', function(){
        var _val = $(this).val();
        var nilai_konversi = $('.nilai_konversi').val();
        var satuanunit_nama_besar = $('.satuanunit_nama_besar').val();
        var satuanunit_nama_kecil = $('.satuanunit_nama_kecil').val();
        var total_konversi = _val.toString().replace(/\.|,/g, '') * nilai_konversi;
        var text = total_konversi + " " + satuanunit_nama_kecil;
        $('.info-konversi').show();
        $('.total_konversi').html("<strong> Total Konversi : "+ text +" </strong>");
        onInputTotalHarga();
    });

    $('.total_harga').on('input', function(){
        onInputTotalHarga();
    });

    function onInputTotalHarga(donasi=false) {
        var total_harga = donasi ? harga_donasi : docoHelper.convertToAngka($('.total_harga').val());
        var nilai_konversi = $('.nilai_konversi').val();
        var qty = $('#penerimaansupplierdetailform-qty_besar').val().toString().replace(/\.|,/g, '') * nilai_konversi;
        var harga_satuan = total_harga/qty;
        harga_satuan = isNaN(harga_satuan) ? 0 : harga_satuan;
        harga_satuan = harga_satuan > 1 ? docoHelper.convertToRupiah(harga_satuan) : harga_satuan;
        $('.info-harga').show();
        $('.harga_netto').html("Harga Netto Satuan : Rp. "+ harga_satuan);
    }

    $('#penerimaansupplierdetailform-diskon').on('keyup', function(){
        match        = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
        valDisDesimal = match[1] + match[2];
        if(valDisDesimal > 100){
            this.value = 100;
        }else{
            this.value = match[1] + match[2];
        }
    });

    $('#penerimaansupplierdetailform-harga_netto').on('keyup', function (e) {
        match        = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
        valDisDesimal = match[1] + match[2];
        this.value = valDisDesimal;
    });

    $('#penerimaansupplierdetailform-harga_netto').on('change', function (e) {
        var angka = $(this).val();
        var number_string = angka.toString().toString().replace(/\./g, ','),
            split = number_string.split(','),
            absvalue = split[0];
        var _split = split[0].replace(/\-/g, '');
        var sisa = _split.length % 3,
            rupiah = _split.substr(0, sisa),
            ribuan = _split.substr(sisa).match(/\d{1,3}/gi);
        var simbol = absvalue.match(/\-/gm);
        simbol = simbol == null ? '' : simbol;

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        var _value = simbol + (split[1] != undefined ? rupiah + ',' + split[1] : rupiah);

        if (_value == 'NaN') {
            _value = 0;
        }
        $(this).val(_value);
    });

    $("#peg_menyetujui").select2({
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
              return {
                results: data.result
              };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });

    $("#supplier_id").select2({
        placeholder: "Pilih Supplier",
        minimumInputLength: 3,
        ajax : {
            url: baseController+"search-supplier",
            dataType: "json",
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
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });

    table = $("#penerimaan").docoTabel({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: true,
        paging: false,
        createdRow: function(row, data, index) {
            if(JSON.parse(data.is_consignment.toLowerCase()) != setConsignment) {
                $('td', row).addClass('consignment');
            }
        },
        ajax: baseController + "get-list-item",
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
                data: "qty_besar",
                orderable: false
            },
            {
                title: "Qty Konversi",
                data: "qty_kecil",
                orderable: false
            },
            {
                title: "Tanggal Kadaluarsa",
                data: "tgl_kadaluarsa",
                searchable: false,
                orderable: false,
                class: "text-center"
            },
            {
                title: "Total Harga Netto (Rp.)",
                data: "harga_netto",
                searchable: false,
                orderable: false,
                class: "text-right"
            },
            {
                title: "Diskon (%)",
                data: "diskon",
                searchable: false,
                orderable: false,
                class: "text-center"
            },
            {
                title: "No. Batch",
                data: "no_batch",
                searchable: false,
                orderable: false,
                class: "text-center"
            },
            {
                title: "Keterangan",
                data: "keterangan",
                searchable: false,
                orderable: false
            },
            {
                title: "Aksi",
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
        ],
        footerCallback: function(row, data, start, end, display) {
            let api = this.api();
            let res = this.api().ajax.json();
            if(res) {
                totalNetto = res.total_netto
            }

            $(api.column(6).footer()).html(totalNetto);

        },
    });

    $(document).on('click','.delete', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            skipConfirm : true,
            success : function (data) {
                table.draw();
                countPenerimaan = countPenerimaan - 1;
                if(countPenerimaan == 0) {
                    setTimeout(function(){
                        $("#simpan-penerimaan").prop('disabled', true);
                        $('#penerimaansupplierform-is_consigment').prop('disabled', false);
                        $('#penerimaansupplierform-is_donasi').prop('disabled', false);
                    }, 100);
                }
                else {
                    setTimeout(function(){
                        $("#simpan-penerimaan").prop('disabled', false);
                    }, 100);
                }
            }
        });
    });

    $("#penerimaan-form").submit(function(event){
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
                var harga_new_netto = is_donasi ? harga_donasi : 0;
                $("#obatalkes_id").val('').trigger('change');
                $("#satuankonversi_id").val('').trigger('change');
                $("#penerimaansupplierdetailform-qty_besar").val('');
                $("#penerimaansupplierdetailform-tgl_kadaluarsa").val('');
                $("#penerimaansupplierdetailform-qty_kecil").val('');
                $("#penerimaansupplierdetailform-harga_netto").val(harga_new_netto);
                $("#penerimaansupplierdetailform-ppn").val(0);
                $("#penerimaansupplierdetailform-diskon").val(0);
                $("#penerimaansupplierdetailform-no_batch").val("");
                $("#penerimaansupplierdetailform-keterangan").val("");

                $('.info-konversi').hide();
                $('.info-harga').hide();
                countPenerimaan = countPenerimaan + 1;
                setTimeout(function(){
                    $("#simpan-penerimaan").prop('disabled', false);
                    $('#penerimaansupplierform-is_consigment').prop('disabled', true);
                    // $('#penerimaansupplierdetailform-harga_netto').removeAttr('readonly');
                    $('#penerimaansupplierform-is_donasi').prop('disabled', true);
                    $("#obatalkes_id").focus();
                }, 100);
                table.draw();
            },
            done: function() {
                $("#simpan-penerimaan").prop('disabled', false);
                $('#penerimaansupplierform-is_consigment').prop('disabled', true);
            }
        });
    });

    $("#simpan-penerimaan").on("click",function (event) {
        event.preventDefault();
        var $supplier_id = $("#supplier_id").val();
        var $no_faktur = $('#penerimaansupplierform-no_faktur').val();
        var formData = $('#penerimaan-form').serializeArray();
        var consignmentLength = $('td.consignment').length;
        var message = "";

        if(setConsignment) {
            message = "Terdapat Item Bukan Consignment!";
        } else {
            message = "Terdapat Item Consignment!";
        }

        if(consignmentLength == 0) {
            $(this).docoForm("click",{
                url : "/gudang/penerimaan-obat-supplier/save",
                method : "POST",
                type : "json",
                data: $("#penerimaan-form").serializeArray(),
                success : function (data) {
                    var no_penerimaan = data.response.no_penerimaan;
                    var id_transaksi = data.response.id_transaksi;
                    setConsignment = false;
                    $("#penerimaan-form")[0].reset();
                    $("#peg_mengetahui").val('').trigger('change');
                    $("#harga_netto").val('').trigger('change');
                    $("#peg_menyetujui").val('').trigger('change');
                    $("#penerimaansupplierform-pajak_id").val('').trigger('change');
                    $("#penerimaansupplierform-payterm_id").val('').trigger('change');
                    $("#supplier_id").val('').trigger('change');
                    $("#penerimaansupplierform-no_faktur").val('');
                    $("#penerimaansupplierdetailform-harga_netto").val(0);
                    $("#penerimaansupplierdetailform-ppn").val(0);
                    $("#penerimaansupplierdetailform-diskon").val(0);
                    $('.info-konversi').hide();
                    $('.info-harga').hide();
                    $("[tab-index='0']").focus();
                    $('#penerimaansupplierform-is_consigment').val(0);
                    $('#penerimaansupplierform-is_donasi').val(0);
                    $('#penerimaansupplierform-is_donasi').prop('checked',false);
                    $('#penerimaansupplierform-is_consigment').prop('disabled', false);
                    $('#penerimaansupplierform-is_donasi').prop('disabled', false);
                    $('#penerimaansupplierdetailform-harga_netto').removeAttr('readonly');
                    $(table.column(6).footer()).html(0);
                    if(countPenerimaan == 0) {
                        setTimeout(function(){
                            $("#simpan-penerimaan").prop('disabled', true);
                        }, 100);
                    }
                    else {
                        setTimeout(function(){
                            $("#simpan-penerimaan").prop('disabled', false);
                        }, 100);
                    }

                    table.draw();

                    if(!need_verif) {
                        alertHarga(id_transaksi);
                    }

                    countPenerimaan = 0;
                    $('.isDonasiHeader').val(0);

                    (new PNotify({
                        title: "Berhasil",
                        text: "Data Penerimaan Obat Alkes Supplier dengan Nomor " + "<strong>" + no_penerimaan + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                        window.open("/gudang/inf-penerimaan-obat-alkes/print-pdf?id="+id_transaksi);
                        location.reload()
                    }).on('pnotify.cancel', function() {
                        location.reload()
                    });
                }
            });
        } else {
            return new PNotify({
                title: "Terjadi Kesalahan",
                text: message,
                addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                type: "warning"
            });
        }
    });

    $('#penerimaansupplierform-is_donasi').change(function(){
        onInputTotalHarga(true);
        if($(this).prop('checked')){
            is_donasi = true;
            $('#penerimaansupplierdetailform-harga_netto').val(harga_donasi).attr('readonly','readonly');
        }else{
            is_donasi = false;
            $('#penerimaansupplierdetailform-harga_netto').removeAttr('readonly');
        }
        $('.isDonasiHeader').val(is_donasi ? 1 : 0);
    });
});

$(document).on('keydown', null, 'alt+s',function(e){
    $('textarea').blur();
    $('input').each(function(){
        $(this).blur();
    });
    $('#simpan-penerimaan').click();
});

