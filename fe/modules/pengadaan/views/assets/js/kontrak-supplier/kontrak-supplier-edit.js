/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @last modified by: Muhamad Lukman Hakim
 * @date : 2020-09-21
 */

var table_list_obat = null;
var list_obat = {};
var pajak_id = "";
var baseController = "/pengadaan/kontrak-supplier/";

function getAndConvertListObatToDatasArray() {
    let initCollections = [];
    $.each(list_obat, function (index, row) {
        initCollections.push(row);
    })
    const collectionDetails = collect(initCollections)
    const sorted = collectionDetails.sortByDesc('client_updated_at');
    const datas = sorted.all();
    let counterNum = 1;
    datas.map((row, index) => {
        datas[index].rowNum = counterNum;
        datas[index].is_first = false;
        counterNum++;
    })
    return datas;
}

function initDatatable() {
    let datas = getAndConvertListObatToDatasArray();
    datas.unshift({ rowNum: '', kode_obat: null, nama_obat: null, uom_text: null, harga_order: null, pengurang: null, total_harga: null, is_first: true });
    table_list_obat = null;
    table_list_obat = $('#datatable-obat-kontrak-supplier').DataTable().clear().destroy();
    table_list_obat = $('#datatable-obat-kontrak-supplier').docoTabel({
        filter: true,
        data: datas,
        order: [[0, "asc"]],
        displayLength: 10,
        processing: false,
        serverSide: false,
        scrollX: true,
        columns: [
            {
                title: "Kode Obat",
                data: "kode_obat",
                orderable: false,
                searchable: true,
                render: function (data, type, row) {
                    const selectKodeObat = `
                        <div>
                            <select name="kode_obat" id="kode_obat" class="form-control select2" style="width: 100%"></select>
                        <div>
                        <span>&nbsp;</span>`;
                    let kodeObat = selectKodeObat;
                    if (!row.is_first) kodeObat = row.kode_obat
                    return kodeObat;
                }
            },
            {
                title: "Nama Obat",
                data: "nama_obat",
                orderable: false,
                searchable: true,
                render: function (data, type, row) {
                    const selectNamaObat = `
                        <div>
                            <select name="nama_obat" id="nama_obat" class="form-control select2" style="width: 100%"></select>
                        </div>
                        <span>&nbsp;</span>`;
                    let namaObat = selectNamaObat
                    if (!row.is_first) namaObat = row.nama_obat
                    return namaObat;
                }
            },
            {
                title: "Unit Of Measurement",
                data: "uom_text",
                orderable: false,
                searchable: true,
                render: function (data, type, row) {
                    const selectUom = `
                        <div>
                            <select name="uom" id="uom" class="form-control" style="width: 100%" disabled="true"></select>
                        </div>
                        <span>&nbsp;</span>`;
                    let uom = selectUom
                    if (!row.is_first) uom = row.uom_text
                    return uom;
                }
            },
            {
                title: "Harga Order",
                data: "harga_order",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const rowKey = row.obatalkes_id;
                    let elementHargaOrder = `harga_order_${rowKey}`;
                    let elementPengurang = `pengurang_${rowKey}`;
                    let elementPengurangRp = `pengurang_rp_${rowKey}`;
                    let elementTotalHarga = `total_harga_${rowKey}`;
                    const inputHargaConverted = docoHelper.convertToRupiah(row.harga_order);
                    const inputPengurangConverted = docoHelper.convertToRupiah(row.pengurang);
                    const inputTotalHargaConverted = docoHelper.convertToRupiah(row.total_harga);
                    const inputTextHargaOrder = `
                        <div class="input-group">
                            <span class="input-group-addon">Rp</span>
                            <input
                                type="text"
                                name="harga_order"
                                class="form-control harga_order text-right"
                                id="harga_order"
                                autocomplete="off"
                                value="0"
                                style="width: 100%">
                        </div>
                        <span>&nbsp;</span>
                    `;
                    const inputTextHargeOrderDynamic = `
                        <div class="input-group">
                            <span class="input-group-addon">Rp</span>
                            <input
                                type="text"
                                class="form-control text-right"
                                id="${elementHargaOrder}"
                                autocomplete="off"
                                value="${inputHargaConverted}"
                                data-row-key="${rowKey}"
                                style="width: 100%">
                        </div>
                        <span>&nbsp;</span>
                        `;
                    let hargaOrder = inputTextHargaOrder;
                    if (!row.is_first) hargaOrder = inputTextHargeOrderDynamic;
                    return hargaOrder;
                }
            },
            {
                title: "Pengurang",
                data: "pengurang",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const rowKey = row.obatalkes_id;
                    let elementHargaOrder = `harga_order_${rowKey}`;
                    let elementPengurang = `pengurang_${rowKey}`;
                    let elementPengurangRp = `pengurang_rp_${rowKey}`;
                    let elementTotalHarga = `total_harga_${rowKey}`;
                    const inputHargaConverted = docoHelper.convertToRupiah(row.harga_order);
                    const inputPengurangConverted = docoHelper.convertToRupiah(row.pengurang);
                    const inputTotalHargaConverted = docoHelper.convertToRupiah(row.total_harga);
                    const inputTextPengurang = `
                        <div class="input-group">
                            <input
                                type="text"
                                name="pengurang"
                                class="form-control input-sm text-right pengurang"
                                id="pengurang"
                                autocomplete="off"
                                value="0"
                                style="width: 100%">
                            <span class="input-group-addon">%</span>
                        </div>
                        <div class="persen_to_rupiah text-right">
                            Rp <span id="pengurang_rp">0</span>
                        </d>
                    `;
                    const inputTextPengurangDynamic = `
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control input-sm text-right pengurang"
                                id="${elementPengurang}"
                                autocomplete="off"
                                value="${inputPengurangConverted}"
                                data-row-key="${rowKey}"
                                style="width: 100%">
                            <span class="input-group-addon">%</span>
                        </div>
                        <div class="persen_to_rupiah text-right">
                            Rp <span id="${elementPengurangRp}">0</span>
                        </div>
                    `;
                    let pengurang = inputTextPengurang;
                    if (!row.is_first) pengurang = inputTextPengurangDynamic;
                    return pengurang;
                }
            },
            {
                title: "Total Harga",
                data: "total_harga",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const rowKey = row.obatalkes_id;
                    let elementHargaOrder = `harga_order_${rowKey}`;
                    let elementPengurang = `pengurang_${rowKey}`;
                    let elementPengurangRp = `pengurang_rp_${rowKey}`;
                    let elementTotalHarga = `total_harga_${rowKey}`;
                    const inputHargaConverted = docoHelper.convertToRupiah(row.harga_order);
                    const inputPengurangConverted = docoHelper.convertToRupiah(row.pengurang);
                    const inputTotalHargaConverted = docoHelper.convertToRupiah(row.total_harga);
                    const spanTotalHarga = `<div class="text-right">Rp.<span id="total_harga" class="text-right">0</span></div>`;
                    const spanTotalHargaDynamic = `<div class="text-right">Rp.<span id="${elementTotalHarga}" class="text-right">${inputTotalHargaConverted}</span></div>`;
                    let totalHarga = spanTotalHarga;
                    if (!row.is_first) totalHarga = spanTotalHargaDynamic;
                    return totalHarga;
                }
            },
            {
                title: "Terakhir Update Pada",
                data: "last_updated_time",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let lastUpdatedAt = row.last_updated_time;
                    if (lastUpdatedAt) {
                        lastUpdatedAt = convertDateByFormat(lastUpdatedAt, 'd-m-y (h:i)');
                    } else {
                        lastUpdatedAt = '';
                    }
                    return lastUpdatedAt;
                }
            },
            {
                title: "Aksi",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const rowKey = row.obatalkes_id;
                    const buttonRemoveHtml = `<button class="btn btn-sm btn-danger btn-hapus" data-index="${rowKey}"><i class="fa fa-close"></i></button>`;
                    const buttonAddHtml = `<button class="btn btn-sm btn-success" id="btn-tambah"><i class="fa fa-plus"></i></button>`;
                    let buttonHtml = buttonRemoveHtml;
                    if (row.is_first) buttonHtml = buttonAddHtml;
                    return buttonHtml;
                }
            },
        ],
        drawCallback: function (settings) {
            initAfterDraw();
            setTimeout(()=>{
                // Override showing entries
                const info = table_list_obat.page.info();
                const start = info.start + 1;
                const end = info.end - 1;
                const total = info.recordsTotal - 1;
                $(`#datatable-obat-kontrak-supplier_wrapper .dataTables_info`).html(`Showing ${start} to ${end} of ${total} entries`);
            }, 10)
        },
        createdRow: function( row, data, dataIndex ) {
            let isNonActive = data.aktif_obat;
            if (isNonActive === false) {
                $(row).addClass('not_active');
            }
            $('td:eq(0)', row).css('min-width', '200px');
            $('td:eq(1)', row).css('min-width', '500px');
            $('td:eq(2)', row).css('min-width', '200px');
            $('td:eq(3)', row).css('min-width', '250px');
            $('td:eq(4)', row).css('min-width', '150px');
        },
    });
}

