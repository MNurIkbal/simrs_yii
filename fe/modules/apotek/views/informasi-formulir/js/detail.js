$(document).ready(function() {
    $("#btn-tambah").attr("disabled", true);
    $.each(detailSO, function(key, val) {
        let rowBackground = $("tr").closest("[data-parent='"+ val.obatalkes_id +"']");
        let stok_fisik = rowBackground.find(".stok-fisik").val().replaceAll(".", "");

        // remove separator ribuan
        var _realVal = rowBackground.find(".revisi_stok").val().replaceAll(".", "");
        // replace comma dengan dot untuk pembacaan desimal
        _realVal = parseFloat(_realVal.replaceAll(",", "."));
        let _val = isNaN(parseFloat(_realVal)) ? 0 : parseFloat(_realVal);
        let stokSistemId = "#stokSistemSection";
        let stokSelisihId = "p.selisih-revisi";
        
        // remove separator ribuan
        var stokSistem = rowBackground.find(stokSistemId).text().replaceAll(".", "");
        // replace comma dengan dot untuk pembacaan desimal
        stokSistem = parseFloat(stokSistem.replaceAll(",", "."));

        var stokSelisihRevisi = _val - stokSistem;
        stokSelisihRevisi = (stokSelisihRevisi % 1) === 0 ? Math.round(stokSelisihRevisi) : docoHelper.numberFormat(stokSelisihRevisi, 3, ',', '.')
        var selisihRevisi = _realVal != '' && _val != '' ? stokSelisihRevisi : (_realVal == 0 ? stokSelisihRevisi : '');
        $(this).closest('tr').find(stokSelisihId).text(selisihRevisi);
    
        let index = detailSO.findIndex(function(item) {
            return item.obatalkes_id == val.obatalkes_id;
        });

        if(typeof detailSO[index] != "undefined") {
            detailSO[index].revisi_stok = _realVal;
            detailSO[index].selisih_akhir = selisihRevisi;
        } else {
            newItem.revisi_stok = _realVal;
            newItem.selisih_akhir = _realVal - newItem.stok_sistem;
        }
        
        if(selisihRevisi == "" && stok_fisik != "" && (stok_fisik - stokSistem) == 0) {
            rowBackground.addClass("row-noselisih");
        } else if(selisihRevisi != "" && selisihRevisi == 0) {
            rowBackground.addClass("row-noselisih");
        } else {
            rowBackground.removeClass("row-noselisih");
        }
    });
});
$(() => {
    $("#resetFormBtn").bind("click", () => {
        $("#detailForm").trigger("reset")
        $("#detailForm").find("select,input").trigger("change")
        $("#ajax-form").trigger("reset")
        $("#ajax-form").find("select,input").trigger("change")
    })
})
function disabledSubmitButton() {
    $("#simpan-so").prop("disabled", true)
    setTimeout(() => {
        $("#simpan-so").prop("disabled", true)
        window.location.href = "/apotek/informasi-formulir/#";
    }, 5000)
}
$("#simpan-so").on("click",function (event) {
    event.preventDefault();
    var _form = $("#ajax-form").serializeArray();
    let validateRequired = true;
    $.each(detailSO, function (key, val) {
        let currentRow = $("tr").closest("[data-parent='"+val.obatalkes_id+"']");
        let textSelisihFisik = $.trim(currentRow.find("#totalSelisihSection").text());
        let textSelisihRevisi = $.trim(currentRow.find(".selisih-revisi").text());

        if(is_first_time) {
            if(is_fulfilled == "1" && (val.stok_fisik == null || val.stok_fisik === "")) {
                currentRow.addClass("row-empty");
                validateRequired = false;
                docoNotification("warning", "Data Tidak Lengkap !", "Stok Fisik harus diisi");
            }
        } else {
            if(is_disabledfulfilled == "1") {
                if(is_fulfilled == "1" && textSelisihFisik != 0 && (textSelisihRevisi == null || textSelisihRevisi == "")) {
                    currentRow.addClass("row-empty");
                    validateRequired = false;
                    docoNotification("warning", "Data Tidak Lengkap !", "Stok Revisi harus diisi");
                }
            } else {
                if(is_fulfilled == "1" && (textSelisihRevisi == null || textSelisihRevisi == "")) {
                    currentRow.addClass("row-empty");
                    validateRequired = false;
                    docoNotification("warning", "Data Tidak Lengkap !", "Stok Revisi harus diisi");
                }
            }
        }
        _form.push({name : "inputan_so[" + key + "]", value : JSON.stringify(val)});
    })

    if(validateRequired) {
        $(this).docoForm("click",{
            url : "/apotek/informasi-formulir/save?id="+_id,
            method : "POST",
            type : "json",
            data: _form,
            isDataString: true,
            success: function(data){
                var response = data.data;
                var id = response.data.id;
                (new PNotify({
                    title: "Berhasil",
                    text: "Transaksi Stok Opname berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                    window.open('/apotek/informasi-stok-opname/export-pdf?id='+id);
                    setTimeout(() => {
                        window.open('/apotek/informasi-stok-opname/inf-stok-formulir-opname', '_self');
                    }, 100);
                }).on('pnotify.cancel', function() {
                    window.open('/apotek/informasi-stok-opname/inf-stok-formulir-opname', '_self');
                });
            },
            error: function(data) {
                var response = data.responseJSON;
                let message = [];
    
                if (response.data.data != undefined) {
                    var data = response.data.data
                    for (const [key, value] of Object.entries(data)) {
                        message.push(value);
                    }
                    docoNotification("error", "Proses Gagal", message.join( "<br />" ))
                } else {
                    docoNotification("error",  response.message)
                }
            }
        });
    }
});
$(document).on("blur", ".stok-fisik", function ({delegateTarget}) {
    const value = $(delegateTarget).val().split(".")
    if(value.length == 2 && value[1] === "") {
        $(delegateTarget).val(value[0])
    } else if (($(delegateTarget).val() % 1) === 0) {
        $(delegateTarget).val(value[0])
    }
})
$(document).on("focusin",".stok-fisik", function (event) {
    $(event.currentTarget).data("oldVal", $(event.currentTarget).val());
});

$(document).on("change", ".stok-fisik", function (event) {
    let currentRow = $($(event.currentTarget).parents(".parent-row"))
    let stokSistemId = "#stokSistemSection";
    let stokSelisihId = "#totalSelisihSection";
    var _parent = $(this).closest("tr");
    let _id = _parent.attr("data-parent");
    if(currentRow.length <= 0) {
        currentRow = $($(event.currentTarget).parents(".new-item"))
        if(currentRow.length <= 0) {
            currentRow = $($(event.currentTarget).parents(".new-row"))
            stokSistemId = "#newStokSistem";
            stokSelisihId = "#newStokSelisih";
            _id = $("#obatalkes_id").val();
        }
    }
    
    let stok_fisik = parseFloat($(event.currentTarget).val().replaceAll(",", "."))
    // remove separator ribuan
    let stok_sistem = $(currentRow.find(stokSistemId)).text().replaceAll(".", "")
    // replace comma dengan dot untuk pembacaan desimal
    stok_sistem = parseFloat(stok_sistem.replaceAll(",", "."));

    const dataSelisih = stok_fisik - stok_sistem
    if (isNaN(dataSelisih)) {
        $(currentRow.find(stokSelisihId)).html("");
    } else {
        $(currentRow.find(stokSelisihId)).html((dataSelisih % 1) === 0 ? Math.round(dataSelisih) : docoHelper.numberFormat(dataSelisih, 3, ',', '.'))
    }
    
    let index = detailSO.findIndex(function(item) {
        return item.obatalkes_id == _id;
    });

    if(typeof detailSO[index] != "undefined") {
        detailSO[index].stok_fisik = $(this).val().replaceAll(",", ".");
        detailSO[index].stok_selisih = dataSelisih;
    } else {
        newItem.stok_fisik = $(this).val().replaceAll(",", ".");
        newItem.stok_selisih = $(this).val().replaceAll(",", ".") - newItem.stok_sistem;
    }

    let rowBackground = $("tr").closest("[data-parent='"+_id+"']");
    let textStokFisik = $(event.currentTarget).val();
    if(is_fulfilled == "1" && (textStokFisik === "" || textStokFisik == null)) {
        rowBackground.addClass("row-empty");
    } else {
        rowBackground.removeClass("row-empty");
    }
})
$(document).on('change', '.revisi_stok', function(e){
    // remove separator ribuan
    var _realVal = $(this).val().replaceAll(".", "");
    // replace comma dengan dot untuk pembacaan desimal
    _realVal = parseFloat(_realVal.replaceAll(",", "."));
    let _val = isNaN(parseFloat(_realVal)) ? 0 : parseFloat(_realVal);
    let currentRow = $($(e.currentTarget).parents(".parent-row"))
    let stokSistemId = "#stokSistemSection";
    let stokSelisihId = "p.selisih-revisi";
    var _parent = $(this).closest("tr");
    let _id = _parent.attr("data-parent");
    if(currentRow.length <= 0) {
        currentRow = $($(e.currentTarget).parents(".new-item"))
        if(currentRow.length <= 0) {
            currentRow = $($(e.currentTarget).parents(".new-row"))
            stokSistemId = "#newStokSistem";
            stokSelisihId = "#newSelisih";
            _id = $("#obatalkes_id").val();
        }
    }
    // remove separator ribuan
    var stokSistem = _parent.find(stokSistemId).text().replaceAll(".", "");
    // replace comma dengan dot untuk pembacaan desimal
    stokSistem = parseFloat(stokSistem.replaceAll(",", "."));

    var stokSelisihRevisi = _val - stokSistem;
    stokSelisihRevisi = (stokSelisihRevisi % 1) === 0 ? Math.round(stokSelisihRevisi) : docoHelper.numberFormat(stokSelisihRevisi, 3, ',', '.')
    var selisihRevisi = _realVal != '' && _val != '' ? stokSelisihRevisi : (_realVal == 0 ? stokSelisihRevisi : '');
    $(this).closest('tr').find(stokSelisihId).text(selisihRevisi);
    
    let index = detailSO.findIndex(function(item) {
        return item.obatalkes_id == _id;
    });

    if(typeof detailSO[index] != "undefined") {
        detailSO[index].revisi_stok = _realVal;
        detailSO[index].selisih_akhir = selisihRevisi;
    } else {
        newItem.revisi_stok = _realVal;
        newItem.selisih_akhir = _realVal - newItem.stok_sistem;
    }

    let rowBackground = $("tr").closest("[data-parent='"+_id+"']");
    let stok_fisik = rowBackground.find(".stok-fisik").val().replaceAll(".", "");
    let textRevisiStok = rowBackground.find(".revisi_stok").val();
    let textSelisihFisik = $.trim(rowBackground.find("#totalSelisihSection").text());
    if(is_fulfilled == "1" && textSelisihFisik != 0 && (textRevisiStok == null || textRevisiStok === "")) {
        rowBackground.addClass("row-empty");
    } else {
        rowBackground.removeClass("row-empty");
    }
    
    if(selisihRevisi == "" && stok_fisik != "" && (stok_fisik - stokSistem) == 0) {
        rowBackground.addClass("row-noselisih");
    } else if(selisihRevisi != "" && selisihRevisi == 0) {
        rowBackground.addClass("row-noselisih");
    } else {
        rowBackground.removeClass("row-noselisih");
    }
});

$("#obatalkes_id").on("change", function (e) {
    if($(this).val() != null) {
        newItem = {
            obatalkes_id: $(this).val(),
            obatalkes_nama: $(this).select2("data")[0].obatalkes_nama,
            obatalkes_kode: $(this).select2("data")[0].obatalkes_kode,
            satuankecil_nama:  $(this).select2("data")[0].satuan_kecil,
            rakobat_nama:  $(this).select2("data")[0].rakobat_nama,
            laci:  $(this).select2("data")[0].laci,
            uom:  $(this).select2("data")[0].uom,
            stok_saatini:  $(this).select2("data")[0].stok_saatini,
            stok_in:  $(this).select2("data")[0].stok_in,
            stok_out:  $(this).select2("data")[0].stok_out,
            stok_sistem:  $(this).select2("data")[0].stok_sistem,
            is_newso: true
        };
        
        $("#newKode").text(newItem.obatalkes_kode);
        $("#newNama").text(newItem.obatalkes_nama);
        $("#newSatuan").text(newItem.satuankecil_nama);
        $("#newMasuk").text(newItem.stok_in);
        $("#newKeluar").text(newItem.stok_out);
        $("#newLaci").text(newItem.laci);
        $("#NewUom").text(newItem.uom);
        $("#newStokSaatIni").text(newItem.stok_saatini);
        $("#newStokSistem").text(newItem.stok_sistem);

        let index = detailSO.findIndex(function(item) {
            return item.obatalkes_id == _id;
        });
        // validasi obat sudah ada di current form SO
        if(detailSO.some(item => item.obatalkes_id == newItem.obatalkes_id)) {
            docoNotification("error", "Tambah Obat Gagal !", "Obat yang dipilih sudah ada di formulir " + noFormulir);
            $("#btn-tambah").attr("disabled", true);
            disableAdd = true; 
            return false;
        }
        
        $.ajax({
            url : "/apotek/informasi-formulir/get-stok-obat",
            data: {
                'obatalkes_id': newItem.obatalkes_id,
                'ruangan_id': ruanganId
            },
            beforeSend: function() {
                $("#btn-tambah").attr("disabled", true);
            },
            complete: function() {
                $("#btn-tambah").attr("disabled", false);
            },
            success : function (res){
                let data = res.data;
                $("#newStokSistem").text(data.stok_sistem);
                $("#newStokFisik").val(data.stok_saatini);
                $("#newStokSaatIni").val(data.stok_saatini);
                $("#NewUom").val(data.uom);
                $("#newSatuan").val(data.satuan_kecil);
                $("#newMasuk").val(data.stok_in);
                $("#newKeluar").val(data.stok_out);
                $("#newStokSelisih").text(0);

                newItem["satuankeci"] = data.satuan_kecil;
                if(data.uom != null){
                    newItem["uom"] = data.uom;
                }else{
                    newItem["uom"] = "";
                }
                if(data.stok_saatini == null){
                    newItem["qty_stok"] = 0;
                    newItem["stok_fisik"] = 0;
                }else{
                    newItem["qty_stok"] = data.stok_saatini;
                    newItem["stok_fisik"] = data.stok_saatini;
                }
                if(data.laci == null){
                    newItem["laci"] = "Tanpa Rak";
                }else{
                    newItem["laci"] = data.laci;
                }
                if(data.stok_in == null){
                    newItem["qty_masuk"] = 0;
                }else{
                    newItem["qty_masuk"] = data.stok_in;
                }
                if(data.stok_out == null){
                    newItem["qty_keluar"] = 0;
                }else{
                    newItem["qty_keluar"] = data.stok_out;
                }
                if(data.stok_sistem == null){
                    newItem["stok_sistem"] = 0;
                }else{
                    newItem["stok_sistem"] = data.stok_sistem;
                }
                newItem["stok_selisih"] = 0;
                newItem["revisi_stok"] = "";
                newItem["selisih_akhir"] = "";
                // jika status belum belum_verifikasi
                if(belum_verifikasi == false) {
                    $("#newStokFisik").val(newItem["stok_fisik"]);
                    $("#newRevisiStok").val(newItem["revisi_stok"]);
                    $("#newSelisih").text(0);
                    newItem["revisi_stok"] = 0;
                    newItem["selisih_akhir"] = 0;
                }
            }
        });
    }
});

$("#btn-tambah").on("click", function(e) {
    e.preventDefault();
    if($("#newStokFisik").val() == "" || $("#newStokFisik").val() == null) {
        docoNotification("warning", "Data Tidak Lengkap !", "Stok Fisik harus diisi");
        return false;
    }
    appendobat(newItem);
    detailSO.push(newItem);
    clearNewForm();
});

$(document).on("click", ".btn-hapus", function(e) {
    e.preventDefault();
    let obatId = $(this).attr("data-index");
    // delete item from array detailSO
    detailSO = detailSO.filter(function(item) {
        return item.obatalkes_id != obatId;
    });
    $("#new-item-"+obatId).remove();
});

function appendobat(newItem){
    var no = detailSO.length + 1;
    var str_tr = "";
    newItem.formstokopname_id;
    str_tr = `
        <tr class="new-item" id="new-item-${newItem.obatalkes_id}" data-parent="${newItem.obatalkes_id}">
            <td>${no}</td>
            <td>${newItem.laci}</td>
            <td>${newItem.obatalkes_nama}</td>
            <td>${newItem.uom}</td>
            <td>${newItem.satuankecil_nama}</td>
            <td>${(newItem.stok_sistem % 1) === 0 ? Math.round(newItem.stok_sistem) : docoHelper.numberFormat(newItem.stok_sistem, 3, ',', '.')}</td>
            <td class="text-center">${(newItem.qty_masuk % 1) === 0 ? Math.round(newItem.qty_masuk) : docoHelper.numberFormat(newItem.qty_masuk, 3, ',', '.')}</td>
            <td class="text-center">${(newItem.qty_keluar % 1) === 0 ? Math.round(newItem.qty_keluar) : docoHelper.numberFormat(newItem.qty_keluar, 3, ',', '.')}</td>
            <td>${(newItem.qty_stok % 1) === 0 ? Math.round(newItem.qty_stok) : docoHelper.numberFormat(newItem.qty_stok, 3, ',', '.')}</td>
            <td>
                <input 
                    type="text" 
                    class="form-control doco-decimal input-sm typeahead text-right event-so stok-fisik" 
                    value="${(newItem.stok_fisik % 1) === 0 ? Math.round(newItem.stok_fisik) : docoHelper.numberFormat(newItem.stok_fisik, 3, ',', '.')}"
                    autocomplete="off"
                >
            </td>
            <td class="text-center">${(newItem.stok_selisih % 1) === 0 ? Math.round(newItem.stok_selisih) : docoHelper.numberFormat(newItem.stok_selisih, 3, ',', '.')}</td>
            <td>
                <input 
                    type="text" 
                    class="form-control doco-decimal input-sm typeahead text-right event-so revisi_stok" 
                    id="revisi-new"
                    value="${newItem.revisi_stok == "" ? "" : (newItem.revisi_stok % 1) === 0 ? Math.round(newItem.revisi_stok) : docoHelper.numberFormat(newItem.revisi_stok, 3, ',', '.')}"
                    autocomplete="off"
                >
            </td>
            <td class="text-center"><p>${newItem.selisih_akhir == "" ? "" : (newItem.selisih_akhir % 1) === 0 ? Math.round(newItem.selisih_akhir) : docoHelper.numberFormat(newItem.selisih_akhir, 3, ',', '.')}</p></td>
            <td class="text-center"><button class="btn btn-sm btn-danger btn-hapus" data-index="${newItem.obatalkes_id}"><i class="fa fa-minus"></i></button></td>
        </tr>`;
    
    $(".new-row").before(str_tr);

    if(is_first_time) {
        console.log("fisik")
        $(`[data-parent='${newItem.obatalkes_id}']`).find("input.stok-fisik").prop("disabled", false);
        $(`[data-parent='${newItem.obatalkes_id}']`).find("input.revisi_stok").prop("disabled", true);
    } else {
        console.log("revisi")
        $(`[data-parent='${newItem.obatalkes_id}']`).find("input.stok-fisik").prop("disabled", true);
        $(`[data-parent='${newItem.obatalkes_id}']`).find("input.revisi_stok").prop("disabled", false);
    }
}

function clearNewForm() {
    newItem = {};
    $("#obatalkes_id").val(null).trigger("change");
    $("#newSatuan").text("");
    $("#newMasuk").text("");
    $("#newStokSaatIni").text("");
    $("#NewUom").text("");
    $("#newKeluar").text("");
    $("#newStokSistem").text("");
    $("#newStokFisik").val("");
    $("#newStokSelisih").text("");
    $("#newRevisiStok").val("");
    $("#newSelisih").text("");
    $("#btn-tambah").attr("disabled", true);
}
