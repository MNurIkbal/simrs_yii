if (parseInt(increment) == 0) {
    $('.info-upload').hide();
    }

$(document).on("click", ".addrow", function (e) {
    e.preventDefault();
    var _parent = $(this).closest("tr");
    var _id = parseInt(_parent.attr("data-id"));
    var _key = parseInt(_parent.attr("data-key"));
    var _last = parseInt(_parent.attr("data-last"));
    var _clone = _parent.clone();
    var _tr = $("<tr></tr>");
    _length = 0;

    if (typeof _tmp[_id]["row-" + _key] != "undefined") {
        _last++;
        var _dP = _tmp[_id]["row-" + _key];
        var _data = {
            obat_barang_id: _dP.obat_barang_id,
            qty_input: _dP.qty_input,
            id_detail: _dP.id_detail,
            konversi_id: _dP.konversi_id,
            po_balance: _dP.po_balance,
            qty_penerimaan: _dP.qty_penerimaan,
            satuan: _dP.satuan,
            qty_diterima: 0,
            harga: _dP.harga,
            discount: _dP.discount,
            ppn_persen: _dP.ppn_persen,
            discount_rp: _dP.discount_rp,
            jumlah: _dP.jumlah,
            transaksi_id: _dP.transaksi_id,
            is_kadaluarsa: true,
        };

        var _length = Object.keys(_tmp[_id]).length + 1;
    }

    _clone.filter(function () {
        var _td = $(this).find("td");
        $.each(_td, function (key, val) {
            var _div = $("<div></div>");
            if (key < 5) {
                _td.eq(key).remove();
            }

            if (key == 5) {
                _td.eq(key).find("div.error-parent").remove();
                _td.eq(key).append(_div.attr("id", "error_InfoPoForm" + _id + "qty-row-" + _last));
            }

            if (key == 6) {
                _td.eq(key).find("div.error-parent").remove();
                _td.eq(key).append(_div.attr("id", "error_InfoPoForm" + _id + "date-row-" + _last));
            }
        })
        return _td;
    });

    _clone.attr("data-key", _last);
    var _button = '<button type="button" class="del-row btn btn-danger btn-xs btn-custom" style="padding-left:8px !important;"><span class="fa fa-trash"></span></button>';

    _clone.find("td:last-child").html(_button);
    _clone.find("input").val(null)
    _parent.attr("data-last", _last);
    _parent.after(_clone);
    _parent.filter(function () {
        var _td = $(this).find("td");
        $.each(_td, function (key, val) {
            if (key < 5) {
                _td.eq(key).attr("rowspan", _length);
            }
        })
        return _td;
    });
    $(".date-kartik").kvDatepicker({
        autoclose: true,
        format: "dd-M-yyyy",
        lang: "en",
        startDate: "0d"
    });
});

$(document).on("click", ".del-row", function (e) {
    e.preventDefault();
    var _parent = $(this).closest("tr");
    var _id = parseInt(_parent.attr("data-id"));
    var _key = parseInt(_parent.attr("data-key"));
    var _last = parseInt(_parent.attr("data-last"));
    var _grandPa = $("tr[data-id=" + _id + "][data-key=0]");
    _length = 0;

    if (typeof _tmp[_id]["row-" + _key] != "undefined") {
        delete _cache[_id]["row-" + _key];
        delete _tmp[_id]["row-" + _key];
        var _length = Object.keys(_tmp[_id]).length + 1;
    }

    _grandPa.filter(function () {
        var _td = $(this).find("td");
        $.each(_td, function (key, val) {
            if (key < 5) {
                _td.eq(key).attr("rowspan", _length);
            }
        })
        return _td;
    });
    _parent.remove();
    _hitung();
});

