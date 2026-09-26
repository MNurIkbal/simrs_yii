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
        success : function (data) {
            table.draw()
        }
    });
    return false;
});

// Event Ready
$(document).ready(function() {
    
    // Generate Table
    table = $("#example").docoTabel({
        filter: true,
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: true,
        scrollX: false,
        ajax: baseUrl+"rm/informasi/get-data?tipe=kunjungan",
        columns: [
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {data: "tanggal_pendaftaran", name: "tanggal_pendaftaran"},
            {data: "no_pendaftaran", name: "no_pendaftaran"},
            {data: "no_rekam_medik", name: "no_rekam_medik"},
            {data: "nama_pasien", name: "nama_pasien"},
            {data: "jenis_kelamin", name: "jenis_kelamin"},
            {data: "cara_bayar", name: "cara_bayar"},
            {data: "penjamin", name: "penjamin"},
            {data: "jenis_kasus_penyakit", name: "jenis_kasus_penyakit"},
            {data: "instalasi", name: "instalasi"},
            {data: "ruangan", name: "ruangan"},
            {data: "dokter_pj", name: "dokter_pj"},
        ],
        scrollCollapse: false,
    });
    $(".dataTables_filter").hide();
});

