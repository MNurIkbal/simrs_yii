var tbl;
tbl = $('#tbl-detail').docoTabel({
    processing: true,
    serverSide: false,
    ajax: baseUrl + 'pendaftaran/daftar-igd/get-detail-kunjungan?no_rekam_medik=' + no_rekam_medik,
    columns: [
        {
            title: 'No',
            data: 'rowNum',
            searchable: false,
            orderable: false
        },
        { title: 'Instalasi', data: 'instalasi_nama', searchable: false, orderable: false },
        { title: 'Ruangan', data: 'ruangan_nama', searchable: false, orderable: false },
        { title: 'Dokter', data: 'nama_pegawai', searchable: false, orderable: false },
        { title: 'Tgl. Keluar', data: 'tglpasienpulang', searchable: false, orderable: false },
        { title: 'Cara Keluar', data: 'carakeluar_nama', searchable: false, orderable: false },
    ],

});
$('.dataTables_filter').hide();