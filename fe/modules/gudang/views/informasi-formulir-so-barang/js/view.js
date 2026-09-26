$(document).ready(function($) {
    $("#btn-tambah").attr("disabled", true);
    $('.revisi_edit').trigger('keyup');
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
        window.location.href = "/gudang/informasi-formulir-so-barang/#";
    }, 5000)
}
$("#simpan-so").on("click",function (event) {
    event.preventDefault();
    var _form = $("#ajax-form").serializeArray();
    $.each(detailSO, function (key, val) {
        _form.push({name : "inputan_so[" + key + "]", value : JSON.stringify(val)});
    })
    $(this).docoForm("click",{
        url : "/gudang/informasi-formulir-so-barang/save?id="+_id,
        method : "POST",
        type : "json",
        data : _form,
        success : function (data) {
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
            })).get().on("pnotify.confirm", function () {   
                var response = data.data;
                window.open(`/gudang/informasi-formulir-so-barang/export-pdf?id=${response.data.id}`);
            }).on("pnotify.cancel", function () {

            });
            disabledSubmitButton()
            $(".content-wrapper").find("input,select").prop("disabled",true);
        },
        error: function(data) {
            var response = data.responseJSON.data;
            let message = [];

            if (response.data != undefined) {
                var data = response.data
                for (const [key, value] of Object.entries(data)) {
                    message.push(value);
                }
                docoNotification("error", "Proses Gagal", message.join( "<br />" ))
            } else {
                docoNotification("error", response.data.meta.title, response.message)
            }
        }
    });
});
$(document).on("blur", ".stok-fisik", function ({delegateTarget}) {
    const value = $(delegateTarget).val().split(".")
    if(value.length == 2 && value[1] === "") {
        $(delegateTarget).val(value[0])
    } else if (($(delegateTarget).val() % 1) === 0) {
        $(delegateTarget).val(value[0])
    }
})
$(document).on("keypress", ".stok-fisik", function (event) {
    return isNumberKey(event, this, "with-commas")
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
            _id = $("#barang_id").val();
        }
    }
    let thisVal = $(event.currentTarget).val() != '' ? $(event.currentTarget).val() : $(currentRow.find(stokSistemId)).text();
    const dataSelisih = parseFloat(thisVal) - parseFloat($(currentRow.find(stokSistemId)).text());
    if (isNaN(dataSelisih)) {
        $(currentRow.find(stokSelisihId)).html("");
    } else {
        $(currentRow.find(stokSelisihId)).html((dataSelisih % 1) === 0 ? Math.round(dataSelisih) : dataSelisih.toFixed(2))
    }
    
    let index = detailSO.findIndex(function(item) {
        return item.barang_id == _id;
    });

    if(typeof detailSO[index] != "undefined") {
        detailSO[index].stok_fisik = $(this).val();
        detailSO[index].stok_selisih = dataSelisih;
    } else {
        newItem.stok_fisik = $(this).val();
        newItem.stok_selisih = $(this).val() - newItem.stok_sistem;
    }
})
$(document).on("keypress", ".revisi_edit", function (event) {
    return isNumberKey(event, this, "with-commas")
})
$(document).on('keyup', '.revisi_edit', function(e){
    let _val = $(this).val();
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
            _id = $("#barang_id").val();
        }
    }
    var stokSistem = parseFloat(_parent.find(stokSistemId).text());
    var stokSelisihRevisi = _val - stokSistem;
    stokSelisihRevisi = (stokSelisihRevisi % 1) === 0 ? Math.round(stokSelisihRevisi) : stokSelisihRevisi.toFixed(2)
    var selisihRevisi = _val != '' ? stokSelisihRevisi : '';
    $(this).closest('tr').find(stokSelisihId).text(selisihRevisi);
    
    let index = detailSO.findIndex(function(item) {
        return item.barang_id == _id;
    });

    if(typeof detailSO[index] != "undefined") {
        detailSO[index].revisi_stok = $(this).val();
        detailSO[index].selisih_akhir = selisihRevisi;
    } else {
        newItem.revisi_stok = $(this).val();
        newItem.selisih_akhir = $(this).val() - newItem.stok_sistem;
    }
});

