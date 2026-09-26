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
        ajax: baseUrl+"rm/laporan/get-data?tipe=sirs",
        columns: [
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {data: "jenis_laporan", name: "jenis_laporan"},
            {data: "jumlah", name: "jumlah"},
        ],
        scrollCollapse: false,
    });
    $(".dataTables_filter").hide();
});

