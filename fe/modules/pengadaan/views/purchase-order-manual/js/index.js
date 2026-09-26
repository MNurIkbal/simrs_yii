var _listIncremnt = {};
var instalasi_id = "";
var ruangan_id = "";
var is_valid = false;
var pajak_id = "";
var setConsignment = false;

$("#instalasi_id").on("change", function (event) {
    event.preventDefault();
    instalasi_id = $(this).val();

    if(instalasi_id == 66) {
        //obatalkes
        $(".checkbox-consignment").show();
        $(".kode-warna").show();
    }
    else {
        //barang
        $(".checkbox-consignment").hide();
        $(".kode-warna").hide();

        setConsignment = false;
        $("#pomanualform-is_consigment").prop("checked",false);
    }
});

$("#ruangan_id").on("change", function (event) {
    event.preventDefault();
    ruangan_id = $(this).val();
});

$(document).ready(function() {
    $(".qty").trigger("keyup");
    $(".harga").trigger("keyup");
    $(".discount").trigger("keyup");
    $(".diskon_rp").trigger("change");
    $(".total_harga").trigger("change");
    $(".sub_total").html(0);
    $(".ppn").html(0);
    $(".grand_total").html(0);

    $(".selectSupplier").select2({
        placeholder: "Pilih Supplier",
        minimumInputLength: 3,
        ajax : {
            url: baseUrl+"pengadaan/purchase-order-manual/search-supplier",
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

    $("#pajak_id").select2();

    $("#supplier_id").on("select2:select", function(e) {
        pajak_id = e.params.data.pajak_id == null ? "" : e.params.data.pajak_id;
        $("#pajak_id").val(pajak_id).trigger("change");
    });

    $(".selectPegawai").select2({
        placeholder: "Pilih Pegawai",
        minimumInputLength: 3,
        ajax : {
            url: baseUrl+"pengadaan/purchase-order-manual/search-pegawai",
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

    $(".selectObat").select2();
    $(".addrow").click(function(){
        var parent = $("#tr-default");
        var _row = parseInt(parent.attr("data-last"));
        if (instalasi_id == "" || ruangan_id == "") {
            docoNotification("error","Proses Gagal !","Instalasi atau Ruangan harus diisi");
            return false;
        }

        var _inc = parseInt(_row) + 1;
        if (typeof _listIncremnt[_inc] == "undefined") {
            _listIncremnt[_inc] = {
                id : _row + 1,
                item_id : null,
                qty : 0,
                satuan_id : null,
                harga : 0,
                discount : 0,
                discount_rp : 0,
                total_harga : 0,
                is_disc_nominal : false,
            };
        }

        if (Object.keys(_listIncremnt).length > 0) {
            $("#ruangan_id, #instalasi_id, #pomanualform-is_consigment").prop("disabled",true);
        }

        parent.attr("data-last", _inc);
        // parent.hide();
        var item_id = "<div class=\'form-group\'><div class=\'col-sm-12\'><div class=\'input-group\'><select name=\'PoManualDetailForm[item_id]["+_inc+"]\' class=\'form-control input-sm selectObat\' id=\'item_id_"+_inc+"\'></select><span class=\'input-group-btn\'><button type=\'button\' class=\'btn btn-info add-item btn-custom\' title=\'Tambah Barang/Obat\'><i class=\'fa fa-plus\'></i></button></span></div></div></div>";

        var qty = "<div class=\'form-group\'><div class=\'col-sm-9\'><input type=\'text\' name=\'PoManualDetailForm[qty]["+_inc+"]\' class=\'form-control qty input-sm doco-number text-right\' id=\'qty_"+_inc+"\'></div></div>";

        var satuan_id = "<div class=\'form-group\'><div class=\'col-sm-12\'><div class=\'input-group\'><select name=\'PoManualDetailForm[satuan_id]["+_inc+"]\' class=\'form-control input-sm satuan_id selectSatuan\' id=\'satuan_id_"+_inc+"\'></select><span class=\'input-group-btn\'><button type=\'button\' class=\'btn btn-info add-satuan btn-custom\' title=\'Tambah Satuan\'><i class=\'fa fa-plus\'></i></button></span></div></div></div>";

        var harga = "<div class=\"input-group\"><span class=\"input-group-addon\">Rp.</span><input type=\'text\' name=\'PoManualDetailForm[harga]["+_inc+"]\' class=\'form-control harga input-sm doco-number-decimal text-right\' id=\'harga_"+_inc+"\'></div>";

        var discount = "<div class=\'form-group\'><div class=\'col-sm-9\'><input type=\'text\' name=\'PoManualDetailForm[discount]["+_inc+"]\' class=\'form-control discount input-sm text-right\' id=\'discount_"+_inc+"\' value=\'0\'></div></div>";

        var check = "<label><input type=\'checkbox\' id=\'discount_rp_check_"+_inc+"\' name=\'PoManualDetailForm[is_disc_nominal]["+_inc+"]\' value=\"1\"></label>"
        var discount_rp = "<div class=\"input-group\"><span class=\"input-group-addon\">"+check+"</span><input type=\'text\' name=\'PoManualDetailForm[discount_rp]["+_inc+"]\' class=\'form-control diskon_rp input-sm doco-number-decimal text-right\' id=\'diskon_rp_"+_inc+"\' value=\'0\' readonly=\'true\'></div>";

        var button_delete = "<button type=\'button\' class=\'deleteRow btn btn-danger btn-custom\'><span class=\'fa fa-trash\'></span></button>";

        var data = "<tr data-row=\'"+ _inc +"\' id=\'row-"+ _inc +"\' class=\'child\'><td>"+item_id+"</td><td>"+qty+"</td>" +
                   "<td>"+satuan_id+"</td>"+
                   "<td><span id=\'satuan_kecil"+_inc+"\'></span></td>" +
                   "<td>"+harga+"</td><td>"+discount+"</td><td>"+discount_rp+"</td><td><span id=\'total_harga_"+_inc+"\'>0</span></td><td>"+button_delete+"</td></tr>";

        $("#tr-default").before(data);

        $(".selectObat").select2({
            placeholder: "Pilih Obat/Barang",
            allowClear: true,
            minimumInputLength: 3,
            ajax : {
                url: baseUrl+"pengadaan/purchase-order-manual/search-item",
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

        $(".selectObat").on("select2:select", function(e) {
            if(e.params.data.is_consigment != setConsignment && instalasi_id == 66) {
                $("tr#row-"+_inc).addClass("consignment");
            }
        });

        $("#satuan_id_"+_inc).depdrop({
            depends: ["item_id_"+_inc],
            url: "/pengadaan/purchase-order-manual/get-satuan-konversi-item"
        });

        $(".selectSatuan").select2();

        $(document).on("change", "#satuan_id_"+_inc, function(e) {
            var satuan_kecil = $(this).children('option:selected').data('satuan_kecil');
            var nilai_konversi = $(this).children('option:selected').data('nilai_konversi');
            var _qty = docoHelper.convertToAngka($("#qty_"+_inc).val());
            nilai_konversi = isNaN(nilai_konversi) ? 0 : nilai_konversi;
            _qty = isNaN(_qty) ? 0 : _qty;
            satuan_kecil = typeof satuan_kecil === "undefined" ? "" : satuan_kecil;

            var jlm_satuan_kecil = (nilai_konversi * _qty);
            var valueName = $(this).find('option:selected').text();
            var konversi = '1 '+valueName+' = '+nilai_konversi+' '+satuan_kecil;
            $("#satuan_kecil"+_inc).html(konversi);
        });

        $("#discount_rp_check_"+_inc).on("change", function(){
            if ($(this).is(':checked')) {
                $("#diskon_rp_"+_inc).prop("readonly", false);
                $("#discount_"+_inc).prop("disabled", true);
            } else {
                $("#diskon_rp_"+_inc).prop("readonly", true);
                $("#discount_"+_inc).prop("disabled", false);
            }
            if (typeof _listIncremnt[_inc] != "undefined") {
                _listIncremnt[_inc].is_disc_nominal = $(this).is(':checked');
            }
        });

        $(document).on("change", "#discount_"+_inc, function (e) {
            var _value = docoHelper.convertToDecimal(this.value);
            // var _value = docoHelper.convertToAngka(this.value);
            if(isNaN(_value)) {
                _value = 0;
                this.value = 0;
            }

            var _qty = docoHelper.convertToAngka($("#qty_"+_inc).val());
            var harga = docoHelper.convertToAngka($("#harga_"+_inc).val());
            var diskon_rupiah = (_value / 100) * (harga * _qty);
            var total_harga = (harga * _qty) - diskon_rupiah;

            $("#diskon_rp_"+_inc).val(docoHelper.convertToRupiah(diskon_rupiah));
            $("#total_harga_"+_inc).html(docoHelper.convertToRupiah(total_harga));

            if (typeof _listIncremnt[_inc] != "undefined") {
                _listIncremnt[_inc].discount = _value;
                _listIncremnt[_inc].discount_rp = diskon_rupiah;
                _listIncremnt[_inc].total_harga = total_harga;
            } else {
                _listIncremnt[_inc] = {
                    id : _inc,
                    item_id : null,
                    qty : 0,
                    satuan_id : null,
                    harga : 0,
                    discount : _value,
                    discount_rp : 0,
                    total_harga : 0,
                };
            }
            hitung();
        })

        $(document).on("change", "#harga_"+_inc, function (e) {
            /*
            var _value = docoHelper.convertToAngka($(this).val());
            var _diskon = docoHelper.convertToAngka($("#discount_"+_inc).val());
            var _qty = docoHelper.convertToAngka($("#qty_"+_inc).val());
            var diskon_rupiah = (_diskon / 100) * (_value * _qty);
            var total_harga = (_value * _qty) - diskon_rupiah;

            $("#diskon_rp_"+_inc).val(docoHelper.convertToRupiah(diskon_rupiah));
            $("#total_harga_"+_inc).html(docoHelper.convertToRupiah(total_harga));

            if (typeof _listIncremnt[_inc] != "undefined") {
                _listIncremnt[_inc].harga = _value;
                _listIncremnt[_inc].discount_rp = docoHelper.convertToRupiah(diskon_rupiah);
                _listIncremnt[_inc].total_harga = docoHelper.convertToRupiah(total_harga);
            } else {
                _listIncremnt[_inc] = {
                    id : _inc,
                    item_id : null,
                    qty : 0,
                    satuan_id : null,
                    harga : _value,
                    discount : 0,
                    discount_rp : 0,
                    total_harga : 0,
                };
            }
            hitung();
            $("#discount_"+_inc).trigger("keyup");
            */
            isDiskonRp(this.id, _inc);
        })

        $(document).on("change", "#qty_"+_inc, function (e) {
            /*
            var _value = docoHelper.convertToAngka($(this).val());
            var _diskon = docoHelper.convertToAngka($("#discount_"+_inc).val());
            var _harga = docoHelper.convertToAngka($("#harga_"+_inc).val());
            var diskon_rupiah = (_diskon / 100) * (_harga * _value);
            var total_harga = (_value * _harga) - diskon_rupiah;

            $("#diskon_rp_"+_inc).val(docoHelper.convertToRupiah(diskon_rupiah));
            $("#total_harga_"+_inc).html(docoHelper.convertToRupiah(total_harga));

            if (typeof _listIncremnt[_inc] != "undefined") {
                _listIncremnt[_inc].qty = _value;
                _listIncremnt[_inc].discount_rp = docoHelper.convertToRupiah(diskon_rupiah);
                _listIncremnt[_inc].total_harga = docoHelper.convertToRupiah(total_harga);
            } else {
                _listIncremnt[_inc] = {
                    id : _inc,
                    item_id : null,
                    qty : _value,
                    satuan_id : null,
                    harga : 0,
                    discount : 0,
                    discount_rp : 0,
                    total_harga : 0,
                };
            }
            hitung();
            $("#discount_"+_inc).trigger("keyup");
            */
            isDiskonRp(this.id, _inc);
            $("#satuan_id_"+_inc).trigger('change');
        })

        $(document).on("change", "#diskon_rp_"+_inc, function (e) {
            var _value = docoHelper.convertToAngka($(this).val());
            if(isNaN(_value)) {
                _value = 0;
                this.value = 0;
            }

            var _qty = docoHelper.convertToAngka($("#qty_"+_inc).val());
            var harga = docoHelper.convertToAngka($("#harga_"+_inc).val());
            var discount = (_value * 100 ) / harga / _qty;
            discount = isNaN(discount) ? 0 : discount;
            // var discount = discount.toString().replace(".", ",");
            var total_harga = (harga * _qty) - _value;
            
            $("#discount_"+_inc).val(discount);
            $("#total_harga_"+_inc).html(docoHelper.convertToRupiah(total_harga));

            if (typeof _listIncremnt[_inc] != "undefined") {
                _listIncremnt[_inc].discount = discount;
                _listIncremnt[_inc].discount_rp = parseFloat(_value);
                _listIncremnt[_inc].total_harga = total_harga;
            } else {
                _listIncremnt[_inc] = {
                    id : _inc,
                    item_id : null,
                    qty : 0,
                    satuan_id : null,
                    harga : 0,
                    discount : 0,
                    discount_rp : _value,
                    total_harga : 0,
                };
            }
            hitung();
            //$("#discount_"+_inc).trigger("keyup");
        })

    });

    $("#po").on("click", ".deleteRow",function(){
        var parent = $(this).closest("tr");
        var _row = parseInt(parent.attr("data-row"));
        parent.remove();
        if (typeof _listIncremnt[_row] != "undefined") {
            delete _listIncremnt[_row]
        }
        if (Object.keys(_listIncremnt).length <= 0) {
            $("#ruangan_id, #instalasi_id, #pomanualform-is_consigment").prop("disabled",false);
        }
        hitung();
    });

    $("#pomanualform-is_consigment").on("change", function() {
        setConsignment = $(this).prop("checked");

        if(setConsignment) {
            $("div.legend-information__text").text("Item Bukan Consignment");
        } else {
            $("div.legend-information__text").text("Item Consignment");
        }

    });
});

function hitung() {
    var subTotal = 0;
    var totalDiskon = 0;
    var totalPpn = 0;
    var grandTotal = 0;
    var pajak_value = $("#pajak_id").val();
    var pajak =  typeof _mapPajak[pajak_value] != "undefined" ? _mapPajak[pajak_value] : 0;

    $.each(_listIncremnt, function(key,val){
        var totHar = isNaN(val.total_harga) ? 0 : val.total_harga;
        var totDis = isNaN(val.discount_rp) ? 0 : val.discount_rp;
        subTotal += totHar;
        totalDiskon += totDis;
    });

    if (pajak_value == "") {
        pajak = 0;
        totalPpn = 0;
    } else {
        // totalPpn = (pajak/100) * subTotal;
        totalPpn = (subTotal * pajak) / 100;
        totalPpn = parseFloat(totalPpn.toFixed(2));
    }

    grandTotal = subTotal + totalPpn;

    $(".sub_total").html(docoHelper.convertToRupiah(subTotal));
    $(".total_diskon").html(docoHelper.convertToRupiah(totalDiskon));
    $(".ppn").html(docoHelper.convertToRupiah(totalPpn));
    $(".grand_total").html(docoHelper.convertToRupiah(grandTotal));
}

$(document).on("change", ".selectObat ", function (e) {
    var parent = $(this).closest("tr");
    var _value = $(this).val();
    var _row = parseInt(parent.attr("data-row"));
    parent.find("#satuan_kecil"+_row).html('');

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

    if (typeof _listIncremnt[_row] != "undefined") {
        _listIncremnt[_row].item_id = _value
    } else {
        _listIncremnt[_row] = {
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
})

$(document).on("change", ".satuan_id ", function (e) {
    var parent = $(this).closest("tr");
    var _value = $(this).val();
    var _row = parseInt(parent.attr("data-row"));

    if (typeof _listIncremnt[_row] != "undefined") {
        _listIncremnt[_row].satuan_id = _value
    } else {
        _listIncremnt[_row] = {
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
})

$(document).on("change", "#pajak_id", function(){
    //$(".discount").trigger("keyup");
    hitung();
});

$(document).on("keyup", ".qty ", function (e) {
    var parent = $(this).closest("tr");
    var _value = docoHelper.convertToAngka($(this).val());
    var _row = parseInt(parent.attr("data-row"));
    if (typeof _listIncremnt[_row] != "undefined") {
        _listIncremnt[_row].qty = _value
    } else {
        _listIncremnt[_row] = {
            id : _row,
            item_id : null,
            qty : _value,
            satuan_id : null,
            harga : 0,
            discount : 0,
            discount_rp : 0,
            total_harga : 0,
        };
    }
    hitung();
    //$(".discount").trigger("keyup");
})

$(document).on("keyup", ".harga ", function (e) {
    var parent = $(this).closest("tr");
    var _value = docoHelper.convertToAngka($(this).val());
    var _row = parseInt(parent.attr("data-row"));
    if (typeof _listIncremnt[_row] != "undefined") {
        _listIncremnt[_row].harga = _value
    } else {
        _listIncremnt[_row] = {
            id : _row,
            item_id : null,
            qty : 0,
            satuan_id : null,
            harga : _value,
            discount : 0,
            discount_rp : 0,
            total_harga : 0,
        };
    }
    $(this).closest("td").find("span.help-block").remove();
    hitung();
    //$(".discount").trigger("keyup");
})

$(document).on("keyup", ".diskon_rp", function (e) {
    var parent = $(this).closest("tr");
    var _row = parseInt(parent.attr("data-row"));
    var _qty = docoHelper.convertToAngka(parent.find("#qty_"+_row).val());
    var harga = docoHelper.convertToAngka(parent.find("#harga_"+_row).val());
    var total_harga = (harga * _qty);

    if (docoHelper.convertToAngka(this.value) > total_harga) {
        docoNotification("error", "Peringatan", "Discount(Rp) tidak boleh lebih besar dari Jumlah");
        this.value = 0;
    }   
    var _value = docoHelper.convertToAngka(this.value);
    if (typeof _listIncremnt[_row] != "undefined") {
        _listIncremnt[_row].discount_rp = parseFloat(_value)
    } else {
        _listIncremnt[_row] = {
            id : _row,
            item_id : null,
            qty : 0,
            satuan_id : null,
            harga : 0,
            discount : 0,
            discount_rp : _value,
            total_harga : 0,
        };
    }
    $(this).closest("td").find("span.help-block").remove();
    hitung();
    //$(".discount").trigger("keyup");
})

$(document).on("keyup", ".discount ", function (e) {
    this.value   = this.value.replace(/^0+(?=\d)/,""); // remove leading zero
    match        = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
    this.value   = match[1] + match[2];

    if(Number.parseFloat(this.value) > 100){
        this.value = 100;
    }
    this.value = this.value == '' ? 0 : this.value;
    var _value = docoHelper.convertToAngka(this.value);
    var parent = $(this).closest("tr");
    var _row = parseInt(parent.attr("data-row"));

    if (typeof _listIncremnt[_row] != "undefined") {
        _listIncremnt[_row].discount = _value
    } else {
        _listIncremnt[_row] = {
            id : _row,
            item_id : null,
            qty : 0,
            satuan_id : null,
            harga : 0,
            discount : _value,
            discount_rp : 0,
            total_harga : 0,
        };
    }

    hitung();
})

$(document).on("keyup", "#discount_header", function (e) {
    var parent = $(this).closest("tr");
    var _row = parseInt(parent.attr("data-row"));
    var _qty = docoHelper.convertToAngka($(".qty").val());
    var _value = docoHelper.convertToAngka($(this).val());
    var harga = docoHelper.convertToAngka($(".harga").val());
    var sub_total = $(".sub_total").html();
    var diskon_rupiah = (_value / 100) * harga;
    var total_harga = (harga * _qty) - diskon_rupiah;

    var pajak = $("#pajak_id option:selected").text();
    var ppn = (pajak/ 100) * total_harga;
    var grand_total = total_harga + ppn;

    $(".diskon_rupiah").html(docoHelper.convertToRupiah(diskon_rupiah));
    $(".total_harga").html(docoHelper.convertToRupiah(total_harga));
    $(".sub_total").html(docoHelper.convertToRupiah(total_harga));
    $(".total_diskon").html(docoHelper.convertToRupiah(diskon_rupiah));
    $(".ppn").html(docoHelper.convertToRupiah(ppn));
    $(".grand_total").html(docoHelper.convertToRupiah(grand_total));

    if (typeof _listIncremnt[_row] != "undefined") {
        _listIncremnt[_row].discount = _value;
        _listIncremnt[_row].discount_rp = diskon_rupiah;
        _listIncremnt[_row].total_harga = total_harga;
    } else {
        _listIncremnt[_row] = {
            id : _row,
            item_id : null,
            qty : 0,
            satuan_id : null,
            harga : 0,
            discount : _value,
            discount_rp : 0,
            total_harga : 0,
        };
    }
})

$("#btn-simpan").on("click", function() {
    var consignmentLength = $("tr.consignment").length;

    if(setConsignment) {
        message = "Terdapat Item Bukan Consignment!";
    } else {
        message = "Terdapat Item Consignment!";
    }

    
    if(consignmentLength > 0 && instalasi_id == 66) {
        return new PNotify({
            title: "Terjadi Kesalahan",
            text: message,
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });
    } else {
        $(this).docoForm("click", {
            url : baseUrl+"pengadaan/purchase-order-manual/simpan",
            method : "POST",
            type : "json",
            data: {
                data : _listIncremnt,
                instalasi_id: $("#instalasi_id").val(),
                ruangan_id: $("#ruangan_id").val(),
                supplier_id: $("#supplier_id").val(),
                tgl_rencanaterima: $("#pomanualform-tgl_rencanaterima").val(),
                peg_menyetujui_id: $("#peg_menyetujui_id").val(),
                peg_mengetahui_id: $("#peg_mengetahui_id").val(),
                payterm_id: $("#payterm_id").val(),
                pajak_id: $("#pajak_id").val(),
                catatan1: $("#pomanualform-catatan1").val(),
                catatan2: $("#pomanualform-catatan2").val(),
                is_consignment: setConsignment,
            },
            success : function (data) {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); 
                var yyyy = today.getFullYear();
                
                var monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
                                  "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
                
                var monthName = monthNames[today.getMonth()];
                
                today = dd + '-' + monthName + '-' + yyyy;
                

                var no_pomanual = data.response.no_pomanual;
                var _orderOleh = $("#pomanualform-diorder_oleh").val();
                docoResetForm($("#po-form"));
                _listIncremnt = {};
                setConsignment = false;
                $("#pomanualform-diorder_oleh").val(_orderOleh);
                $(".child").remove();
                $(".sub_total").html(0);
                $(".ppn").html(0);
                $(".total_diskon").html(0);
                $(".grand_total").html(0);
                $(".total_harga").html(0);
                $(".diskon_rupiah").html(0);
                $("#ruangan_id, #instalasi_id, #pomanualform-is_consigment").prop("disabled",false);
                $("#pomanualform-is_consigment").prop("checked",false);
                $("#tgl_pomanual").prop("value",today);

                (new PNotify({
                    title: "Berhasil",
                    text: "PO Manual dengan Nomor " + "<strong>" + no_pomanual + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                                text: "Ya",
                                addClass: "btn btn-xs btn-success",
                            },
                            {
                                text: "Tidak",
                                addClass: "btn btn-xs btn-danger",
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on("pnotify.confirm", function() {
                    window.open("/pengadaan/info-purchase-order/cetak-rincian?id="+data.response.no_transaksi+"&type_po="+data.response.type+"&no_transaksi="+data.response.no_pomanual);
                }).on("pnotify.cancel", function() {
                });
            },
            error : function (data) {
                docoNotification("error", i18next.t(data.responseJSON.message), i18next.t("Terjadi kesalahan pada sistem"));
            }
        })
    }
});

$("#add-supplier").on("click", function(){
    window.open(baseUrl+"master/supplier/create");
});

$(".add-item").on("click", function(){
    if(instalasi_id == "") {
        docoNotification("warning", "Info", "Maaf, Pilih Instalasi Terlebih Dahulu.");
    }
    else {
        if(instalasi_id == 66) {
            window.open(baseUrl+"master/obat-alkes/create");
        }
        else {
            window.open(baseUrl+"master/barang/create");
        }
    }
});

$(".add-satuan").on("click", function(){
    if(instalasi_id == "") {
        docoNotification("warning", "Info", "Maaf, Pilih Instalasi Terlebih Dahulu.");
    }
    else {
        if(instalasi_id == 15) {
            window.open(baseUrl+"master/satuan-konversi-barang/create");
        }
        else {
            window.open(baseUrl+"master/satuan-konversi/index");
        }
    }
});

$(document).on("keyup", ".doco-number-decimal", function(e){
      match        = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
      valDisDesimal = match[1] + match[2];
      this.value = valDisDesimal;
  });

$(document).on("keyup", ".doco-number-decimal", function(e) {
    var angka = $(this).val();
    var number_string = angka.toString().toString().replace(/\./g, ","),
        split = number_string.split(","),
        absvalue = split[0];
    var _split = split[0].replace(/\-/g, "");
    var sisa = _split.length % 3,
        rupiah = _split.substr(0, sisa),
        ribuan = _split.substr(sisa).match(/\d{1,3}/gi);
    var simbol = absvalue.match(/\-/gm);
    simbol = simbol == null ? "" : simbol;

    if (ribuan) {
        separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }

    var _value = simbol + (split[1] != undefined ? rupiah + "," + split[1] : rupiah);

    if (_value == "NaN") {
        _value = 0;
    }
    $(this).val(_value);
});


function isDiskonRp (inputBy, _inc) {
    var isCheck = $("#discount_rp_check_"+_inc).is(":checked");
    var _qty = docoHelper.convertToAngka($("#qty_"+_inc).val());
    var _harga = docoHelper.convertToAngka($("#harga_"+_inc).val());
    _harga = isNaN(_harga) ? 0 : _harga;
    var _diskon = docoHelper.convertToAngka($("#discount_"+_inc).val());
    var _diskonRp = docoHelper.convertToAngka($("#diskon_rp_"+_inc).val());

    var discount = 0;
    var diskon_rupiah = 0;
    var total_harga = 0;

    if (isCheck) {
        var discount = (_diskonRp * 100 ) / _harga / _qty;
        discount = discount.toString().replace(".", ",");
        total_harga = (_harga * _qty) - _diskonRp;
        $("#discount_"+_inc).val(discount);
        $("#total_harga_"+_inc).html(docoHelper.convertToRupiah(total_harga));
    } else {
        diskon_rupiah = (_diskon / 100) * (_harga * _qty);
        total_harga = (_qty * _harga) - diskon_rupiah;
        $("#diskon_rp_"+_inc).val(docoHelper.convertToRupiah(diskon_rupiah));
        $("#total_harga_"+_inc).html(docoHelper.convertToRupiah(total_harga));
    }

    if (typeof _listIncremnt[_inc] != "undefined") {
        if (inputBy == "qty_"+_inc) {
            _listIncremnt[_inc].qty = _qty;
        } else {
            _listIncremnt[_inc].harga = _harga;
        }
        if (isCheck) {
            _listIncremnt[_inc].discount = discount;
        } else {
            _listIncremnt[_inc].discount_rp = diskon_rupiah;
        }
        _listIncremnt[_inc].total_harga = total_harga;
    } else {
        _listIncremnt[_inc] = {
            id : _inc,
            item_id : null,
            qty : inputBy == "qty_"+_inc ? _qty : 0,
            satuan_id : null,
            harga : inputBy == "harga_"+_inc ? _harga : 0,
            discount : 0,
            discount_rp : 0,
            total_harga : 0,
        };
    }
    hitung();
    if ($("#discount_rp_check_"+_inc).is(":checked")) {
        $("#diskon_rp_"+_inc).trigger("keyup");
        $("#diskon_rp_"+_inc).trigger("change");
    } else {
        $("#discount_"+_inc).trigger("keyup");
        $("#discount_"+_inc).trigger("change");
    }
}