// Global Var
var table;

// Event Reload
$(document).on("click", ".data-reload", function() {
    table.draw();
});

// Event Delete
$(document).on("click", ".data-delete", function(e) {
    e.preventDefault();

    $(this).docoForm("delete",{
        additional: 'data-rm',
        success : function (data) {
            table.draw()
        }
    });
    return false;
});

// Event Ready
$(document).ready(function() {
    $(".pickadate").pickadate();
    // Generate Table
    table = $("#example").docoTabel({
        filter: true,
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: true,
        scrollX: false,
        ajax: baseUrl+"rm/informasi/get-data?tipe=pemakaian_barang",
        columns: [
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {data: "tgl_pemakaianbarang", name: "tgl_pemakaianbarang"},
            {data: "nama_pegawai", name: "nama_pegawai"},
            {data: "barang_nama", name: "barang_nama"},
            {data: "jumlah_pakai", name: "jumlah_pakai"},
            {data: "barang_satuan", name: "barang_satuan"},
            {
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
        ],
        scrollCollapse: false,
    });
    $(".dataTables_filter").hide();
});

