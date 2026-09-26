/** 
*
* * created by Bambang Hermawan 
* * @juli 12 2022
*
*/

var table;

$(document).ready(function() {
    table = $("#table-perubahan-data-pasien").docoTabel({
        filter: false,
        sorting: [],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: `${baseUrl}pendaftaran/informasi-pencarian-pasien/get-data-riwayat-perubahan-data-pasien?id=`+id,
        columns: [
            {
                title: "No.",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            { 
                title: "Tanggal", 
                data: "tgl_ubahdata", 
                searchable: false 
            },
            { 
                title: "Nama Pegawai", 
                data: "pegawai_created",
                searchable: false 

            },
            { 
                title: "Alasan", 
                data: "alasan_ubahdata", 
                searchable: false 
            },
        ]
    });

    $(".dataTables_filter").hide();
});
