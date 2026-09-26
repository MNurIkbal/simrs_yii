$(document).ready(function () {
    const historyPendaftaranId = $('#pendaftaran-id').val();
    let historyTable = $('#tbl-riwayat-visit').docoTabel({
        cacheFilter: true,
        filter: false,
        info: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        ordering: false,
        ajax: {
            url: `/ranap/inf-pasien-ranap/get-riwayat-visit?pendaftaran_id=${historyPendaftaranId}`,
        },
        columns: [
            {
                data: 'tgl_cppt',
                orderable: false,
                title: 'Tanggal CPPT',
                render: (data) => {
                    return moment(data).format('DD-MMM-YYYY');
                }
            },
            {
                data: 'dokter_cppt',
                orderable: false,
                title: 'Dokter Visit / Dokter Isi SOAP'
            },
        ],
    })
});