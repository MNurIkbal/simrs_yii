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
        ajax: baseUrl+"rm/informasi/get-data?tipe=stok_opname",
        columns: [
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {data: "tglstokopname", name: "tglstokopname"},
            {data: "nostokopname", name: "nostokopname"},
            {data: "totalharga", name: "totalharga"},
            {data: "totalnetto", name: "totalnetto"},
            {data: "selisih", name: "selisih"},
        ],
        scrollCollapse: false,
    });
    $(".dataTables_filter").hide();
});

