var tblBpjs;
$(document).ready(function() {
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
            { title: 'Tgl SEP', data: 'tglSep', searchable: true, orderable: false },
            { title: 'Tgl. Pulang', data: 'tglPlgSep', searchable: false, orderable: false },
            { title: 'Diagnosa', data: 'diagnosa', searchable: false, orderable: false },
            { title: 'No Rujukan', data: 'noRujukan', searchable: false, orderable: false },
            { title: 'Spesialis/Sub Spesialis', data: 'poli', searchable: false, orderable: false },
            { title: 'PPK Pelayanan', data: 'ppkPelayanan', searchable: false, orderable: false },
        ],
        formFilters: [
            {
                fieldName: 'tglSep',
                label: 'Tanggal',
                type: {
                    name: 'rangeDate',
                    payload: {
                        allDate: true,
                        isInModal: true,
                    }
                }
            },
        ],

    });
    $('.dataTables_filter').hide();

    dateRangeHelper('.startDate','.endDate','.targetDate');

    $('#btn-search__tbl-detail-bpjs').hide();
    $('#btn-reset__tbl-detail-bpjs').hide();

    $('.data-search').on('click', function() {
        var start = $('.startDate').val();
        var end = $('.endDate').val();

        var countDays = calculateDate(start, end);

        if(countDays > 90) {
            docoNotification('error', 'Terjadi kesalahan.', 'Maksimum filter tanggal pada rentang waktu 90 Hari.');
        } else {
            var url = baseUrl + 'pendaftaran/daftar-igd/get-detail-kunjungan-bpjs?no_kartu=' + no_kartu + '&start_date=' + start + '&end_date=' + end;

            tblBpjs.ajax.url(url).load();
        }
    });
});