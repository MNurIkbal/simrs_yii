// Tabel
var tabel;

// Initiate page
$(document).ready(function () {
    // Generate Table
    tabel = $("#tb-terra").docoTabel({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        paging: false,
        info: false,
        aaSorting: [],
        scrollY: true,
        ajax: baseUrl + "rajal/pemeriksaan/get-data-history-cppt-terra-medik?pasien_terra=" + pasien_terra,
        columns: [
            {
                title: "No",
                data: "no",
                searchable: false,
                orderable: false
            },
            { 
                title: "Ruang / Tanggal dan Jam / Profesi",
                data: "ruang", 
                searchable: false, 
                orderable: false },
            { 
                title: "Hasil Asesmen Penatalaksanaan Pasien",
                data: "soap", 
                searchable: false, 
                orderable: false },
            {
                title: "Instruksi DPJP Termasuk Pasca Bedah",
                data: "resep",
                searchable: false,
                orderable: false
            },
        ],
    });

    // Hide filter
    $(".dataTables_filter").hide();
});
