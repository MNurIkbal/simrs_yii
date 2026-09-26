$(document).on("change","#pajak_id",function (e) {
    e.preventDefault();
    _hitung();
});

$(document).on("click","#btn-print", function (e) {
    e.preventDefault();
    var _action = $(this).attr("action");
    window.open(_action);
});

$(document).on("click","#btn-print-kop", function (e) {
    e.preventDefault();
    var _action = $(this).attr("action");
    window.open(_action);
});

$(document).on("click","#btn-simpan", function (e) {
    e.preventDefault();
    var _data = $("#po-form").serializeArray();
    _data.push({
        name : "list_data",
        value : JSON.stringify(_listIncremnt)
    });

    _data.push({
        name : "diorder_oleh",
        value : diorder_oleh
    });

    if(alasan_edit != null) {
        _data.push({
            name : "alasan_edit",
            value : alasan_edit
        });
    }

    $().docoForm("click",{
        url : $("#po-form").attr("action"),
        data : _data,
        success : function (data) {
            location.reload();
        }
    });
});

$(document).on("click","#btn-validasi", function (e) {
    e.preventDefault();
    var _data = $("#po-form").serializeArray();
    _data.push({
        name : "list_data",
        value : JSON.stringify(_listIncremnt)
    });

    _data.push({
        name : "diorder_oleh",
        value : diorder_oleh
    });

    _data.push({
        name : "is_validasi",
        value : true
    });

    $().docoForm("click",{
        url : $("#po-form").attr("action"),
        data : _data,
        success : function (data) {
            location.reload();
        }
    });
});

/** Untuk Tabel **/

$(document).on("change", ".qty", function (e) {
    e.preventDefault();

    var _value = parseFloat($(this).val());

    if(isNaN(_value)) {
        this.value = 0;
        _value = 0;
    }

    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");
    var _prValue = parseFloat(_listQty[_trId].qty);

    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].qty = parseFloat(_value);
    }
    validasiDiskonRp(_trParent, _trId);
    _hitungRow(_trParent, _trId);
});

$(document).on("input", ".qty", function(e){
    this.value   = this.value.replace(/^0+(?=\d)/,""); // remove leading zero
    match        = (/(\d{0,9})*((?:\d{0,2})?)/g).exec(this.value.replace(/[^\d]/g, ""));
    this.value   = match[1] + match[2];

    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");
    var _prValue = parseFloat(_listQty[_trId].qty);
    var _nilaiKonversi = _listQty[_trId].nilai_konversi;
    var _prKonversi = parseFloat(_listQty[_trId].qty * _nilaiKonversi);
    var _itemId = _trId.split("-")[1];
    var _data = _listIncremnt[_trId];
    var currentValConvert = this.value * _hasilKonversi[_itemId][_data.satuan_id];
    if(this.value == "undefined"){
        this.value = 0;
    } 
});

$(document).on("input", ".harga", function(e){
    this.value   = this.value.replace(/^0+(?=\d)/,""); // remove leading zero
    match        = (/(\d+)[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
    this.value   = match[1] + match[2];
});

$(document).on("input", ".disc", function(e){
    this.value   = this.value.replace(/^0+(?=\d)/,""); // remove leading zero
    match        = (/(\d+)[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
    this.value   = match[1] + match[2];

    if(this.value > 100){
        this.value = 100;
    }
});

$(document).on("change", ".harga", function (e) {
    e.preventDefault();

    this.value   = docoHelper.convertToAngka(this.value);
    this.value   = docoHelper.convertToRupiah(this.value);

    var _value = parseFloat(docoHelper.convertToAngka(this.value));
    if (isNaN(_value)) {
        this.value = 0;
        _value = 0;
    }

    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");
    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].harga = parseFloat(_value);
    }
    validasiDiskonRp(_trParent, _trId);
    _hitungRow(_trParent, _trId);
});

$(document).on("change", ".disc", function (e) {
    e.preventDefault();

    var _value = parseFloat(docoHelper.convertToAngka(this.value));
    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");

    if(isNaN(_value)) {
        this.value = 0;
        _value = 0;
    }
    if(Number.parseFloat(this.value) > 100){
        this.value = 100;
    }
    this.value = this.value == '' ? 0 : this.value;

    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].discount = _value;
    }
    _hitungRow(_trParent, _trId);
});

