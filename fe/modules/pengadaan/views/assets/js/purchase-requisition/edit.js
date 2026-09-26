/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

if(isLargeUnit) {
    $(".selectSatuan").prop('disabled', false);
}

$(".addrow").click(function(){
    var parent = $("#tr-default");
    var _row = parseInt(parent.attr("data-last"));
    var _inc = parseInt(_row) + 1;
    var _isDisabled = null;

    if(!isLargeUnit) {
        _isDisabled = "disabled";
    }

    _listIncremnt["new_"+_inc] = {
        id : _row + 1,
        obatalkes_id : null,
        stok: 0,
        stok_farmasi: 0,
        stok_gudang: 0,
        stok_lain: 0,
        qty : 0,
        satuan_id : null,
        catatan : null
    };

    parent.attr("data-last", _inc);

    var obatalkes_id = "<select name=\'InfoPrForm[obatalkes_id]["+_inc+"]\' class=\'form-control input-sm selectObat\' id=\'obatalkes_id_"+_inc+"\'><option></option></select>";

    var satuan_id = "<select name=\'InfoPrForm[satuan_id]["+_inc+"]\' class=\'form-control input-sm selectSatuan satuan_id\' data-konversi=\'1\' id=\'satuan_id_"+_inc+"\' " + _isDisabled + "></select>";

    var stok = "<span id=\'stok_"+_inc+"\'></span>";
    var stok_farmasi = "<span id=\'stok_farmasi_"+_inc+"\'></span>";
    var stok_gudang = "<span id=\'stok_gudang_"+_inc+"\'></span>";
    var stok_lain = "<span id=\'stok_lain_"+_inc+"\'></span>";

    var qty = "<input type=\'text\' name=\'InfoPrForm[qty]["+_inc+"]\' class=\'form-control qty input-sm text-right\' id=\'qty_"+_inc+"\'>";

    var catatan = "<input type=\'text\' name=\'InfoPrForm[catatan]["+_inc+"]\' class=\'form-control catatan input-sm\' id=\'catatan_"+_inc+"\'>";

    var button_delete = "<button type=\'button\' class=\'deleteRow btn btn-danger btn-custom\'><span class=\'fa fa-trash\'></span></button>";

    var data = "<tr data-row=\'"+ _inc +"\' class=\'child\'>"+
                    "<td>" + _inc + "</td>" +
                    "<td colspan='2'>"+ obatalkes_id +"</td>" +
                    "<td></td>" +
                    "<td></td>" +
                    "<td>"+ stok +"</td>" +
                    "<td>"+ stok_farmasi +"</td>" +
                    "<td>"+ stok_gudang +"</td>" +
                    "<td>"+ stok_lain +"</td>" +
                    "<td></td>" +
                    "<td>"+ qty +"</td>" +
                    "<td>"+ satuan_id +"</td>" +
                    "<td>"+ catatan +"</td>" +
                    "<td></td>" +
                    "<td>"+ button_delete +"</td>" +
                "</tr>";

    $("#tr-default").before(data);

    $("#satuan_id_"+_inc).select2();
    $("#obatalkes_id_"+_inc).select2({
        language: {
            errorLoading: function() { return "Please Wait .." }
        },
        placeholder: "Kode / Nama Obat",
        minimumInputLength: 2,
        ajax: {
            data: function(params) {
                var query = {
                    term: params.term,
                    is_consignment: isConsignment == 1 ? true : false
                };
    
                return query;
            },
            url: '/pengadaan/purchase-requisition/search-obat',
            dataType: 'json',
            quietMillis: 250,
            delay: 250,
        },
    });

    $("#obatalkes_id_"+_inc).on('select2:select', function(e) {
        if(this.value in _listIncremnt) {
            $("#obatalkes_id_"+_inc).val(null).trigger("change");
            docoNotification('warning', "Peringatan!", "Obat sudah ada pada list");
            return false;
        }

        $("#stok_"+_inc).html("0");
        $("#satuan_id_"+_inc).find("Option").remove();
        var data_select = e.params.data;
        var data_satuan = data_select.satuan;
        $.each(data_satuan, function(index, value) {
            var newOpt = new Option(value.lbl, value.sbid, false, false);
            newOpt.setAttribute('data-konversi', value.konv);
            newOpt.setAttribute('data-satuan', value.sb);
            $("#satuan_id_"+_inc).append(newOpt).trigger("change");
        });
        getStok(_inc);
        $("#satuan_id_"+_inc).trigger("change");
        _listIncremnt["new_"+_inc].obatalkes_id = this.value;
    });

    $("#satuan_id_"+_inc).on("change", function(){
        _listIncremnt["new_"+_inc].satuan_id = this.value;
        konversiStok(_inc);
    })

    $("#catatan_"+_inc).on("input", function(){
        _listIncremnt["new_"+_inc].catatan = this.value;
    });

    $("#qty_"+_inc).on("input", function(){
        _listIncremnt["new_"+_inc].qty = this.value;
    });
});

$(document).on("input", ".qty", function(e){
    this.value   = this.value.replace(/^0+(?=\d)/,""); // remove leading zero
    match        = (/(\d{0,9})*((?:\d{0,2})?)/g).exec(this.value.replace(/[^\d]/g, ""));
    this.value   = match[1] + match[2];

    if(this.value == "undefined"){
        this.value = 0;
    }

    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");
    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].qty = parseInt(this.value);
    }
});