function initAfterDraw() {
    initHandlerHeaderInput();
    initHandlerRowInput();
    let datas = getAndConvertListObatToDatasArray();
    datas.map((row, index) => {
        const rowKey = row.obatalkes_id;
        let elementHargaOrder = `#harga_order_${rowKey}`;
        let elementPengurang = `#pengurang_${rowKey}`;
        let elementPengurangRp = `#pengurang_rp_${rowKey}`;
        let elementTotalHarga = `#total_harga_${rowKey}`;
        runInitAppendEventLitener({ elementHargaOrder, elementPengurang, elementPengurangRp, elementTotalHarga });
        handlerSumTotal({
            elementHargaOrder,
            elementPengurang,
            elementPengurangRp,
            elementTotalHarga,
            isEdited: false
        });
    })
}

function appendObat(data, isInitial = false) {
    $("#table-obat-kontrak-supplier tbody").html("");
    let initCollections = [];
    $.each(data, function (index, row) {
        initCollections.push(row);
    })
    const collectionDetails = collect(initCollections)
    const sorted = collectionDetails.sortByDesc('client_updated_at');
    const datas = sorted.all();
    datas.map((row, index) => {
        const rowKey = row.obatalkes_id;
        if (row == undefined) {
            return;
        }
        const isActiveObat = row.aktif_obat;
        let classRowChild = ``;
        if (!isActiveObat) classRowChild = `${classRowChild} not_active`;
        const inputHargaConverted = docoHelper.convertToRupiah(row.harga_order);
        const inputPengurangConverted = docoHelper.convertToRupiah(row.pengurang);
        let lastUpdatedAt = row.last_updated_time;
        if (lastUpdatedAt) {
            lastUpdatedAt = convertDateByFormat(lastUpdatedAt, 'd-m-y (h:i)');
        } else {
            lastUpdatedAt = '';
        }
        let elementHargaOrder = `harga_order_${rowKey}`;
        let elementPengurang = `pengurang_${rowKey}`;
        let elementPengurangRp = `pengurang_rp_${rowKey}`;
        let elementTotalHarga = `total_harga_${rowKey}`;
        const inputTextHarga = `
            <div class="input-group">
                <span class="input-group-addon">Rp</span>
                <input
                    type="text"
                    class="form-control text-right"
                    id="${elementHargaOrder}"
                    autocomplete="off"
                    value="${inputHargaConverted}"
                    data-row-key="${rowKey}"
                    style="width: 100%">
            </div>`;
        const inputTextPengurang = `
            <div class="input-group">
                <input
                    type="text"
                    class="form-control input-sm text-right pengurang"
                    id="${elementPengurang}"
                    autocomplete="off"
                    value="${inputPengurangConverted}"
                    data-row-key="${rowKey}"
                    style="width: 100%">
                <span class="input-group-addon">%</span>
            </div>
            <span class="persen_to_rupiah">
                Rp <span id="${elementPengurangRp}">0</span>
            </span>
        `;
        var str_tr = "";
        str_tr += `<tr class='${classRowChild}'>`;
        str_tr += `<td>${row.kode_obat}</td>`;
        str_tr += "<td>" + row.nama_obat + "</td>";
        str_tr += "<td>" + row.uom_text + "</td>";
        str_tr += `<td class='text-right'>${inputTextHarga}</td>`;
        str_tr += "<td class='text-right'>" + inputTextPengurang + "</td>";
        str_tr += `<td class='text-right'><span id='${elementTotalHarga}'>Rp ` + docoHelper.convertToRupiah(row.total_harga) + "</span></td>";
        str_tr += `<td class='text-center'>${lastUpdatedAt}</td>`;
        str_tr += `<td><button class="btn btn-sm btn-danger btn-hapus"
                data-index="`+ rowKey + `"><i class="fa fa-close"></i></button></td>`;
        str_tr += "</tr>";
        $("#table-obat-kontrak-supplier tbody").append(str_tr);
        elementHargaOrder = `#harga_order_${rowKey}`;
        elementPengurang = `#pengurang_${rowKey}`;
        elementPengurangRp = `#pengurang_rp_${rowKey}`;
        elementTotalHarga = `#total_harga_${rowKey}`;
        runInitAppendEventLitener({ elementHargaOrder, elementPengurang, elementPengurangRp, elementTotalHarga });
        if (isInitial) {
            handlerSumTotal({
                elementHargaOrder,
                elementPengurang,
                elementPengurangRp,
                elementTotalHarga,
                isEdited: false
            });
        }
    })
}

