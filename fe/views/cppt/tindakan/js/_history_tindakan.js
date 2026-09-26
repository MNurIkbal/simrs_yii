$(document).ready(function() {
    $('.data-filter').click(function() {
        tabel.draw();
    });

    tabel = $('#table-history-tindakan').docoTabel({
        processing: true,
        serverSide: true,
        filter: true,
        select: {
            style:    'os',
            selector: 'tr'
        },
        sorting: [[1, 'desc']],
        displayLength: 10,
        responsive: true,
        ajax: `${frontendUrl}/get-list-history-tindakan?no_pendaftaran=${no_pendaftaran}`,
        columns: [
            {
                width: '50px',
                title: 'No.',
                data: 'rowNum',
                searchable: false, orderable: false
            },
            { title:'Tanggal Tindakan', data: 'tgl_tindakan'},
            { title:'Nama Tindakan/Paket', data: 'tindakan_obat'},
            { title:'Dokter Pemeriksa', data: 'dokter_pemeriksa'},
            { title:'Dokter Delegasi', data: 'dokter_delegasi'},
            { title:'Perawat 1', data: 'perawat_1'},
            { title:'Perawat 2', data: 'perawat_2'},
            { title:'Qty', data: 'qty'},
            { title:'Keterangan', data: 'keterangan'}
        ],
    });
    $(".dataTables_filter").hide();
});
