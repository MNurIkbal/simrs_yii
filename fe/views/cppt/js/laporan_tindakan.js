var tbLaporanTindakan;
$(document).ready(function() {

    $('.startDateTerapi').on('change', function () {
        $('.endDateTerapi').prop('disabled', false)
      })

    $('.pickadate').pickadate({
        format: 'dd/mm/yyyy',
        formatSubmit: 'yyyy-mm-dd'
    });

    function defaultDate () {
        var startDate = moment().subtract(3, 'months').format("YYYY/MM/DD");
        var endDate = moment().format("YYYY/MM/DD");
        $('.startDateTerapi').pickadate('picker').set('select', startDate, { format: 'yyyy-mm-dd' });
        $('.endDateTerapi').pickadate('picker').set('select', endDate, { format: 'yyyy-mm-dd' });
    }

    defaultDate();

    $('#btn-reset-filter-terapi').on('click', function (e) {
        e.preventDefault();
        $('#filter-instruksi').val(null).trigger('change');
        $('#filter-jenis').val(null).trigger('change');
        defaultDate();
        tbLaporanTindakan.ajax.reload(null, false);
    });

    $('#btn-search-filter-terapi').on('click', function (e) {
        e.preventDefault();
        tbLaporanTindakan.ajax.reload(null, false);
    });

    $('#filter-instruksi, #filter-jenis').keypress(function (e) {
        if (e.which == '13') {
            tbLaporanTindakan.ajax.reload(null, false);
        }
    });

    var limitDefault = 10;
    var limit = 10;
    var currentPage = 0;
    tbLaporanTindakan = $('#tbl-laporan-tindakan').docoTabel({
        filter: false,
        info: false,
        sorting: [[3, 'desc']],
        paging: false,
        processing: true,
        serverSide: true,
        scrollY: true,
        ajax: {
            url: $('#tbl-laporan-tindakan').data('href'),
            data: function(d) {
                d.start = currentPage * limit;
                d.length = limit;
                d.instruksi = $('#filter-instruksi').val();
                d.jenis = $('#filter-jenis').val();
                d.startDate = $('.startDateTerapi').val();
                d.endDate = $('.endDateTerapi').val();
            },
            dataSrc: function(json) {
                return json.data;
            }
        },
        columns: [
            {
                title: 'No.',
                data: null,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = tbLaporanTindakan.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                title: 'No. Pendaftaran',
                data: 'no_pendaftaran',
                orderable: false,
            },
            {
                title: 'Jenis',
                data: 'jenis',
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    let _jenis = rowData.jenis
                    const groupingTipeLower = rowData.grouping_tipe.toLowerCase();
                    const isReseptur = groupingTipeLower == 'reseptur';
                    const isRehabMedik = groupingTipeLower == 'rehab medik';
                    if (rowData.grouping_tipe == 'Penunjang'){
                        _jenis = rowData.instalasi_penunjang_nama + ' - ' + rowData.ruangan_penunjang_nama
                    } else if (Array.isArray(rowData.instruksi)) {
                        if (isReseptur) {
                            return rowData.grouping_tipe;
                        }
                        if (isRehabMedik) {
                            const titleJenis = _jenis[0] + '- <br>' + rowData.grouping_tipe;
                            return titleJenis
                        }
                    }
                    return rowData.grouping_tipe + '<br>' +  _jenis
                },
                searchable: false,
                orderable: false,
            },
            {
                title: 'Tanggal Instruksi',
                data: 'tgl_tindakan',
                searchable: false,
                orderable: false,
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY HH:mm')
                }
            },
            {
                title: 'Instruksi',
                data: 'instruksi',
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    if (!Array.isArray(rowData.instruksi)) return data;
                    let instruksi = '';
                    const groupingTipeLower = rowData.grouping_tipe.toLowerCase();
                    const isReseptur = groupingTipeLower == 'reseptur';
                    const isRehabMedik = groupingTipeLower == 'rehab medik';
                    if(Array.isArray(rowData.instruksi)) {
                        if (isReseptur) {
                            instruksi += rowData.noresep + '<br>';
                        }
                        instruksi += '<ul>'
                        for(id in rowData.instruksi) {
                            const namaTindakan = rowData.tindakaninstruksi_nama[id] ?? '';
                            const qty = rowData.qty[id] ?? '';
                            const satuanKecilNama = rowData.satuankecil_nama[id] ?? '';
                            instruksi += '<li>' + namaTindakan + ' - ' + qty + ' ' + satuanKecilNama + '</li>'
                        }
                        instruksi += '</ul>'
                    } else {
                        instruksi = data;
                    }

                    return instruksi;
                },
                searchable: false,
                orderable: false,
            },
            {
                title: 'Pegawai Pemberi Instruksi',
                data: 'nama_pegawai',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Aksi',
                data: 'aksi',
                searchable: false,
                orderable: false
            }
        ],
        drawCallback: (settings) => {
            if (settings.json.load_more == true) {
                $('.btn-load').prop('disabled', false);
            }  else {
                $('.btn-load').prop('disabled', true);
            }

            if (limit <= limitDefault) {
                $('.btn-hide').prop('disabled', true);
            } else {
                $('.btn-hide').prop('disabled', false);
            }
        },
    })

    $('#form-filter__tbl-laporan-tindakan input').keypress(function (e) {
        if (e.which == '13') {
            $('#btn-search__tbl-laporan-tindakan').click();
        }
    });

    $('.btn-load').on('click', function() {
        limit = limit + limitDefault;
        tbLaporanTindakan.ajax.reload(null, false);
    });
    // $('.btn-load').prop('disabled', true);

    $('.btn-hide').on('click', function() {
        limit = limit - limitDefault;
        tbLaporanTindakan.ajax.reload(null, false);
    });
    // $('.btn-hide').prop('disabled', true);

    $('#tbl-laporan-tindakan').on('draw.dt', function() {
        if (currentPage === 0) {
            $('.btn-load').show();
        }
    });
})