$(`input[name="KontrakSupplierForm[kontraksupplier_no]"]`).on("blur", function () {
    var no_kontraksupplier = $(`input[name="KontrakSupplierForm[kontraksupplier_no]"]`).val();
    var is_exist_no_kontrak = list_no_kontraksupplier.indexOf(no_kontraksupplier);
    if (is_exist_no_kontrak != -1) {
        docoNotification('warning', "Data Sudah Ada", "No Kontrak Supplier sudah ada");
        return false;
    }
});

$("#supplier_id").on('select2:select', function (e) {
    pajak_id = e.params.data.pajak_id == null ? "" : e.params.data.pajak_id;
    $("#pajak_id").val(pajak_id).trigger("change");
});

$("#btn-simpan").on('click', function () {
    var no_kontraksupplier = $(`input[name="KontrakSupplierForm[kontraksupplier_no]"]`).val();
    var is_exist_no_kontrak = list_no_kontraksupplier.indexOf(no_kontraksupplier);
    if (is_exist_no_kontrak != -1) {
        docoNotification('warning', "Data Sudah Ada", "No Kontrak Supplier sudah ada");
        return false;
    }
    if ($.isEmptyObject(list_obat)) {
        docoNotification('warning', "Data Tidak Lengkap", "List obat kosong");
        return false;
    }
    var _data = $("#create-kontrak-supplier-form").serializeArray();
    _data.push({
        name: "list_obat",
        value: JSON.stringify(list_obat)
    });
    $(this).docoForm("click", {
        url: baseController + "save",
        data: _data,
        success: function (data) {
            (new PNotify({
                title: "Berhasil",
                text: "Kontrak Supplier dengan Nomor " + "<strong>" + data.response.data.no_kontrak_supplier + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan input data baru?",
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
            })).get().on('pnotify.confirm', function () {
                location.reload();
            }).on('pnotify.cancel', function () {
                window.location.replace(baseController);
            });
        }
    });
})

