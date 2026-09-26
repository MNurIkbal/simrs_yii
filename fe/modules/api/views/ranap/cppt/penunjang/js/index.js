var table = $('#table-riwayat').docoTabel({
    filter: false,
    columnDefs: [{
        className: 'text-center',
        targets: [6,7]
    }],
    order: [[0, 'desc']],
    displayLength: 10,
    processing: true,
    serverSide: true,
    ajax: '/api/ranap/get-data-history-surgery/index?norm=' + dataView.noRm + '&',
    columns: [
        {
            title: 'No',
            data: 'rowNum',
            orderable: false
        },
        {
            title: 'Nomor Pemeriksaan',
            data: 'no_pemeriksaan',
            orderable: false
        },
        {
            title: 'Tangaal Permintaan',
            data: 'tanggal_permintaan',
            orderable: false
        },
        {
            title: 'Jenis Pemeriksaan',
            data: 'jenis_pemeriksaan',
            orderable: false
        },
        {
            title: 'Nama Pemeriksaan',
            data: 'nama_pemeriksaan',
            orderable: false
        },
        {
            title: 'Status',
            data: 'status',
            orderable: false
        },
        {
            title: 'Disetujui Oleh',
            data: 'disetujui_oleh',
            orderable: false
        },
        {
            title: 'Tanggal Disetujui',
            data: 'tanggal_disetujui',
            orderable: false
        },
    ],
});

table.clear().draw();