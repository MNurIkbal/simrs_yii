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
        sort: false, 
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: true,
        scrollX: true,
        ajax: baseUrl+"rm/laporan/get-data?tipe=morbiditas",
        columns: [
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false,
                class: 'text-center',
            },
            {data: "no_dtd", name: "no_dtd", orderable: false},
            {data: "no_daftar", name: "no_daftar", orderable: false},
            {data: "golongan_sebab_penyakit", name: "golongan_sebab_penyakit", orderable: false},
            {data: "col_1.L", name: "col_1.L", orderable: false, class: 'text-center'},
            {data: "col_1.P", name: "col_1.P", orderable: false, class: 'text-center'},
            {data: "col_2.L", name: "col_2.L", orderable: false, class: 'text-center'},
            {data: "col_2.P", name: "col_2.P", orderable: false, class: 'text-center'},
            {data: "col_3.L", name: "col_3.L", orderable: false, class: 'text-center'},
            {data: "col_3.P", name: "col_3.P", orderable: false, class: 'text-center'},
            {data: "col_4.L", name: "col_4.L", orderable: false, class: 'text-center'},
            {data: "col_4.P", name: "col_4.P", orderable: false, class: 'text-center'},
            {data: "col_5.L", name: "col_5.L", orderable: false, class: 'text-center'},
            {data: "col_5.P", name: "col_5.P", orderable: false, class: 'text-center'},
            {data: "col_6.L", name: "col_6.L", orderable: false, class: 'text-center'},
            {data: "col_6.P", name: "col_6.P", orderable: false, class: 'text-center'},
            {data: "col_7.L", name: "col_7.L", orderable: false, class: 'text-center'},
            {data: "col_7.P", name: "col_7.P", orderable: false, class: 'text-center'},
            {data: "col_8.L", name: "col_8.L", orderable: false, class: 'text-center'},
            {data: "col_8.P", name: "col_8.P", orderable: false, class: 'text-center'},
            {data: "col_9.L", name: "col_9.L", orderable: false, class: 'text-center'},
            {data: "col_9.P", name: "col_9.P", orderable: false, class: 'text-center'},
            {data: "col_1_data_pasien_keluar.L", name: "col_1_data_pasien_keluar.L", orderable: false, class: 'text-center'},
            {data: "col_1_data_pasien_keluar.P", name: "col_1_data_pasien_keluar.P", orderable: false, class: 'text-center'},
            {data: "col_1_data_pasien_keluar_hidup.L", name: "col_1_data_pasien_keluar_hidup.L", orderable: false, class: 'text-center'},
        ],
        // scrollCollapse: true,
        // fixedColumns: {
        //     leftColumns: 4,
        // }
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table,[
        
    ]);
});

