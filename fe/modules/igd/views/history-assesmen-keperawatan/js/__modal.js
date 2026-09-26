var params = 'pendaftaran_id=' + pendaftaran_id;

$(document).ready(function () {
    // Generate Table
    table = $('#table-history-assesmen').docoTabel({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        paging: true,
        ajax: baseUrl + 'igd/history-assesmen-keperawatan/get-data-history?' + params,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            { 
                title: 'Tanggal Assesmen',
                data: 'tgl_asesmen', 
                searchable: false, 
                orderable: false 
            },
            { 
                title: 'Petugas Assesmen',
                data: 'perawar_assesmen', 
                searchable: false, 
                orderable: false 
            },
            {
                title: 'Hasil Assesmen',
                data: 'hasil_assesmend',
                searchable: false,
                orderable: false,
                class: 'text-center'
            },
        ],
    });
});