$(document).on("click", ".adddet", function (e) {
    e.preventDefault();
    var parent_tr = $(this).closest("tr");

    var tr_id = parseInt(parent_tr.attr("data-id"));
    var tr_key = parseInt(parent_tr.attr("data-key"));
    var tr_last_key = parseInt(parent_tr.attr("data-last"));
    var data_tmp = _tmp[tr_id]["row-0"];

    var add_tmp = {
        obatalkes_id: data_tmp.obatalkes_id,
        qty_po: data_tmp.qty_po,
        po_balance: data_tmp.po_balance,
        qty_diterima: 0,
        s_konversiobt_id: data_tmp.s_konversiobt_id,
        tgl_kadaluarsa: "",
        no_batch: "",
        keterangan: "",
        harga: data_tmp.harga,
        discount: data_tmp.discount,
        ppn_persen: data_tmp.ppn_persen,
        discount_rp: data_tmp.discount_rp,
        jumlah: data_tmp.jumlah,
        validasipoobatdetail_id: data_tmp.validasipoobatdetail_id,
        is_kadaluarsa: true
    };
    _tmp[tr_id]["row-" + tr_last_key] = add_tmp;
});

$(document).on("keyup", ".qty_penerimaan", function (e) {
    e.preventDefault();
    var _parent = $(this).closest("tr");
    var _id = parseInt(_parent.attr("data-id"));
    var _key = parseInt(_parent.attr("data-key"));
    var _last = parseInt(_parent.attr("data-last"));
    var _default = 0;
    if (typeof _tmp[_id]["row-" + _key] != "undefined") {
        _tmp[_id]["row-" + _key].qty_diterima = 0;
        if (typeof _validPo[_id] != "undefined") {
            var _max = _validPo[_id].max;
            var _value = parseInt(docoHelper.convertToAngka($(this).val()));
            var _usage = 0;
            $.each(_tmp[_id], function (key, val) {
                if (typeof val.qty_diterima != "undefined") {
                    _usage += parseInt(val.qty_diterima);
                }
            });
            _usage += _value;
            if (_max >= _usage) {
                _tmp[_id]["row-" + _key].qty_diterima = parseInt(docoHelper.convertToAngka($(this).val()));
            } else {
                _usage -= _value;
                _tmp[_id]["row-" + _key].qty_diterima = _max - parseInt(_usage);
                $(this).val(docoHelper.convertToRupiah(_max - _usage));
                return false;
            }
        }
    }

    _tmp[_id]["row-" + _key].qty_diterima = parseInt(docoHelper.convertToAngka($(this).val()));
    _hitung();
});

$(document).on("change", ".date-kartik", function (e) {
    e.preventDefault();
    var _parent = $(this).closest("tr");
    var _id = parseInt(_parent.attr("data-id"));
    var _key = parseInt(_parent.attr("data-key"));
    var _last = parseInt(_parent.attr("data-last"));
    if (typeof _cache[_id]["row-" + _key] != "undefined") {
        var _data = _cache[_id]["row-" + _key];
        _data.tgl_kadaluarsa = $(this).val();
        var _dataEx = $.extend({}, _data, _cache[_id]["row-" + _key]);
        _cache[_id]["row-" + _key] = _dataEx;
    }

    _tmp[_id]["row-" + _key].tgl_kadaluarsa = $(this).val();
});

$(document).on("keyup", ".no_batch", function (e) {
    e.preventDefault();
    var _parent = $(this).closest("tr");
    var _id = parseInt(_parent.attr("data-id"));
    var _key = parseInt(_parent.attr("data-key"));
    var _last = parseInt(_parent.attr("data-last"));
    if (typeof _cache[_id]["row-" + _key] != "undefined") {
        var _data = _cache[_id]["row-" + _key];
        _data.no_batch = $(this).val();
        var _dataEx = $.extend({}, _data, _cache[_id]["row-" + _key]);
        _cache[_id]["row-" + _key] = _dataEx;
    }

    _tmp[_id]["row-" + _key].no_batch = $(this).val();
});

$(document).on("keyup", ".keterangan", function (e) {
    e.preventDefault();
    var _parent = $(this).closest("tr");
    var _id = parseInt(_parent.attr("data-id"));
    var _key = parseInt(_parent.attr("data-key"));
    var _last = parseInt(_parent.attr("data-last"));
    if (typeof _cache[_id]["row-" + _key] != "undefined") {
        var _data = _cache[_id]["row-" + _key];
        _data.keterangan = $(this).val();
        var _dataEx = $.extend({}, _data, _cache[_id]["row-" + _key]);
        _cache[_id]["row-" + _key] = _dataEx;
    }

    _tmp[_id]["row-" + _key].keterangan = $(this).val();
});

