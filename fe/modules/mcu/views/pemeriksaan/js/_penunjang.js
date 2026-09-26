var params = 'pendaftaran_id=' + pend_id;
var table_riwayatPenunjang;
var pemeriksaanlab = {};

$(document).ready(function() {
    var table_id = 'tabel-r';

    $("#form-penunjang").docoForm('submit', {
        success: function(data) {
            $("#tab-penunjang").trigger('click')
        }
    });

    table_riwayatPenunjang = $('#' + table_id).docoTabel({
        filter: false,
        displayLength: 10,
        select: {
            selector: 'tr'
        },
        sorting: [
            [1, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: baseUrl + 'mcu/pemeriksaan/get-data-riwayat?' + params,
        columns: [{
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            { data: 'instalasi_nama' },
            { data: 'nama_pemeriksaan' },
            { data: 'status' },
        ]
    });
});