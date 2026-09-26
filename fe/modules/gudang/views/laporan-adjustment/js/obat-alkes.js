var table;
$(() => {
    const _baseUrl = baseUrl + "gudang/laporan-adjustment";
    table = $("#laporan-adjustment").docoTabel({
        filter: false,
        order: [
            [3, "desc"]
        ],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollY: false,
        ajax: {
            url: _baseUrl + "/get-data",
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
                title: "Ruangan",
                data: "ruangan_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "No. Transaksi",
                data: "no_adjusmen",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Tanggal Adjustment",
                data: "tgl_adjusmen",
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY");
                },
            },
            {
                title: "Jenis Adjustment",
                data: "jenis_adjusmen_nama",
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
                title: "Kode Obat Alkes",
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
                title: "Qty",
                data: "qty_input",
                className: "text-right",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Satuan",
                data: "satuan_besar",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Qty Konversi",
                data: "qty_konversi",
                className: "text-right",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Satuan Terkecil",
                data: "satuan_kecil",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Nama Pegawai",
                data: "pegawai_adjusmen",
                render: (data) => {
                    return data == "" || data == null ? "-" : data;
                },
            }
        ],
        formFilters: [
            {
                fieldName: "tgl_adjusmen",
                label: "Tanggal Adjustment",
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
                fieldName: 'jenis_adjusmen_nama',
                label: 'Jenis Adjustment',
                type: {
                    name: 'select',
                    payload: filters.jenis_adjustment
                }
            },
            {
                fieldName: 'jenisobatalkes_nama',
                label: 'Jenis Obat Alkes',
                type: {
                    name: 'select',
                    payload: filters.jenis_obatalkes
                }
            },
        ],
    });

    $(".btn-reset").on("click", function () {
        $(document).find("input").val("");
        $(document)
            .find("#tgl_adjusmen-startDate")
            .val(moment().format("DD-MMM-YYYY"))
            .trigger("change");
        $(document)
            .find("#tgl_adjusmen-endDate")
            .val(moment().format("DD-MMM-YYYY"))
            .trigger("change");
        $(`#laporan-adjustment`).DataTable().context[0].ajax.data.advancedFilter =
            serializeArrayToJson($(`#form-filter__laporan-adjustment`));
        $(`#laporan-adjustment`).DataTable().ajax.reload();
    });

    $(".flex-1").css("display", "none");
    
    if ($(this).find("row row__hidden")) {
        $("#filter-section__laporan-adjustment .row__hidden").removeClass("row__hidden");
    }
});