$("#btn-edit").on('click', function () {
    if ($.isEmptyObject(list_obat)) {
        docoNotification('warning', "Data Tidak Lengkap", "List obat kosong");
        return false;
    }
    delete list_obat["undefined"];
    var _data = $("#update-kontrak-supplier-form").serializeArray();
    _data.push(
        {
            name: "KontrakSupplierForm[kontraksupplier_id]",
            value: kontraksupplier_id
        },
        {
            name: "list_obat",
            value: JSON.stringify(list_obat)
        }
    );
    $(this).docoForm("click", {
        url: baseController + "update?id=" + kontraksupplier_id,
        data: _data,
        success: function () {
            docoNotification('success', "Proses berhasil!", "Data telah diperbaharui.");
            location.reload();
        }
    });
})

function isObatValid() {
    const isObatNull = ($(`select[name="nama_obat"]`).val() == null) && ($(`select[name="kode_obat"]`).val() == null)
    if (isObatNull) {
        docoNotification('warning', "Data Tidak Lengkap", "Obat belum terpilih");
        return false;
    }
    if ($(`select[name="nama_obat"]`).val() in list_obat) {
        docoNotification('warning', "Data Sudah Ada", "Obat sudah ada pada list");
        return false;
    }
    if ($(`select[name="uom"]`).val() == null) {
        docoNotification('warning', "Data Tidak Lengkap", "Satuan belum terpilih");
        return false;
    }
    return true;
}

