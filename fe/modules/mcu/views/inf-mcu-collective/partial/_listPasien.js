var tableDetail;
var optionStatus = [];

$.each(_status_reservasi, function (index, value) {
    optionStatus.push({
        id: index,
        text: value,
    });
});

$(() => {
    const progress = $(".progress");
    const progressBar = $(".progress .progress-bar");
    const labelProgress = $(".label-progress");
    const labelPercent = $(".progress .label-persentase");
    const labelInfo = $(".populate-data");

    progress.css("display", "none")

    tableDetail = $("#detail-mcu").docoTabel({
        filter: false,
        columnDefs: [{
            searchable: false,
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'multi',
            selector: 'tr'
        },
        sorting: [[5, "asc"]],
        displayLength: 10,
        scrollCollapse: true,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollY: false,
        ajax: {
            url: "/mcu/inf-mcu-collective/list-pasien?no_order=" + no_order,
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = tableDetail.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                    return `
                  <p style="font-weight:bold;font-size:13px;margin-bottom: 2px"> ${rowData.nama != null ? rowData.nama : '-'} (${rowData.jeniskelamin_id == 16 ? 'P' : 'L'})</p>
                  <p> Tanggal Lahir : ${rowData.tanggal_lahir != null ? moment(rowData.tanggal_lahir).format('DD-MM-YYYY') : '-'} </p>
                  <p> No Rekam Medik : ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : '-'}</p>
               `
                }
            },
            {
                searchable: false,
                render: (data, rowElement, rowData) => {
                    return rowData.status_reservasi.toLowerCase().replace(/\b(\w)/g, s => s.toUpperCase());
                }
            },
            {
                searchable: false,
                orderable: false,
                data: "no_pendaftaran",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                searchable: false,
                data: "tgl_pendaftaran",
                render: (data, rowElement, rowData) => {
                    return `<p style="margin-bottom: 2px"> ${data == "" || data == null ? "-" : moment(data).format("DD-MMM-YYYY")} </p>`
                }
            },
            {
                searchable: false,
                orderable: false,
                data: "no_pembayaran",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
        ],
        formFilters: [
            'no_rekam_medik',
            'no_pendaftaran',
            {
                fieldName: 'nama',
                label: 'Nama Pasien',
            },
            {
                fieldName: 'statusreservasi_id',
                label: 'Status',
                type: {
                    name: 'select',
                    payload: optionStatus
                }
            },
        ],
        drawCallback: (settings) => {
            tableDetail.rows().select();
        },
    });

    const populateData = (type) => {
        var arrData = [];
        var rowCount = tableDetail.rows('.selected').data().length;
        if (rowCount == 0) {
            docoNotification('warning', 'Peringatan', 'Tidak Ada Data yang di Pilih!');
            return false;
        }

        var rowData = tableDetail.rows({ selected: true }).data();
        var filteredRows = $(rowData).filter(function (idx) {
            return (type == 1) ? rowData[idx].pendaftaran_id != null && rowData[idx].no_pembayaran == null : rowData[idx].no_pembayaran != null;
        });
        if (filteredRows.length == 0) {
            var _messageError = (type == 1) ? 'Tidak Ada Data yang Sudah di Daftarkan!' : 'Tidak Ada Data yang Sudah di Bayarkan!';
            docoNotification('warning', 'Peringatan', _messageError);
            return false;
        }
        for (var i = 0; i < filteredRows.length; i++) {
            var _pendaftaranId = typeof filteredRows[i].pendaftaran_id != 'undefined' ? filteredRows[i].pendaftaran_id : null;
            var _noPembayaran = typeof filteredRows[i].no_pembayaran != 'undefined' ? filteredRows[i].no_pembayaran : null;
            var _noRm = typeof filteredRows[i].no_rekam_medik != 'undefined' ? filteredRows[i].no_rekam_medik : null;
            var _namaPasien = typeof filteredRows[i].nama != 'undefined' ? filteredRows[i].nama : null;
            var _pembayaranId = typeof filteredRows[i].pembayaran_id != 'undefined' ? filteredRows[i].pembayaran_id : null;

            arrData.push(
                {
                    pendaftaran_id: _pendaftaranId,
                    pembayaran_id: _pembayaranId,
                    no_pembayaran: _noPembayaran,
                    no_rekam_medik: _noRm,
                    nama_pasien: _namaPasien,
                }
            );
        }

        return arrData;
    }

    const setPresentase = function (progress) {
        labelPercent.html(progress)
        progressBar.css("width", progress + "%")
            .attr("aria-valuenow", progress)
            .attr("aria-volume", progress);
    }

    var prosesInvoice = (type) => {
        var channel = null;
        var _dataPost = populateData(type)

        if (_dataPost) {
            if ($('.progress-bar').hasClass('bg-danger')) {
                $('.progress-bar').removeClass('bg-danger').addClass('bg-primary');
            }

            async function updateProgressBar() {
                let config = await $.getJSON("./../../json/setup.json")
                const socket = (config.origin == "true") ? io.connect(window.location.origin) : io.connect(config.ip + ':' + config.port)
                $.ajax({
                    url: `/mcu/inf-mcu-collective/proses-invoice?no_order=${no_order}&type=${type}`,
                    type: 'POST',
                    data: {
                        data: _dataPost
                    },
                    success: function (res) {
                        const _randString = res.randString;
                        const _countData = res.countData;
                        const _unique = `${config.name}:${_randString}`

                        if (type == 1) {
                            channel = `create-invoice-corporate:${_unique}`
                        }
                        else if (type == 2) {
                            channel = `cetak-invoice-corporate:${_unique}`
                        }
                        else {
                            channel = `corporate:${_unique}`
                        }

                        progress.css("display", "block");

                        let startNum = 10
                        setPresentase(startNum)
                        let totalProgres = parseInt(startNum) + parseInt(res.totalPerStep) + 20;
                        $(".label-progress").html(`<p style="font-size:14px;font-weight:bold;">Sedang memproses Data <i> (0/${_countData}) </i> data </p>`);

                        socket.on(channel, (data) => {
                            const _data = $.parseJSON(data);
                            const { status, messageProcess, no_order, progress } = _data
                            if (status == 'finish') {
                                $(".label-progress").html(`<p style="font-size:14px;font-weight:bold;"> <i> ${messageProcess} </i></p>`)
                                setPresentase(progress)
                                if (progress == 100) {
                                    if (type == 1) {
                                        tableDetail.draw();
                                    }
                                    else {
                                        var _urlDownload = (type == 2) ? 'inf-mcu-collective' : 'lap-hasil-mcu';
                                        window.open(`/mcu/${_urlDownload}/download-file?no_order=${no_order}`, '_blank')
                                    }
                                    labelProgress.css('display', 'none')
                                    setPresentase(0)
                                    $(".progress").css("display", "none")
                                }
                            } else if (status == 'failed') {
                                docoNotification('error', 'Proses Gagal!', messageProcess)
                                $('.progress-bar').removeClass('bg-primary').addClass('bg-danger');
                                $(".label-progress").html(`<p style="font-size:14px;font-weight:bold;color:red;"> <i> ${messageProcess} </i></p>`)
                            } else {
                                startNum++
                                setPresentase(Math.ceil((startNum / totalProgres) * 100))
                                var currentProcess = (startNum - 10);
                                $(".label-progress")
                                    .html(`<p style="font-size:14px;font-weight:bold;">Sedang memproses Data <i> (${currentProcess}/${_countData}) </i> data </p>`)
                            }
                        })
                    }
                });
            }
            updateProgressBar()
        }
    }

    $(document).on('click', '#cetak-invoice', function () {
        prosesInvoice(2)
    });

    $(document).on('click', '#create-invoice', function () {
        prosesInvoice(1)
    });

    $(document).on('click', '#cetak-kesimpulan', function () {
        prosesInvoice(3)
    });

    $('#form-filter__detail-mcu').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
});