$(document).on("input", ".catatan", function(e){
    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");
    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].catatan = this.value;
    }
});

$(document).on("change", ".selectSatuan", function(e){
    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");
    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].satuan_id = this.value;
    }

    var satuan = $("#existing_satuan_id_"+_trId).find(":selected");
    var satuan_text = satuan.attr("data-satuan");
    var konversi = satuan.attr("data-konversi");

    if(_listIncremnt[_trId] != undefined) {
        var stok = _listIncremnt[_trId].stok;
        var stok_konversi = parseInt(stok) / parseInt(konversi);
        if (isNaN(stok_konversi) || stok_konversi < 0) {
            stok_konversi = "0";
        }
        $("#existing_stok_"+_trId).text(stok_konversi+" "+satuan_text);
    }
    konversiStokExisting(_trId);
});

$(document).on("click", ".deleteRow", function(){
    var count_row = Object.keys(_listIncremnt).length;
    if(count_row > 1) {
        var parent = $(this).closest("tr");
        var _dataRow = parseInt(parent.attr("data-row")); // new row
        var _row = parseInt(parent.attr("id")); // existing row
        parent.remove();
        if(!isNaN(_dataRow)) {
            delete _listIncremnt["new_"+_dataRow];
        }
        if(!isNaN(_row)) {
            delete _listIncremnt[_row];
        }
    } else {
        docoNotification('warning', "Peringatan!", "Detail obat tidak boleh kosong");
    }
});

$(document).on("click","#btn-simpan", function (e) {
    e.preventDefault();
    if (!validateForm()) {
        return false;
    } else {
        var _data = $("#pr-edit-form").serializeArray();
        _data.push(
            {
                name : "list_data",
                value : JSON.stringify(_listIncremnt)
            },
            {
                name : "type",
                value : "obat"
            }
        );

        $().docoForm("click",{
            url : $("#pr-edit-form").attr("action"),
            data : _data,
            success : function (data) {
                $('#btn-simpan').prop('disabled', 'disabled');
                setTimeout(function(){
                    window.location.href = "/pengadaan/info-purchase-requisition";
                }, 3000);
            }
        });
    }
});

function getStok(_inc) {
    var oid = $("#obatalkes_id_"+_inc).val();
    if (oid == null) {
        return false;
    }

    $.get('/pengadaan/purchase-requisition/get-stok?oid='+oid, function(data, status){
        var response = data.response;
        _listIncremnt["new_"+_inc].obatalkes_id = oid;
        _listIncremnt["new_"+_inc].stok = response.qty_tersedia;
        _listIncremnt["new_"+_inc].stok_farmasi = response.stok_farmasi;
        _listIncremnt["new_"+_inc].stok_gudang = response.stok_gudang;
        _listIncremnt["new_"+_inc].stok_lain = response.stok_lain;
    }).then(function () {
        konversiStok(_inc);
    });
}

function konversiStok(_inc) {
    var satuan = $("#satuan_id_"+_inc).find(":selected");
    var satuan_text = satuan.attr("data-satuan");
    var konversi = satuan.attr("data-konversi");
    var stok;
    var stok_konversi;
    
    for (var name in _listIncremnt["new_"+_inc]) {
        if (name.indexOf('stok') === 0) {
            stok = _listIncremnt["new_"+_inc][name];
            stok_konversi = parseInt(stok) / parseInt(konversi);
            if (isNaN(stok_konversi) || stok_konversi < 0) {
                stok_konversi = "0";
            }
            if(stok_konversi % 1 != 0) {
                stok_konversi = stok_konversi.toFixed(docoHelper.decimal_places);
            }
            $("#"+name+"_"+_inc).text(stok_konversi+" "+satuan_text);
        }
    }
}

function validateForm() {
    var isValid = true;
    $.each(_listIncremnt, function(key, value){
        if(value.obatalkes_id == null) {
            docoNotification('warning', "Data Tidak Lengkap", "Obat belum terpilih");
            isValid = false;
        }

        if(value.satuan_id == null) {
            docoNotification('warning', "Data Tidak Lengkap", "Satuan belum terpilih");
            isValid = false;
        }

        if(value.qty <= 0){
            docoNotification('warning', "Data Tidak Lengkap", "Qty PR tidak boleh kurang kosong atau dari 0");
            isValid = false;
        }
    });

    return isValid;
}

function konversiStokExisting(_inc) {
    var satuan = $("#existing_satuan_id_"+_inc).find(":selected");
    var satuan_text = satuan.attr("data-satuan");
    var konversi = satuan.attr("data-konversi");
    var stok;
    var stok_konversi;
    
    for (var name in _listIncremnt[_inc]) {
        if (name.indexOf('stok') === 0 || name.indexOf('ext_st_farmasi') === 0 || name.indexOf('ext_st_gudang') === 0 || name.indexOf('ext_st_lain') === 0 || name.indexOf('ext_qty_sugesstion') === 0) {
            stok = _listIncremnt[_inc][name];
            stok_konversi = parseInt(stok) / parseInt(konversi);
            if (isNaN(stok_konversi) || stok_konversi < 0) {
                stok_konversi = "0";
            }
            $("."+name+"_"+_inc).text(stok_konversi+" "+satuan_text);
        }
    }
}