function resetFormObat() {
    $("#kode_obat").val(null).trigger("change");
    $("#kode_obat").focus();
    $("#nama_obat").val(null).trigger("change");
    $("#nama_obat").focus();
    $("#uom").val(null).trigger("change");
    $("#uom").prop("disabled", true);
    $("#harga_order").val(0);
    $("#pengurang").val(0);
    $("#total_harga").text(0);
    $("#pengurang_rp").text(0);
}

function sumTotalHarga() {
    var harga_order = docoHelper.convertToAngka($("#harga_order").val());
    var persen_pengurang = docoHelper.convertToDecimal($("#pengurang").val());
    var pengurang = harga_order * persen_pengurang / 100;
    var total = harga_order - pengurang;
    total = docoHelper.convertToRupiah(total);
    pengurang = docoHelper.convertToRupiah(pengurang);
    $("#total_harga").text(total);
    $("#pengurang_rp").text(pengurang);
}

function initHandlerRowInput() {
    $(`select[name="uom"]`).select2();
    $(`select[name="kode_obat"]`).select2({
        language: {
            errorLoading: function () { return "Please Wait .." }
        },
        placeholder: "Kode Obat",
        minimumInputLength: 2,
        ajax: {
            url: baseController + 'search-obat?code_only=true',
            dataType: 'json',
            quietMillis: 250,
            delay: 250,
        },
    });
    $(`select[name="kode_obat"]`).on('select2:select', function (e) {
        $("#stok_fisik").html("0");
        $(`select[name="uom"]`).find('Option').remove();
        var data_select = e.params.data;
        var data_satuan = data_select.satuan;
        var satuan_results = [];
        $.each(data_satuan, function (index, value) {
            var newOpt = new Option(value.lbl, value.sbid, false, false);
            newOpt.setAttribute('data-konversi', value.konv);
            $(`select[name="uom"]`).append(newOpt).trigger('change');
        });
        $(`select[name="uom"]`).trigger('change');
        $(`select[name="uom"]`).prop('disabled', false);
        // Inject Value Kode
        const newOptNameObat = new Option(data_select.nama_obat, data_select.id, true, true);
        $(`select[name="nama_obat"]`).append(newOptNameObat).trigger('change');
    });
    $(`select[name="nama_obat"]`).select2({
        language: {
            errorLoading: function () { return "Please Wait .." }
        },
        placeholder: "Nama Obat",
        minimumInputLength: 2,
        ajax: {
            url: baseController + 'search-obat?name_only=true',
            dataType: 'json',
            quietMillis: 250,
            delay: 250,
        },
    });
    $(`select[name="nama_obat"]`).on('select2:select', function (e) {
        $("#stok_fisik").html("0");
        $(`select[name="uom"]`).find('Option').remove();
        var data_select = e.params.data;
        var data_satuan = data_select.satuan;
        var satuan_results = [];
        $.each(data_satuan, function (index, value) {
            var newOpt = new Option(value.lbl, value.sbid, false, false);
            newOpt.setAttribute('data-konversi', value.konv);
            $(`select[name="uom"]`).append(newOpt).trigger('change');
        });
        $(`select[name="uom"]`).trigger('change');
        $(`select[name="uom"]`).prop('disabled', false);
        // Inject Value Name
        const newOptKode = new Option(data_select.kode_obat, data_select.id, true, true);
        $(`select[name="kode_obat"]`).append(newOptKode).trigger('change');
    });
    $(`.btn-hapus`).on('click', function () {
        var button = $(this);
        var index = button.attr('data-index');
        delete list_obat[index];
        // appendObat(list_obat);
        initDatatable();
    });
    $(`#btn-tambah`).on('click', function () {
        var select_nama_obat = $(`select[name="nama_obat"]`).select2('data')[0];
        var select_kode_obat = $(`select[name="kode_obat"]`).select2('data')[0];
        var select_uom = $(`select[name="uom"]`).select2('data')[0];
        if (!isObatValid()) return false;
        var obat_id = select_nama_obat.id;
        var obat_nama = select_nama_obat.text;
        var obat_kode = select_kode_obat.text;
        var uom_id = select_uom.id;
        var uom_text = select_uom.text;
        var harga_order = docoHelper.convertToAngka($("#harga_order").val());
        var pengurang = docoHelper.convertToAngka($("#pengurang").val());
        var total_harga = docoHelper.convertToAngka($("#total_harga").text());
        var newData = {
            nama_obat: obat_nama,
            kode_obat: obat_kode,
            obatalkes_id: obat_id,
            qty_minimum: 1,
            uom_id: uom_id,
            uom_text: uom_text,
            harga_order: harga_order,
            pengurang: pengurang,
            penambah: 0,
            total_harga: total_harga,
            aktif_obat: true,
            last_updated_time: null,
            is_edited: false,
            client_updated_at: getCurrentTimeInt()
        };
        list_obat[obat_id] = newData;
        // appendObat(list_obat);
        resetFormObat();
        initDatatable();
    });
}

