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
        stateSave: false,
        scrollX: true,
        ajax: baseUrl+"laporan/morbiditas/get-data",
        columns: [
            {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false,
                class: 'text-center',
            },
            {data: "dtd_kode", name: "dtd_kode", orderable: false},
            {data: "diagnosa_kode", name: "diagnosa_kode", orderable: false},
            {data: "diagnosa_nama", name: "diagnosa_nama", orderable: false},
            {data: "umur_0_6hr", name: "umur_0_6hr", orderable: false, class: 'text-center'},
            {data: "umur_0_6hr", name: "umur_0_6hr", orderable: false, class: 'text-center'},
            {data: "umur_7_28hr", name: "umur_7_28hr", orderable: false, class: 'text-center'},
            {data: "umur_7_28hr", name: "umur_7_28hr", orderable: false, class: 'text-center'},
            {data: "umur_28hr_<1thn", name: "umur_28hr_<1thn", orderable: false, class: 'text-center'},
            {data: "umur_28hr_<1thn", name: "umur_28hr_<1thn", orderable: false, class: 'text-center'},
            {data: "umur_1_4thn", name: "umur_1_4thn", orderable: false, class: 'text-center'},
            {data: "umur_1_4thn", name: "umur_1_4thn", orderable: false, class: 'text-center'},
            {data: "umur_1_4thn", name: "umur_1_4thn", orderable: false, class: 'text-center'},
            {data: "umur_1_4thn", name: "umur_1_4thn", orderable: false, class: 'text-center'},
            {data: "umur_15_24thn", name: "umur_15_24thn", orderable: false, class: 'text-center'},
            {data: "umur_15_24thn", name: "umur_15_24thn", orderable: false, class: 'text-center'},
            {data: "umur_25_44thn", name: "umur_25_44thn", orderable: false, class: 'text-center'},
            {data: "umur_25_44thn", name: "umur_25_44thn", orderable: false, class: 'text-center'},
            {data: "umur_45_64thn", name: "umur_45_64thn", orderable: false, class: 'text-center'},
            {data: "umur_45_64thn", name: "umur_45_64thn", orderable: false, class: 'text-center'},
            {data: "umur_>65thn", name: "umur_>65thn", orderable: false, class: 'text-center'},
            {data: "umur_>65thn", name: "umur_>65thn", orderable: false, class: 'text-center'},
            {data: "pasien_keluar_hidup_laki", name: "pasien_keluar_hidup_laki", orderable: false, class: 'text-center'},
            {data: "pasien_keluar_hidup_perempuan", name: "pasien_keluar_hidup_perempuan", orderable: false, class: 'text-center'},
            {data: "jumlah_pasien_keluar_hidup", name: "jumlah_pasien_keluar_hidup", orderable: false, class: 'text-center'},
        ],
        // scrollCollapse: true,
        // fixedColumns: {
        //     leftColumns: 4,
        // }
    });
    $(".dataTables_filter").hide();
});

