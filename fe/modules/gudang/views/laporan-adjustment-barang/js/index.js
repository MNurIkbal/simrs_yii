var table;
$(() => {
    const _baseUrl = baseUrl + "gudang/laporan-adjustment-barang";
    table = $("#laporan-adjustment-barang").docoTabel({
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
            url: _baseUrl + "/get-data?tipe=barang",
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
            }
        ],
    });

    $(document).on("click", ".export-excel-laporan", (e) => {
        e.preventDefault();
        if (table.data().count() > 0) {
            var url = _baseUrl + "/export-excel";
            $.ajax({
                url: url,
                method: "post",
                data: $.param(table.ajax.params()),
                success: function (data) {
                    window.open(_baseUrl + "/download-excel?data=" + data, "_blank");
                },
            });
        } else {
            docoNotification("warning", "Terjadi Kesalahan", "Data Tidak Tersedia!");
        }
    });

    $(".btn-reset").on("click", function () {
        $(document).find("input").val("");
        $(document)
            .find("#tgl_po-startDate")
            .val(moment().format("DD-MMM-YYYY"))
            .trigger("change");
        $(document)
            .find("#tgl_po-endDate")
            .val(moment().format("DD-MMM-YYYY"))
            .trigger("change");
        $(`#example`).DataTable().context[0].ajax.data.advancedFilter =
            serializeArrayToJson($(`#form-filter__example`));
        $(`#example`).DataTable().ajax.reload();
    });

    $(".flex-1").css("display", "none");
    
    if ($(this).find("row row__hidden")) {
        $("#filter-section__example .row__hidden").removeClass("row__hidden");
    }
});