$("#barang_id").on("change", function (e) {
    if($(this).val() != null) {
        newItem = {
            barang_id: $(this).val(),
            barang_nama: $(this).select2("data")[0].barang_nama,
            kelompok_nama: $(this).select2("data")[0].kelompok_nama,
            subkelompok_nama: $(this).select2("data")[0].subkelompok_nama
        };
        
        $("#newKelompok").text(newItem.kelompok_nama);
        $("#newSubKelompok").text(newItem.subkelompok_nama);

        // validasi barang sudah ada di current form SO
        if(detailSO.some(item => item.barang_id == newItem.barang_id)) {
            docoNotification("error", "Tambah Barang Gagal !", "Barang yang dipilih sudah ada di formulir " + noFormulir);
            disableAdd = true; 
            return false;
        }
        
        // to-do: validasi barang sudah ada di form SO lain

        $.ajax({
            url : "/gudang/informasi-formulir-so-barang/get-stok-barang",
            data: {
                'barang_id': newItem.barang_id,
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
                $("#newStokSistem").text(data.qty_stok);
                $("#newStokFisik").val(data.qty_stok);
                $("#newStokSelisih").text(0);

                newItem["stok_sistem"] = data.qty_stok;
                newItem["stok_fisik"] = data.qty_stok;
                newItem["stok_selisih"] = 0;
                newItem["revisi_stok"] = "";
                newItem["selisih_akhir"] = "";

                // jika status belum verifikasi
                if(isBelumInputHasil == false) {
                    $("#newRevisiStok").val(data.qty_stok);
                    $("#newSelisih").text(0);
                    newItem["revisi_stok"] = data.qty_stok;
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
    appendbarang(newItem);
    detailSO.push(newItem);
    clearNewForm();
});

$(document).on("click", ".btn-hapus", function(e) {
    e.preventDefault();
    let barangId = $(this).attr("data-index");
    // delete item from array detailSO
    detailSO = detailSO.filter(function(item) {
        return item.barang_id != barangId;
    });
    $("#new-item-"+barangId).remove();
});

function appendbarang(newItem){
    var no = detailSO.length + 1;
    var str_tr = "";
    str_tr = `
        <tr class="new-item" id="new-item-${newItem.barang_id}" data-parent="${newItem.barang_id}">
            <td>${no}</td>
            <td>${newItem.barang_nama}</td>
            <td>${newItem.kelompok_nama}</td>
            <td>${newItem.subkelompok_nama}</td>
            <td class="text-center" id="stokSistemSection">${newItem.stok_sistem}</td>
            <td>
                <input 
                    type="text" 
                    class="form-control doco-decimal input-sm typeahead text-right event-so stok-fisik" 
                    name="test" 
                    value="${newItem.stok_fisik}"
                    autocomplete="off">
            </td>
            <td class="text-center" id="totalSelisihSection">${newItem.stok_selisih}</td>
            <td>
                <input 
                    type="text" 
                    class="form-control doco-decimal input-sm typeahead text-right event-so revisi_edit" 
                    name="test" 
                    value="${newItem.revisi_stok}"
                    autocomplete="off">
            </td>
            <td class="text-center"><p class="selisih-revisi">${newItem.selisih_akhir}</p></td>
            <td class="text-center"><button class="btn btn-sm btn-danger btn-hapus" data-index="${newItem.barang_id}"><i class="fa fa-minus"></i></button></td>
        </tr>
    `;
    
    $(".new-row").before(str_tr);

    if(isBelumInputHasil == true) {
        $(`[data-parent='${newItem.barang_id}']`).find("input.stok-fisik").prop("disabled", false);
        $(`[data-parent='${newItem.barang_id}']`).find("input.revisi_edit").prop("disabled", true);
    } else {
        $(`[data-parent='${newItem.barang_id}']`).find("input.stok-fisik").prop("disabled", true);
        $(`[data-parent='${newItem.barang_id}']`).find("input.revisi_edit").prop("disabled", false);
    }
}

function clearNewForm() {
    newItem = {};
    $("#barang_id").val(null).trigger("change");
    $("#newKelompok").text("");
    $("#newSubKelompok").text("");
    $("#newStokSistem").text("");
    $("#newStokFisik").val("");
    $("#newStokSelisih").text("");
    $("#newRevisiStok").val("");
    $("#newSelisih").text("");
    $("#btn-tambah").attr("disabled", true);
}
