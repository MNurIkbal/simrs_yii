$(document).ready(function() {
    var tabledata = $('#cppt-gizi-table').docoTabel({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: false,
        orderable: false,
        order: [[0, "desc"]],
        ajax: baseUrl + "gizi/asesmen-gizi/get-data-cppt?id=" +pendaftaran_id,
        createdRow: function(row, data, index) {
            $('td', row).eq(0).css('vertical-align', 'top')
            $('td', row).eq(1).css('vertical-align', 'top')
            $('td', row).eq(3).css('vertical-align', 'top')
        },
        columns: [
            {
                title: 'Tanggal/Jam',
                data: 'tgl_cppt',
                orderable: false,
            },
            {
                title: 'Profesional Pemberi Asuhan',
                data: 'pegawai_nama',
                orderable: false,
            },
            {
                title: 'Hasil Asesmen Pasien',
                data: 'hasil_asesmen',
                orderable: false,
            },
            {
                title: 'Instruksi PPA',
                data: 'instruksi_ppa',
                orderable: false,
            },
            {
                title: 'Verifikasi',
                data: 'verifikasi',
                orderable: false,
            },
        ]
    })
})