// Global Var
var { details } = phpVars

function initDatatable() {
    let datas = details;
    $('#datatable-obat-kontrak-supplier').DataTable().clear().destroy();
    $('#datatable-obat-kontrak-supplier').docoTabel({
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
                searchable: true
            },
            {
                title: "Nama Obat",
                data: "nama_obat",
                orderable: false,
                searchable: true
            },
            {
                title: "Unit Of Measurement",
                data: "uom_text",
                orderable: false,
                searchable: true
            },
            {
                title: "Harga Order",
                data: "harga",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let harga = row.harga;
                    const renderHarga = `<div class='text-right'>${harga}</div>`;
                    return renderHarga;
                }
            },
            {
                title: "Pengurang",
                data: "pengurang",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let pengurang = row.pengurang;
                    pengurang = `${pengurang} %`;
                    let pengurangRp = row.pengurang_rp;
                    const renderPengurang = `
                        <div class='text-bold text-right'>${pengurangRp}</div>
                        <div class='text-xs text-right'>${pengurang}</div>
                    `;
                    return renderPengurang;
                }
            },
            {
                title: "Total Harga",
                data: "total_harga",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let totalHarga = row.total_harga;
                    const renderTotalHarga = `<div class='text-right'>${totalHarga}</div>`;
                    return renderTotalHarga;
                }
                
            },
            {
                title: "Terakhir Update Pada",
                data: "last_updated_time",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return row.last_updated_time;
                }
            },
        ],
        drawCallback: function (settings) {},
        createdRow: function( row, data, dataIndex ) {
            let isNonActive = data.aktif_obat;
            if (isNonActive === false) {
                $(row).addClass('not_active');
            }
        },
    });
}

initDatatable();