function initHandlerHeaderInput() {
    $("#harga_order").on("focus", function () {
        if (this.value == 0) {
            this.value = "";
        }
    });
    $("#harga_order").on("blur", function () {
        if (this.value == "") {
            this.value = 0;
        }
    });
    $("#harga_order").on("input", function () {
        match = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
        this.value = match[1] + match[2];
        var angka = this.value;
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
        this.value = _value;
        sumTotalHarga();
    });
    $("#pengurang").on("focus", function () {
        if (this.value == 0) {
            this.value = "";
        }
    });
    $("#pengurang").on("blur", function () {
        if (this.value == "") {
            this.value = 0;
        }
    });
    $("#pengurang").on("input", function () {
        match = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
        this.value = match[1] + match[2];
        if (this.value > 100) {
            this.value = 100;
        }
        sumTotalHarga();
    });
}

function setListObatHarga(rowKey, values) {
    let currentListObat = list_obat[rowKey]
    let { harga_order, pengurang, total_harga, is_edited } = values
    if (harga_order === null) harga_order = currentListObat.harga_order
    if (pengurang === null) pengurang = currentListObat.pengurang
    if (total_harga === null) total_harga = currentListObat.total_harga
    if (is_edited === null) is_edited = currentListObat.is_edited
    const newCurrentListObat = {
        ...currentListObat,
        harga_order: harga_order,
        pengurang: pengurang,
        total_harga: total_harga,
        is_edited: is_edited,
        client_updated_at: getCurrentTimeInt()
    };
    list_obat[rowKey] = newCurrentListObat;
}

function runInitAppendEventLitener({ elementHargaOrder, elementPengurang, elementPengurangRp, elementTotalHarga }) {
    // Element Harga Order
    $(document).on('focus', elementHargaOrder, function () {
        const currentElement = $(elementHargaOrder);
        if (currentElement.val() == 0) currentElement.val("");
    });
    $(document).on('blur', elementHargaOrder, function () {
        const currentElement = $(elementHargaOrder);
        if (currentElement.val() == "") currentElement.val(0);
    });
    $(document).on('input', elementHargaOrder, function () {
        handlerOnlyDecimal(elementHargaOrder);
        handlerSumTotal({
            elementHargaOrder,
            elementPengurang,
            elementPengurangRp,
            elementTotalHarga,
            isEdited: true
        });
    });
    // Element Pengurang
    $(document).on('focus', elementPengurang, function () {
        const currentElement = $(elementPengurang);
        if (currentElement.val() == 0) currentElement.val("");
    });
    $(document).on('blur', elementPengurang, function () {
        const currentElement = $(elementPengurang);
        if (currentElement.val() == "") currentElement.val(0);
    });
    $(document).on('input', elementPengurang, function () {
        isEdited = true;
        const currentElement = $(elementPengurang);
        match = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(currentElement.val().replace(/[^\d,]/g, ""));
        let newValue = match[1] + match[2];
        if (newValue > 100) newValue = 100;
        currentElement.val(newValue);
        handlerSumTotal({
            elementHargaOrder,
            elementPengurang,
            elementPengurangRp,
            elementTotalHarga,
            isEdited: true
        });
    });
}

