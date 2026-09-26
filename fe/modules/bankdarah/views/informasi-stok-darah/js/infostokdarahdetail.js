var table;
$(document).ready(function(){
    table = $("#example").docoTabel({
        filter: false,
        sorting: [[2, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"bankdarah/informasi-stok-darah/get-detail?id="+_id,
        columns : [

        { // 1
            title: "No.",
            data: "rowNum",
            searchable: false,
            orderable: false
        },
        {
            title: "No. Kantong",
            data: "no_kantongdarah",
        },
        {
            title: "Jenis Darah",
            data: "jenisdarah_nama",
        },
        {
            title: "Golongan Darah",
            data: "golongandarah",
        },
        {
            title: "Rhesus",
            data: "rhesus",
        },
        {
            title: "Tanggal Kadaluarsa",
            data: "tgl_kadaluarsa",
        },
        {
            title: "Suhu Penyimpanan (C)",
            data: "suhu_penyimpanan",
        },
        ]
    });

    $(".dataTables_filter").hide();
});