$(document).on("click", "#btn-edit", function (ev) {
    ev.preventDefault();
    var editPost = $("#po-form").serializeArray();
    var no_faktur = $("#no_faktur").val();
    var no_faktur_sementara = $("#no_faktur_sementara").val();

    if (no_faktur_sementara == "" && no_faktur == "") {
        docoNotification("warning", i18next.t("Perhatian"), i18next.t("No Faktur / No Faktur Sementara harus di isi"));
        return false;
    }

    editPost.push(
        {
            name: "list_data",
            value: JSON.stringify(_tmp)
        },
        {
            name: "InfoPoForm[diterima_oleh]",
            value: 1
        }
    );

    var toPost = new FormData();
    for (let i = 1; i < list.length + 1; i++) {
        let getFile = $("#file-" + i)[0];
        if (typeof getFile !== 'undefined') {
        toPost.append("UploadHasilForm[upload_file][]", $("#file-" + i)[0].files[0]);
        toPost.append("UploadHasilForm[catatan][]", $("#catatan-" + i).val());
    }
}

        $.each(editPost, function (key, value) {
    toPost.append(value.name, value.value);
});

$().docoForm("click", {
    url: $("#po-form").attr("action"),
    data: toPost,
    dataType: false,
    contentType: false,
    processData: false,
    isUpload: true,
    success: function (data) {
        window.location = _urlToIndex
    },
    error: function (data) {

    }
})
    });

