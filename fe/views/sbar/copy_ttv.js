var tableCopyTtv
$(document).ready(function(){
    tableCopyTtv = $('#table-copy-ttv').docoTabel({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        sorting: [[1, "desc"]],
        ajax: {
            url: `/${modul}${url}/get-data-ttv?pendaftaran_id=${pendaftaranId}`,
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = tableCopyTtv.page.info();
                    return tableInfo.start + rowAdditionalData.row + 1;
                },
            },
            {
                name: 'tanggal_ttv',
                data: null,
                render: function(data, type, row) {
                    var date = new Date(row.tanggal_ttv);
                    var formattedDate = date.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }).replace(/\./g, ':');
                    return row.sumberttv + ' <br/>-----------------<br/> ' + formattedDate;
                }
            },
            {
                data: 'jenisttv'
            },
            {
                data: 'tingkatkesadaran'
            },
            {
                data: 'sistol'
            },
            {
                data: 'diastol'
            },
            {
                data: 'nadi'
            },
            {
                data: 'respirasi'
            },
            {
                data: 'spo2'
            },
            {
                data: 'suhu'
            },
            {
                data: 'tinggi_badan'
            },
            {
                data: 'berat_badan'
            },
            {
                data: 'gcs_e'
            },
            {
                data: 'gcs_v'
            },
            {
                data: 'gcs_m'
            },
        ],
    });

    $('#table-copy-ttv tbody').on('click', 'tr', function() {
        var data = tableCopyTtv.row(this).data();
        $('#sbarform-sistol').val(data.sistol);
        $('#sbarform-diastol').val(data.diastol);
        $('#sbarform-nadi').val(data.nadi);
        $('#sbarform-respirasi').val(data.respirasi);
        $('#sbarform-spo2').val(data.spo2);
        $('#sbarform-suhu').val(data.suhu);
        $('#sbarform-berat_badan').val(data.berat_badan);
        $('#sbarform-tinggi_badan').val(data.tinggi_badan);
        $('#modal_riwayat').modal('hide');
    })
})