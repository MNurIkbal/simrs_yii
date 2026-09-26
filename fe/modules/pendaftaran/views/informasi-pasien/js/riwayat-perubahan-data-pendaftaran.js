/** 
*
* * created by Ardi Pratama
*
*/

var table;

$(document).ready(function() {
    table = $("#table-perubahan-data-pendaftaran").docoTabel({
        filter: false,
        sorting: [],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: `${baseUrl}pendaftaran/informasi-pasien/get-data-riwayat-perubahan-data-pendaftaran?id=`+primaryKey,
        columns: [
            {
                title: "No.",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            { 
                title: "Tanggal", 
                data: "tanggal", 
                searchable: false 
            },
            { 
                title: "Data Sebelum", 
                data: "data_sebelum",
                searchable: false 

            },
            { 
                title: "Data Sesudah", 
                data: "data_sesudah",
                searchable: false 

            },
            { 
                title: "User", 
                data: "user", 
                searchable: false 
            },
        ],
        rowCallback: function( row, data, index ) {
            $('td:eq(2)', row).css('white-space', 'pre-line');
            $('td:eq(3)', row).css('white-space', 'pre-line');
        }
    });

    $(".dataTables_filter").hide();
});