$(document).on("change", ".konversi", function (e) {
    e.preventDefault();
    var _labelMaster = $(this).find(':selected').attr('data-detailkonversi')
    var _idprimary = $(this).attr('data-index')
    var _value = parseFloat($(this).val());
    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");
    var _itemId = _trId.split("-")[1];
    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].satuan_id = parseFloat(_value);
    }
    var _data = _listIncremnt[_trId];
    var _satuan = _data.satuan_id;
    var _resLabelMaster = (_labelMaster == undefined) ? ' - ' : _labelMaster;
    $('.detail-konversi-'+_idprimary).html(_resLabelMaster)

    // ganti harga dasar
    _trParent.find("td.harga_sekarang").html(docoHelper.convertToRupiah(_data.harga_sekarang / (_hasilKonversi[_itemId][_data.satuan_asal] / _hasilKonversi[_itemId][_data.satuan_id])));

    // ganti qty
    var _qtyInput = _data.qty_awal * (_hasilKonversi[_itemId][_data.satuan_asal] / _hasilKonversi[_itemId][_data.satuan_id]);
    if(isNaN(_qtyInput)){
        _qtyInput = _data.qty_awal;
    }
    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].qty = parseFloat(_qtyInput);
    }

    _trParent.find("input.qty").val(_qtyInput);

    // ganti harga
    var _harga = _data.harga_awal / (_hasilKonversi[_itemId][_data.satuan_asal] / _hasilKonversi[_itemId][_data.satuan_id]);

    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].harga = parseFloat(_harga);
    }

    _trParent.find("input.harga").val(docoHelper.convertToRupiah(_harga));

    _hitungRow(_trParent, _trId);
});

var _hitungRow = function (_row, id) {
    if (typeof _listIncremnt[id] != "undefined") {
        var _data = _listIncremnt[id];
        var _satuan = _data.satuan_id;
        var _itemId = id.split("-")[1];
        if (typeof _hasilKonversi[_itemId][_satuan] != "undefined") {
            _listIncremnt[id].nilai_konversi = parseFloat(_data.qty) * parseFloat(_hasilKonversi[_itemId][_satuan]);
        }
        _listIncremnt[id].total_harga = (_data.qty * _data.harga);
        isDiskonRp(_row, id);
        // _listIncremnt[id].discount_rp = ((_data.discount / 100) * _listIncremnt[id].total_harga);
        _listIncremnt[id].subtotal_after_disc = _listIncremnt[id].total_harga - _listIncremnt[id].discount_rp;
        // _listIncremnt[id].total_harga -= _listIncremnt[id].discount_rp;
        // _row.find("td.disc-rp").html(docoHelper.convertToRupiah(_listIncremnt[id].discount_rp));
        _row.find("td.subtotal-after-disc").html(docoHelper.convertToRupiah(_listIncremnt[id].subtotal_after_disc));
        _row.find("td.jumlah").html(docoHelper.convertToRupiah(_listIncremnt[id].total_harga));
    }

    _hitung();
}

var _hitung = function () {
    var subTotal = 0;
    var subTotalBeforeDisc = 0;
    var totalDiskon = 0;
    var totalPpn = 0;
    var grandTotal = 0;
    var pajak_value = $("#pajak_id").val();
    var pajak =  typeof _mapPajak[pajak_value] != "undefined" ? _mapPajak[pajak_value] : 0;

    $.each(_listIncremnt, function(key,val){
        var totHar = isNaN(val.total_harga) ? 0 : val.total_harga;
        var totDis = isNaN(val.discount_rp) ? 0 : val.discount_rp;
        subTotalBeforeDisc += totHar;
        totalDiskon += totDis;
        // totalDiskon += val.harga * val.qty * val.discount / 100;
    });

    subTotal = subTotalBeforeDisc - totalDiskon

    if (pajak_value == "") {
        pajak = 0;
        totalPpn = 0;
    } else {
        // totalPpn = (pajak/100) * subTotal;
        totalPpn = (subTotal * pajak) / 100;
        totalPpn = parseFloat(totalPpn.toFixed(2));
    }

    grandTotal = subTotal + totalPpn;

    $(".sub_total_before_disc").html(docoHelper.convertToRupiah(subTotalBeforeDisc));
    $(".sub_total").html(docoHelper.convertToRupiah(subTotal));
    $(".total_diskon").html(docoHelper.convertToRupiah(totalDiskon));
    $(".ppn").html(docoHelper.convertToRupiah(totalPpn));
    $(".grand_total").html(docoHelper.convertToRupiah(grandTotal));
}

/** Untuk Tabel **/

$(function(){
    _isCheckLoad();
    $(".doco-number").trigger("keyup");
    $(".konversi").trigger("change");
    if (_status) {
        $("select,input,textarea").prop("disabled",true)
    }
    $("#peg_menyetujui_id").select2({
        placeholder: "Pilih Pegawai",
        minimumInputLength: 3,
        ajax : {
            url: baseUrl+"pengadaan/info-purchase-order/search-pegawai",
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
                });
              return {
                results: data.result
              };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });
    $("#peg_mengetahui_id").select2({
        placeholder: "Pilih Pegawai",
        minimumInputLength: 3,
        ajax : {
            url: baseUrl+"pengadaan/info-purchase-order/search-pegawai",
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
                });
              return {
                results: data.result
              };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });
});

