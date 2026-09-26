/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

$(document).ready(function (){
    if(_isValidasi) {
        $(".deleteRow").attr("disabled", true);
        $(".splitPO").attr("disabled", true);
    } else {
        $(".deleteRow").attr("disabled", false);
        $(".splitPO").attr("disabled", false);
    }

    $(".addrow").click(function(){
        var parent = $("#tr-default");
        var _row = parseInt(parent.attr("data-last"));
        if (instalasi_id == "" || ruangan_id == "") {
            docoNotification("error","Proses Gagal !","Instalasi atau Ruangan harus diisi");
            return false;
        }

        var _inc = parseInt(_row) + 1;
        _listIncremnt["new_"+_inc] = {
            id : _row + 1,
            item_id : null,
            qty : 0,
            satuan_id : null,
            harga : 0,
            discount : 0,
            discount_rp : 0,
            total_harga : 0,
        };

        parent.attr("data-last", _inc);

        var item_id = "<div class=\'form-group\'><div class=\'col-sm-12\'><div class=\'input-group\'><select name=\'PoManualDetailForm[item_id]["+_inc+"]\' class=\'form-control input-sm selectObat\' id=\'item_id_"+_inc+"\'></select></div></div></div>";

        var qty = "<div class=\'form-group\'><div class=\'col-sm-9\'><input type=\'text\' name=\'PoManualDetailForm[qty]["+_inc+"]\' class=\'form-control qty input-sm text-right\' id=\'qty_"+_inc+"\'></div></div>";

        var satuan_id = "<div class=\'form-group\'><div class=\'col-sm-12\'><div class=\'input-group\'><select name=\'PoManualDetailForm[satuan_id]["+_inc+"]\' class=\'form-control input-sm satuan_id selectSatuan\' id=\'satuan_id_"+_inc+"\'></select></div></div></div>";

        var harga = "<div class=\"input-group\"><span class=\"input-group-addon\">Rp.</span><input type=\'text\' name=\'PoManualDetailForm[harga]["+_inc+"]\' class=\'form-control harga input-sm text-right\' id=\'harga_"+_inc+"\'></div>";

        var discount = "<div class=\'form-group\'><div class=\'col-sm-9\'><input type=\'text\' name=\'PoManualDetailForm[discount]["+_inc+"]\' class=\'form-control disc input-sm text-right\' id=\'discount_"+_inc+"\' value=\'0\'></div></div>";

        var button_delete = "<button type=\'button\' class=\'deleteRow btn btn-danger btn-custom\'><span class=\'fa fa-trash\'></span></button>";

        var button_split = "<button type=\'button\' class=\'splitPO btn btn-primary btn-custom\'><span class=\'fa fa-trash\'></span></button>";

        var data = "<tr data-row=\'"+ _inc +"\' class=\'child\'>"+
                        "<td>" + _inc + "</td>" +
                        "<td>"+ item_id +"</td>" +
                        "<td></td>" +
                        "<td>"+qty+"</td>" +
                        "<td>"+satuan_id+"</td>" +
                        "<td>"+harga+"</td>" +
                        "<td>"+discount+"</td>" +
                        "<td class='text-right'><span id=\'diskon_rp_"+_inc+"\' class=\'doco-number\'>0</span></td>" +
                        "<td class='text-right'><span id=\'total_harga_"+_inc+"\'>0</span></td>" +
                        "<td>"+button_delete + button_split+"</td>" +
                    "</tr>";

        $("#tr-default").before(data);

        $(".selectObat").select2({
            placeholder: "Pilih Obat/Barang",
            allowClear: true,
            minimumInputLength: 3,
            ajax : {
                url: baseUrl+"pengadaan/info-purchase-order/search-item",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  params.instalasi_id = instalasi_id;
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

        $("#qty_"+_inc).on("change", function(){
            var _value = parseFloat($(this).val());

            if(isNaN(_value)) {
                this.value = 0;
                _value = 0;
            }

            var _harga = parseFloat(docoHelper.convertToAngka($("#harga_"+_inc).val()));
            var _discount = parseFloat(docoHelper.convertToAngka($("#discount_"+_inc).val()));
            var diskon_rupiah = (_discount / 100) * (_harga * _value);
            var total_harga = (_harga * _value) - parseFloat(diskon_rupiah);

            $("#diskon_rp_"+_inc).html(docoHelper.convertToRupiah(diskon_rupiah));
            $("#total_harga_"+_inc).html(docoHelper.convertToRupiah(total_harga));

            if (typeof _listIncremnt["new_"+_inc] != "undefined") {
                _listIncremnt["new_"+_inc].qty = parseFloat(_value);
                _listIncremnt["new_"+_inc].harga = _harga;
                _listIncremnt["new_"+_inc].discount_rp = diskon_rupiah;
                _listIncremnt["new_"+_inc].total_harga = total_harga;
            }

            hitung();
        });

        $(document).on("input", "#qty_"+_inc, function(e){
            this.value   = this.value.replace(/^0+(?=\d)/,""); // remove leading zero
            match        = (/(\d{0,9})*((?:\d{0,2})?)/g).exec(this.value.replace(/[^\d]/g, ""));
            this.value   = match[1] + match[2];

            if(this.value == "undefined"){
                this.value = 0;
            }
        });

        $("#satuan_id_"+_inc).depdrop({
            depends: ["item_id_"+_inc],
            url: "/pengadaan/info-purchase-order/get-satuan-konversi-item"
        });

        $(".selectSatuan").select2();

        $(document).on("change", "#discount_"+_inc, function (e) {
            var _value = parseFloat(docoHelper.convertToAngka(this.value));
            if(isNaN(_value)) {
                _value = 0;
                this.value = 0;
            }

            var _qty = parseFloat($("#qty_"+_inc).val());
            var harga = parseFloat(docoHelper.convertToAngka($("#harga_"+_inc).val()));
            var diskon_rupiah = (_value / 100) * (harga * _qty);
            var total_harga = (harga * _qty) - parseFloat(diskon_rupiah);

            $("#diskon_rp_"+_inc).html(docoHelper.convertToRupiah(diskon_rupiah));
            $("#total_harga_"+_inc).html(docoHelper.convertToRupiah(total_harga));

            if (typeof _listIncremnt["new_"+_inc] != "undefined") {
                _listIncremnt["new_"+_inc].discount = _value;
                _listIncremnt["new_"+_inc].discount_rp = diskon_rupiah;
                _listIncremnt["new_"+_inc].total_harga = total_harga;
            } else {
                _listIncremnt["new_"+_inc] = {
                    id : _inc,
                    item_id : null,
                    qty : null,
                    satuan_id : null,
                    harga : null,
                    discount : _value,
                    discount_rp : 0,
                    total_harga : 0,
                };
            }
            hitung();
        });

        $(document).on("input", "#discount_"+_inc, function (e) {
            this.value   = this.value.replace(/^0+(?=\d)/,""); // remove leading zero
            match        = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
            this.value   = match[1] + match[2];

            if(this.value > 100){
                this.value = 100;
            }
        });

        $(document).on("input", "#harga_"+_inc, function (e) {
            this.value   = this.value.replace(/^0+(?=\d)/,""); // remove leading zero
            match        = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
            this.value   = match[1] + match[2];
        });

        $(document).on("change", "#harga_"+_inc, function (e) {
            this.value   = docoHelper.convertToAngka(this.value);
            this.value   = docoHelper.convertToRupiah(this.value);

            var _value = parseFloat(docoHelper.convertToAngka(this.value));
            if(isNaN(_value)) {
                this.value = 0;
                _value = 0;
            }

            var _qty = parseFloat($("#qty_"+_inc).val());
            var diskon_rupiah = 0;
            var total_harga = (_value * _qty) - diskon_rupiah;

            $("#diskon_rp_"+_inc).html(docoHelper.convertToRupiah(diskon_rupiah));
            $("#total_harga_"+_inc).html(docoHelper.convertToRupiah(total_harga));

            if (typeof _listIncremnt["new_"+_inc] != "undefined") {
                _listIncremnt["new_"+_inc].harga = _value;
                _listIncremnt["new_"+_inc].discount_rp = diskon_rupiah;
                _listIncremnt["new_"+_inc].total_harga = total_harga;
            } else {
                _listIncremnt["new_"+_inc] = {
                    id : _inc,
                    item_id : null,
                    qty : null,
                    satuan_id : null,
                    harga : _value,
                    discount : 0,
                    discount_rp : 0,
                    total_harga : 0,
                };
            }
            hitung();
        });
    });

    $("#po").on("click", ".deleteRow", function(){
        var parent = $(this).closest("tr");
        var _dataRow = parseInt(parent.attr("data-row")); // new row
        var _row = parent.attr("id"); // existing row

        parent.remove();

        if(!isNaN(_dataRow)) {
            delete _listIncremnt["new_"+_dataRow];
        } else {
            delete _listIncremnt[_row];
        }

        hitung();
    });

    if(status_batal) {
        $("#btn-print").attr("disabled", true);
        $("#btn-print-kop").attr("disabled", true);
        $("#btn-simpan").attr("disabled", true);
        $("#btn-edit-po").attr("disabled", true);
        $(".deleteRow").attr("disabled", true);
        $(".splitPO").attr("disabled", true);
        $(".btn-custom-log").attr("disabled", true);
    }
});

function hitung() {
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
    });

    subTotal = subTotalBeforeDisc - totalDiskon

    if (pajak_value == "") {
        pajak = 0;
        totalPpn = 0;
    } else {
        totalPpn = (pajak/100) * subTotal;
    }

    grandTotal = subTotal + totalPpn;

    $(".sub_total_before_disc").html(docoHelper.convertToRupiah(subTotalBeforeDisc));
    $(".sub_total").html(docoHelper.convertToRupiah(subTotal));
    $(".total_diskon").html(docoHelper.convertToRupiah(totalDiskon));
    $(".ppn").html(docoHelper.convertToRupiah(totalPpn));
    $(".grand_total").html(docoHelper.convertToRupiah(grandTotal));
}

$(document).on("change", ".selectObat ", function (e) {
    var parent = $(this).closest("tr");
    var _value = $(this).val();
    var _row = parseInt(parent.attr("data-row"));

    if(is_valid) {
        is_valid = false;
        return false;
    }

    $.each(_listIncremnt, function(k,v) {
        if(v.item_id == _value) {
            docoNotification("error", "Peringatan", "Data Obat/Barang sudah ada.");
            is_valid = true;
            return false;
        }
    });

    if(is_valid) {
        $(this).val(null).trigger("change");
        return false;
    }

    if (typeof _listIncremnt["new_"+_row] != "undefined") {
        _listIncremnt["new_"+_row].item_id = _value
    } else {
        _listIncremnt["new_"+_row] = {
            id : _row,
            item_id : _value,
            qty : 0,
            satuan_id : null,
            harga : 0,
            discount : 0,
            discount_rp : 0,
            total_harga : 0,
        };
    }
});

$(document).on("change", ".satuan_id ", function (e) {
    var parent = $(this).closest("tr");
    var _value = $(this).val();
    var _row = parseInt(parent.attr("data-row"));

    if (typeof _listIncremnt["new_"+_row] != "undefined") {
        _listIncremnt["new_"+_row].satuan_id = _value
    } else {
        _listIncremnt["new_"+_row] = {
            id : _row,
            item_id : null,
            qty : 0,
            satuan_id : _value,
            harga : 0,
            discount : 0,
            discount_rp : 0,
            total_harga : 0,
        };
    }
});

$(document).on("keyup", ".discount ", function (e) {
    var _valBefore = $(this).val();
    var parent = $(this).closest("tr");
    var _row = parseInt(parent.attr("data-row"));

    match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ""));
    valDisDesimal = match[1] + match[2];

    if(valDisDesimal > 100){
        this.value = 100;
    }else{
        this.value = match[1] + match[2];
    }

    var _value = parseFloat($(this).val());

    if (typeof _listIncremnt["new_"+_row] != "undefined") {
        _listIncremnt["new_"+_row].discount = _value
    } else {
        _listIncremnt["new_"+_row] = {
            id : _row,
            item_id : null,
            qty : null,
            satuan_id : null,
            harga : null,
            discount : _value,
            discount_rp : 0,
            total_harga : 0,
        };
    }

    hitung();
});
