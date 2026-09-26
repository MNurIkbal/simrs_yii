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
        ajax: baseUrl+"rm/informasi/get-data?tipe=dokumen",
        columns: [
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {data: "tglrekammedis", name: "tglrekammedis"},
            {data: "lokasirak_id", name: "lokasirak_id"},
            {data: "subrak_id", name: "subrak_id"},
            {data: "no_rekam_medik", name: "no_rekam_medik"},
            {data: "nama_pasien", name: "nama_pasien"},
            {data: "warnadokrm_id", name: "warnadokrm_id"},
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

