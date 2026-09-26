var table;
$(() => {
    const _baseUrl = baseUrl + "gudang/laporan-stock-mutasi";
    table = $("#laporan-stock-mutasi").docoTabel({
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
                title: "Tanggal",
                data: "tanggal_inventory",
                visible: false,
                orderable: false
            },
            {
                title: "Kode Obat",
                data: "obatalkes_kode",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Nama Obat Alkes",
                data: "obatalkes_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Jenis Obat Alkes",
                data: "jenisobatalkes_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Manufaktur",
                data: "manufaktur_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Ruangan",
                data: "ruangan_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "UoM",
                data: "uom",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "HNA",
                data: "hna",
                className: "text-right",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Total Qty Awal",
                data: "qty_total_awal",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Total Value Awal",
                data: "total_nilai_awal",
                className: "text-right",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Total Qty Received",
                data: "qtystok_in",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Total Value Received",
                data: "total_nilai_diterima",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Total Qty Usage",
                data: "qtystok_out",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Total Value Usage",
                data: "total_nilai_keluar",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Total Qty Akhir",
                data: "total_qty_akhir",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Total Value Akhir",
                data: "total_nilai_akhir",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Turn Over",
                data: "turn_over",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            }
        ],
        formFilters: [
            {
                fieldName: "tanggal_inventory",
                label: "Tanggal Inventory",
                type: {
                    name: "rangeDate",
                },
            },
            {
                fieldName: 'ruangan_nama',
                label: 'Ruangan',
                type: {
                    name: 'select',
                    payload: filters.instalasi_ruangan
                }
            },
            {
                fieldName: 'obatalkes_nama',
                label: 'Nama Obat Alkes',
                type: {
                    name: 'select',
                    payload: filters.jenis_obatalkes
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
        $(`#laporan-stock-mutasi`).DataTable().context[0].ajax.data.advancedFilter =
            serializeArrayToJson($(`#form-filter__laporan-stock-mutasi`));
    });

    $(".flex-1").css("display", "none");
    
    if ($(this).find("row row__hidden")) {
        $("#filter-section__laporan-stock-mutasi .row__hidden").removeClass("row__hidden");
    }

    $(document).on('keypress',function(e) {
        if(e.which == 13) {
            $(".btn-search--datatable").trigger("click");
        }
    });
});