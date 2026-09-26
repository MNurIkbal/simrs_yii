/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

$(".addrow").click(function(){
    var parent = $("#tr-default");
    var _row = parseInt(parent.attr("data-last"));
    var _inc = parseInt(_row) + 1;
    _listIncremnt["new_"+_inc] = {
        id : _row + 1,
        barang_id : null,
        stok: 0,
        qty : 0,
        satuan_id : null,
        catatan : null
    };

    parent.attr("data-last", _inc);

    var barang_id = "<select name=\'InfoPrForm[barang_id]["+_inc+"]\' class=\'form-control input-sm selectObat\' id=\'barang_id"+_inc+"\'><option></option></select>";

    var satuan_id = "<select name=\'InfoPrForm[satuan_id]["+_inc+"]\' class=\'form-control input-sm satuan_id\' data-konversi=\'1\' id=\'satuan_id_"+_inc+"\'></select>";

    var stok = "<span id=\'stok_"+_inc+"\'></span>";

    var qty = "<input type=\'text\' name=\'InfoPrForm[qty]["+_inc+"]\' class=\'form-control qty input-sm text-right\' id=\'qty_"+_inc+"\'>";

    var catatan = "<input type=\'text\' name=\'InfoPrForm[catatan]["+_inc+"]\' class=\'form-control catatan input-sm\' id=\'catatan_"+_inc+"\'>";

    var button_delete = "<button type=\'button\' class=\'deleteRow btn btn-danger btn-custom\'><span class=\'fa fa-trash\'></span></button>";

    var data = "<tr data-row=\'"+ _inc +"\' class=\'child\'>"+
                    "<td>" + _inc + "</td>" +
                    "<td>"+ barang_id +"</td>" +
                    "<td>"+ satuan_id +"</td>" +
                    "<td>"+ stok +"</td>" +
                    "<td>"+ qty +"</td>" +
                    "<td>"+ catatan +"</td>" +
                    "<td>"+ button_delete +"</td>" +
                "</tr>";

    $("#tr-default").before(data);

    $("#satuan_id_"+_inc).select2();
    $("#barang_id"+_inc).select2({
        language: {
            errorLoading: function() { return "Please Wait .." }
        },
        placeholder: "Kode / Nama Barang",
        minimumInputLength: 2,
        ajax: {
            url: '/pengadaan/purchase-requisition/search-item',
            dataType: 'json',
            quietMillis: 250,
            delay: 250,
        },
    });

    $("#barang_id"+_inc).on('select2:select', function(e) {
        if(this.value in _listIncremnt) {
            $("#barang_id"+_inc).val(null).trigger("change");
            docoNotification('warning', "Peringatan!", "Barang sudah ada pada list");
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
        _listIncremnt["new_"+_inc].barang_id = this.value;
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
    var stok = _listIncremnt[_trId].stok;
    var stok_konversi = parseInt(stok) / parseInt(konversi);
    if (isNaN(stok_konversi) || stok_konversi < 0) {
        stok_konversi = "0";
    }
    $("#existing_stok_"+_trId).text(stok_konversi+" "+satuan_text);
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
        docoNotification('warning', "Peringatan!", "Detail Barang tidak boleh kosong");
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
                value : "barang"
            }
        );

        $().docoForm("click",{
            url : $("#pr-edit-form").attr("action"),
            data : _data,
            success : function (data) {
                $('#btn-simpan').prop('disabled', 'disabled');
                setTimeout(function(){
                    window.location.href = "/pengadaan/info-purchase-requisition/barang";
                }, 3000);
            }
        });
    }
});

function getStok(_inc) {
    var oid = $("#barang_id"+_inc).val();
    if (oid == null) {
        return false;
    }

    $.get('/pengadaan/purchase-requisition/get-stok?oid=' + oid + '&type=barang', function(data, status){
        var response = data.response;
        _listIncremnt["new_"+_inc].barang_id = oid;
        _listIncremnt["new_"+_inc].stok = response.qty_tersedia;
    }).then(function () {
        konversiStok(_inc);
    });
}

function konversiStok(_inc) {
    var satuan = $("#satuan_id_"+_inc).find(":selected");
    var satuan_text = satuan.attr("data-satuan");
    var konversi = satuan.attr("data-konversi");
    var stok = _listIncremnt["new_"+_inc].stok;
    var stok_konversi = parseInt(stok) / parseInt(konversi);
    if (isNaN(stok_konversi) || stok_konversi < 0) {
        stok_konversi = "0";
    }
    if(stok_konversi % 1 != 0) {
        stok_konversi = stok_konversi.toFixed(docoHelper.decimal_places);
    }
    $("#stok_"+_inc).text(stok_konversi+" "+satuan_text);
}

function validateForm() {
    var isValid = true;
    $.each(_listIncremnt, function(key, value){
        if(value.barang_id == null) {
            docoNotification('warning', "Data Tidak Lengkap", "Barang belum terpilih");
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
