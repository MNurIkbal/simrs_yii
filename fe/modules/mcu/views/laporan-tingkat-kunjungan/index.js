var table;
$(document).on("click", ".data-reload", function () {
    table.draw();
});

$(document).ready(function(){
    table = $("#tbl-laporan-tingkat-kunjungan-mcu").docoTabel({
        filter: true,
        sorting: false,
        displayLength: 50,
        processing: true,
        serverSide: true,
        scrollY:true,
        ajax: baseUrl+"mcu/laporan-tingkat-kunjungan/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: 'Tanggal Registrasi', data: "tgl_pendaftaran", orderable:false}, 
            {data: "no_pendaftaran", searchable: false, orderable:false}, 
            {data: "no_rekam_medik", searchable: false, orderable:false}, 
            {data: "nama_pasien", searchable: false, orderable:false}, 
            {data: "tipepaket_nama", searchable: false, orderable:false}, 
            {data: "tarif_tindakan", searchable: false, orderable:false}, 
            {data: "penjamin_nama",searchable: false, orderable:false}, 
            {title: 'Tipe Paket', data: "tipepaket_kode", visible: false}, // 7
            {title: 'Payer', data: "penjamin_id", visible: false}, 
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [1, rangeRegDate],
        [8, dropDownPackageType],
        [9, dropDownPayer],
    ],
    {
        // object:position
        1:0,
        8:1,
        9:2
    });
    dateRangeHelper(".startDate",".endDate",".targetDate");
});