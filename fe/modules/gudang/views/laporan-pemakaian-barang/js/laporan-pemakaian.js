var table;
$(() => {
    const _baseUrl = baseUrl + "gudang/laporan-pemakaian-barang";
    table = $("#laporan-pemakaian-barang").docoTabel({
        filter: false,
        order: [
            [3, "desc"]
        ],
        sorting: [[2, "asc"],[4, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        scrollX: true,
        scrollY: false,
        ajax: {
            url: _baseUrl + "/get-list-data",
            type: "POST",
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = table.page.info();
                    return tableInfo.start + rowAdditionalData.row + 1;
                },
            },
            {
                title: "Nama Ruangan",
                data: "ruangan_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Tanggal Transaksi",
                data: "tgl_transaksi",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "No Transaksi",
                data: "no_transaksi",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Kelompok Barang",
                data: "kelompokbarang_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Kode Barang",
                data: "barang_kode",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Nama Barang",
                data: "barang_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Qty",
                data: "qty",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Satuan",
                data: "satuan_kecil",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Harga Satuan (Rp)",
                data: "harga_netto",
                className: "text-right",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Total Harga (Rp)",
                data: "total_harga",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "User",
                data: "user",
                className: "text-right",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Catatan",
                data: "catatan",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            }
        ],
        formFilters: [
            {
                fieldName: "tgl_transaksi",
                label: "Tanggal Transaksi",
                type: {
                    name: "rangeDate",
                },
            },
            {
                fieldName: 'ruangan_nama',
                label: 'Ruangan',
                type: {
                    name: 'select',
                    payload: filters.ruangan_nama
                }
            },
            {
                fieldName: 'barang_nama',
                label: 'Nama Barang',
                type: {
                    name: 'select',
                    payload: filters.nama_barang
                }
            },
            {
                fieldName: 'barang_kode',
                label: 'Kode Barang',
                type: {
                    name: 'select',
                    payload: filters.kode_barang
                }
            },
            {
                fieldName: 'kelompokbarang_nama',
                label: 'Kelompok Barang',
                type: {
                    name: 'select',
                    payload: filters.kelompok_barang
                }
            },
        ],
    });

    $(".btn-reset").on("click", function () {
        $(document)
            .find("#tanggal_inventory-startDate")
            .val(moment().locale('en').format("DD-MMM-YYYY"))
            .trigger("change");
        $(document)
            .find("#tanggal_inventory-endDate")
            .val(moment().locale('en').format("DD-MMM-YYYY"))
            .trigger("change");
        $("select[name='ruangan_nama']").val("").trigger("change");
        $("select[name='obatalkes_nama']").val("").trigger("change");
        $(".btn-search--datatable").trigger("click");
        $(`#laporan-pemakaian-barang`).DataTable().context[0].ajax.data.advancedFilter =
            serializeArrayToJson($(`#form-filter__laporan-pemakaian-barang`));
    });

    $(".flex-1").css("display", "none");
    
    if ($(this).find("row row__hidden")) {
        $("#filter-section__laporan-pemakaian-barang .row__hidden").removeClass("row__hidden");
    }

    $(document).on('keypress',function(e) {
        if(e.which == 13) {
            $(".btn-search--datatable").trigger("click");
        }
    });
});