var _hitungRowLoad = function (_row, id) {
    if (typeof _listIncremnt[id] != "undefined") {
        var _data = _listIncremnt[id];
        var _satuan = _data.satuan_id;
        var _itemId = id.split("-")[1];
        if (typeof _hasilKonversi[_itemId][_satuan] != "undefined") {
            _listIncremnt[id].nilai_konversi = parseFloat(_data.qty) * parseFloat(_hasilKonversi[_itemId][_satuan]);
        }
        _listIncremnt[id].total_harga = (_data.qty * _data.harga);
        // _listIncremnt[id].discount_rp = (_listIncremnt[id].total_harga * (_data.discount / 100));
        // _listIncremnt[id].discount_rp = ((_data.discount / 100) * _listIncremnt[id].total_harga);
        _listIncremnt[id].subtotal_after_disc = _listIncremnt[id].total_harga - _listIncremnt[id].discount_rp;
        _row.find("td.disc-rp").html(docoHelper.convertToRupiah(_listIncremnt[id].discount_rp));
        _row.find("td.subtotal-after-disc").html(docoHelper.convertToRupiah(_listIncremnt[id].subtotal_after_disc));
        _row.find("td.jumlah").html(docoHelper.convertToRupiah(_listIncremnt[id].total_harga));
    }

    _hitung();
}

var _isCheckLoad = function () {
    $(".discount_rp_check").each(function(i, val){
        var _trParent = $(this).closest("tr");
        var _trId = _trParent.attr("id");
        if ($(this).val() != "") {
            $(this).prop('checked', true);
            _trParent.find('.diskon_rp').prop('readonly', false)
            _trParent.find('.disc').prop('disabled', true)
        } else {
            $(this).prop('checked', false);
            _trParent.find('.diskon_rp').prop('readonly', true)
            _trParent.find('.disc').prop('disabled', false)
        }
    });
}

$(document).on("change", ".discount_rp_check", function(e) {
    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");

    if ($(this).is(':checked')) {
        _trParent.find('.diskon_rp').prop('readonly', false)
        _trParent.find('.disc').prop('disabled', true)
        if (typeof _listIncremnt[_trId] != "undefined") {
            _listIncremnt[_trId].is_disc_nominal = true;
        }
    } else {
        _trParent.find('.diskon_rp').prop('readonly', true)
        _trParent.find('.disc').prop('disabled', false)
        if (typeof _listIncremnt[_trId] != "undefined") {
            _listIncremnt[_trId].is_disc_nominal = false;
        }
    }
})

$(document).on("change", ".diskon_rp", function (e) {
    e.preventDefault();

    this.value   = docoHelper.convertToAngka(this.value);
    this.value   = docoHelper.convertToRupiah(this.value);

    var _value = parseFloat(docoHelper.convertToAngka(this.value));
    if (isNaN(_value)) {
        this.value = 0;
        _value = 0;
    }

    var _trParent = $(this).closest("tr");
    var _trId = _trParent.attr("id");
    var _data = _listIncremnt[_trId];
    var total_harga = (_data.qty * _data.harga);
    if (docoHelper.convertToAngka(this.value) > total_harga) {
        docoNotification("error", "Peringatan", "Discount(Rp) tidak boleh lebih besar dari Jumlah");
        this.value = 0;
        _value = 0;
    }

    if (typeof _listIncremnt[_trId] != "undefined") {
        _listIncremnt[_trId].discount_rp = parseFloat(_value);
    }
    _hitungRow(_trParent, _trId);
});

function isDiskonRp(_row, id) {
    var chek = _row.find("td input[type=checkbox]");
    var _data = _listIncremnt[id];
    
    if (chek.is(':checked')) {
        var discount = (_listIncremnt[id].discount_rp * 100 ) / _data.harga / _data.qty;
        discount = isNaN(discount) ? 0 : discount;
        _listIncremnt[id].discount= discount;
        _row.find("td").find('.disc').val(discount)
    } else {
        _listIncremnt[id].discount_rp = ((_data.discount / 100) * _listIncremnt[id].total_harga);
        _row.find('td').find(".diskon_rp").val(docoHelper.convertToRupiah(_listIncremnt[id].discount_rp));
    }
}

function validasiDiskonRp(_row, id) {
    var chek = _row.find("td input[type=checkbox]");
    if (chek.is(':checked')) {
        _row.find('td').find(".diskon_rp").trigger("change");
    } else {
        _row.find('td').find(".disc").trigger("change");
    }
}