function handlerOnlyDecimal(elementIdentifier) {
    let currentThis = $(elementIdentifier);
    const currentValueReplaced = currentThis.val().replace(/[^\d,]/g, "");
    const match = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(currentValueReplaced);
    const newValue = match[1] + match[2];
    currentThis.val(newValue);
    let angka = newValue;
    let number_string = angka.toString().toString().replace(/\./g, ',');
    let split = number_string.split(',');
    let absvalue = split[0];
    let _split = split[0].replace(/\-/g, '');
    let sisa = _split.length % 3;
    let rupiah = _split.substr(0, sisa)
    let ribuan = _split.substr(sisa).match(/\d{1,3}/gi);
    let simbol = absvalue.match(/\-/gm);
    simbol = simbol == null ? '' : simbol;
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    let _value = simbol + (split[1] != undefined ? rupiah + ',' + split[1] : rupiah);
    if (_value == 'NaN') {
        _value = 0;
    }
    currentThis.val(_value);
}

function handlerSumTotal({
    elementHargaOrder,
    elementPengurang,
    elementPengurangRp,
    elementTotalHarga,
    isEdited
}) {
    const rowKey = $(elementHargaOrder).attr(`data-row-key`);
    let harga_order = $(elementHargaOrder).val()
      ? docoHelper.convertToAngka($(elementHargaOrder).val())
      : '';
    let persen_pengurang = $(elementPengurang).val()
      ? docoHelper.convertToDecimal($(elementPengurang).val())
      : '';
    let pengurang = harga_order * persen_pengurang / 100;
    let total = harga_order - pengurang;
    const totalRupiah = docoHelper.convertToRupiah(total);
    pengurang = docoHelper.convertToRupiah(pengurang);
    $(elementTotalHarga).text(totalRupiah);
    $(elementPengurangRp).text(pengurang);
    const updatedVal = {
        harga_order,
        pengurang: persen_pengurang,
        total_harga: total,
        is_edited: isEdited
    };
    setListObatHarga(rowKey, updatedVal)
}

$(document).ready(function () {
    initDatatable();
    $("#supplier_id").select2({
        placeholder: "Pilih Supplier",
        minimumInputLength: 3,
        ajax: {
            url: baseController + "search-supplier",
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
    $("#pajak_id").select2();
    if (!$.isEmptyObject(list_detail)) {
        $.each(list_detail, function (index, row) {
            if (row == undefined) return;
            var obat_id = row.obatalkes_id;
            var obat_nama = row.nama_obat;
            var obat_kode = row.kode_obat;
            var uom_id = row.uom_id;
            var uom_text = row.uom_text;
            var qty_minimum = 1;
            var harga_order = row.harga;
            var pengurang = row.pengurang;
            var penambah = 0;
            var total_harga = row.total_harga;
            var aktif_obat = row.aktif_obat;
            var last_updated_time = row.last_updated_time;
            var kontraksupplierdetail_id = row.kontraksupplierdetail_id;
            var newData = {
                kontraksupplierdetail_id: kontraksupplierdetail_id,
                nama_obat: obat_nama,
                kode_obat: obat_kode,
                obatalkes_id: obat_id,
                qty_minimum: qty_minimum,
                uom_id: uom_id,
                uom_text: uom_text,
                harga_order: harga_order,
                pengurang: pengurang,
                penambah: penambah,
                total_harga: total_harga,
                aktif_obat: aktif_obat,
                last_updated_time: last_updated_time,
                is_edited: false,
                client_updated_at: getCurrentTimeInt()
            };

            list_obat[obat_id] = newData;
            // appendObat(list_obat, true);
        });
    }
    initDatatable();
});