$(document).on("click", "#btn-simpana", function (e) {
    e.preventDefault();
    var dataPost = $("#po-form").serializeArray();
    dataPost.push({
        name: "data_detail",
        value: JSON.stringify(_cache)
    });

    dataPost.push({
        name: "valid_data",
        value: JSON.stringify(_validPo)
    });

    dataPost.push({
        name: "diterima_oleh",
        value: _idPenerima
    });

    dataPost.push({
        name: "supplier_id",
        value: _supplierId
    });
    var _data = new FormData();
    for (let i = 1; i < list.length + 1; i++) {
        let getFile = $("#file-" + i)[0];
        if (typeof getFile !== 'undefined') {
        _data.append("UploadHasilForm[upload_file][]", $("#file-" + i)[0].files[0]);
        _data.append("UploadHasilForm[catatan][]", $("#catatan-" + i).val());
    }
}

        $.each(dataPost, function (key, value) {
    _data.append(value.name, value.value);
});

_data.append("catatan", $("textarea[name=catatan]").val());
$().docoForm("click", {
    url: $("#po-form").attr("action"),
    data: _data,
    dataType: false,
    cache: false,
    contentType: false,
    processData: false,
    isUpload: true,
    success: function (data) {
        var type = "obat";
        (new PNotify({
            title: "Proses Berhasil !",
            text: "Data Penerimaan dengan Nomor <strong>No Penerimaan</strong> berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                    location.reload();
    }).on('pnotify.cancel', function() {
                    location.reload();
                });
            },
error: function (data) {

}
        });
    });

$(document).on("click", "#btn-batal-penerimaan", function (e) {
    $().docoForm("delete", {
        url: $(this).attr("data-target"),
        dataType: false,
        additional: "data-rm",
        contentType: false,
        processData: false,
        isUpload: true,
        success: function (data) {
            window.location = _urlToIndex
        },
        error: function (data) {
        }
    });
});

$(document).on("click", "#btn-verifikasi", function (e) {
    $().docoForm("delete", {
        url: $(this).attr("data-target"),
        confirmMessage: "Apakah anda yakin ingin verifikasi transaksi ini ?",
        dataType: false,
        contentType: false,
        processData: false,
        success: function (data) {
            var id_transaksi = data.response.id_transaksi;
            alertHarga(id_transaksi);

            $("#modal_update_harga").on("hidden.bs.modal", function () {
                window.location = _urlToIndex
            });
        },
    });

});

$(document).ready(function () {
    if (_verif == 0) {
        $("#btn-edit").attr("disabled", false);
        $("#btn-batal-penerimaan").attr("disabled", false);
        $("#print-detail").attr("disabled", true);
        $("#btn-print-grn").attr("disabled", true);
        $("#btn-verifikasi").attr("disabled", false);
    } else if (_verif == 1) {
        $("#btn-batal-penerimaan").attr("disabled", true);
        $("#print-detail").attr("disabled", false);
        $("#btn-print-grn").attr("disabled", false);
        $("#btn-verifikasi").attr("disabled", true);
        $("#file-1").attr("disabled", true);
        $("#catatan-1").attr("disabled", true);
    } else if (_verif == 2) {
        $("#btn-edit").attr("disabled", true);
        $("#btn-batal-penerimaan").attr("disabled", true);
        $("#print-detail").attr("disabled", true);
        $("#btn-print-grn").attr("disabled", true);
        $("#btn-verifikasi").attr("disabled", true);
        $("#file-1").attr("disabled", true);
        $("#catatan-1").attr("disabled", true);
    } else {
        $("#btn-edit").attr("disabled", true);
        $("#btn-batal-penerimaan").attr("disabled", true);
        $("#print-detail").attr("disabled", true);
        $("#btn-print-grn").attr("disabled", true);
        $("#btn-verifikasi").attr("disabled", true);
    }


    $(".date-kartik").kvDatepicker({
        autoclose: true,
        format: "dd-M-yyyy",
        language: "id"
    });

    $(".search-pegawai").select2({
        placeholder: "Pilih Pegawai",
        minimumInputLength: 3,
        ajax: {
            url: baseUrl + "gudang/informasi-po/search-pegawai",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                    search: params,
                }
                return params;
            },
            processResults: function (data) {
                $.each(data.result, function (key, val) {
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

$(document).ready(function () {
    _checkButton();
    _hitung();
});

var _checkButton = function () {
    var _length = $(".inputfile").length;
    var _no = 0;
    $.each($(".inputfile"), function () {
        var _value = $(this).val();
        if (_value) {
            _no++;
        }
    });

    if (_length == _no) {
        $("#add-upload").prop("disabled", false);
    } else {
        $("#add-upload").prop("disabled", true);
    }
}

$(document).on("click", ".delete", function () {
    let button = this;
    let pkId = $(this).attr("data-id");
    let parent = $(this).attr("data-parent");
    if (pkId) {
        $(this).docoForm("delete", {
            url: "/radiologi/input-hasil/delete-upload?id=" + pkId + "&parent=" + parent,
            success: function (params) {
                $(button).parent().parent().remove();
            }
        });
    } else {
        $(button).parent().parent().remove();
        _checkButton();
    }
})

function uploadForm(increment) {
    let uploadDiv = "";
    uploadDiv += '<div class="col-md-12 upload-section" id="upload-section">';
    uploadDiv += '<div class="lurus">';
    uploadDiv += '<button type="button" class="btn btn-sm btn-block btn-danger delete"><i class="fa fa-trash"></i></button>';
    uploadDiv += '</div>';
    uploadDiv += '<div class="lurus">';
    uploadDiv += '<input type="file" name="UploadHasilForm[upload_file][]" id="file-" + increment + "" class="form-control inputfile inputfile-1" multiple="true">';
    uploadDiv += '<label for="file-" + increment + "" class="label-upload">';
    uploadDiv += '<i class="fa fa-upload"></i>';
    uploadDiv += '<span id="label-file"> Pilih Berkas</span>';
    uploadDiv += '</label>';
    uploadDiv += '</div>';
    uploadDiv += '<div class="lurus">';
    uploadDiv += '<div class="col-md-12">';
    uploadDiv += '<input class="form-control" name="UploadHasilForm[catatan][]" type="text" style="width:400px;" placeholder="Masukan catatan" id="catatan-" + increment + "">';
    uploadDiv += '</div>';
    uploadDiv += '</div>';
    uploadDiv += '</div>';

    $("#list-upload").append(uploadDiv);
}

$(document).on("click", ".delete-doc", function () {
    var actionUrl = $(this).attr("data-action");
    var docID = $(this).attr("data-id");
    var _url = actionUrl + "?id=" + docID;

    if (docID) {
        $(this).docoForm("delete", {
            url: _url,
            success: function () {
                location.reload();
            }
        });
    }
});

$('#add-upload').on('click', function() {
        increment++;
uploadForm(increment)
list.push(increment)
$('.info-upload').show();
        let list_upload = $('.upload-section').get();
        for (var i = 1; i < list.length + 1; i++) {
    $("#file-" + i).hide();
    var inputs = document.querySelectorAll('.inputfile');
            Array.prototype.forEach.call(inputs, function (input) {
        var label = input.nextElementSibling,
            labelVal = label.innerHTML;

        input.addEventListener('change', function (e) {
                    var fileName = '';
        if (this.files && this.files.length > 1) {
            fileName = (this.getAttribute('data-multiple-caption') || '').replace("{count}", this.files.length);
                    } else {
            fileName = e.target.value.split("").pop();
                    }

        if (fileName) {
            $('#add-upload').prop("disabled",false);
                        label.querySelector('span').innerHTML = fileName;
                    }
    });

    input.addEventListener('focus', function () {
                    input.classList.add('has-focus');
                });
input.addEventListener('blur', function () {
                    input.classList.remove('has-focus');
                });
            });
        }
$(this).prop("disabled", true);
    });

(function (document, window, index) {
    var inputs = document.querySelectorAll('.inputfile');
        Array.prototype.forEach.call(inputs, function (input) {
        var label = input.nextElementSibling,
            labelVal = label.innerHTML;

        input.addEventListener('change', function (e) {
                var fileName = '';
        if (this.files && this.files.length > 1) {
            fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                } else {
            fileName = e.target.value.split("").pop();
                }

        if (fileName)
            $('#add-upload').prop("disabled",false);
                    label.querySelector('span').innerHTML = fileName;
            });

    input.addEventListener('focus', function () {
                input.classList.add('has-focus');
            });
input.addEventListener('blur', function () {
                input.classList.remove('has-focus');
            });
        });
    }(document, window, 0));

// To do: 
//              Calculate all amount of total, discount and tax.
//              Total will be re-calc from qty * price
//              Discount will use percentage from purchase order then times with Total
//              Tax will use percentage from purchase order then times with Total

var _hitung = function () {
    var finalTotalDiskon = [];

    var subTotal            = 0;
    var totalDisc           = 0;
    var subWithDisc    = 0;
    var ppnAmount      = 0;
    var grandTotal        = 0;

    $.each(_tmp, function (key, values) {
        $.each(values, function (k, val) {
            // declare base
            var price = isNaN(val.harga) ? 0 : val.harga;
            var qty_val = isNaN(val.qty_diterima) ? 0 : val.qty_diterima;
            var disc_percentage = isNaN(val.discount) ? 0 : val.discount;
            var ppn_percentage = isNaN(val.ppn_persen) ? 0 : val.ppn_persen;

            // Calculation
            var _subtotal                              = price * qty_val;
            var _discount_amount             = (disc_percentage / 100) * _subtotal;
            var _subtotalWithDiscount    = _subtotal - _discount_amount;
            var _tax_amount                        = (ppn_percentage / 100) * _subtotalWithDiscount;
            var _total                                     = _subtotalWithDiscount + _tax_amount;

            // Sum process
            subTotal            += _subtotal;
            totalDisc           += _discount_amount;
            subWithDisc     += _subtotalWithDiscount;
            ppnAmount       += _tax_amount;
            grandTotal         += _total;

            obatAlkesId = val.obatalkes_id;
            finalTotalDiskon["key" + key] = totalDisc;
        })
    });

    $(".sub_total_before_disc").html(docoHelper.convertToRupiah(subTotal));
    $(".total_diskon").html(docoHelper.convertToRupiah(totalDisc));
    $(".sub_total").html(docoHelper.convertToRupiah(subWithDisc));
    $(".ppn").html(docoHelper.convertToRupiah(ppnAmount));
    $(".grand_total").html(docoHelper.convertToRupiah(grandTotal));
}
