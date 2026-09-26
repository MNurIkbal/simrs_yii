var tblBpjs;
tblBpjs = $('#tbl-detail-bpjs').docoTabel({
    processing: true,
    serverSide: false,
    ajax: baseUrl + 'pendaftaran/daftar-igd/get-detail-kunjungan-bpjs?no_kartu=' + no_kartu,
    columns: [
        {
            title: 'No',
            data: 'rowNum',
            searchable: false,
            orderable: false
        },
        { title: 'No SEP', data: 'noSep', searchable: false, orderable: false },
        { title: 'RI/RJ', data: 'jnsPelayanan', searchable: false, orderable: false },
        { title: 'Tgl SEP', data: 'tglSep', searchable: false, orderable: false },
        { title: 'Tgl. Pulang', data: 'tglPlgSep', searchable: false, orderable: false },
        { title: 'Diagnosa', data: 'diagnosa', searchable: false, orderable: false },
        { title: 'No Rujukan', data: 'noRujukan', searchable: false, orderable: false },
        { title: 'Spesialis/Sub Spesialis', data: 'poli', searchable: false, orderable: false },
        { title: 'PPK Pelayanan', data: 'ppkPelayanan', searchable: false, orderable: false },
    ],

});
$('.dataTables_filter').hide();