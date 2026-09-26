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
        ajax: baseUrl+"rm/informasi/get-data?tipe=pasien",
        columns: [
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {data: "tanggal_rekam_medik", name: "tanggal_rekam_medik"},
            {data: "no_rekam_medik", name: "no_rekam_medik"},
            {data: "nama_pasien", name: "nama_pasien"},
            {data: "jenis_kelamin", name: "jenis_kelamin"},
            {data: "alamat", name: "alamat"},
            {data: "tanggal_lahir", name: "tanggal_lahir"},
            {data: "umur", name: "umur"},
            {data: "nama_ibu_kandung", name: "nama_ibu_kandung"},
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

