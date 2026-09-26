var tableDropOut;
var resultResponse;

$(document).ready(() => {
    showLoader()
    initChart()
    handlerSearch()
    handlerExportPdf()
    tableDropOut = $("#table-drop-out").docoTabel({
        select: {
            style: "single",
            selector: "tr"
        },
        filter: true,
        sorting: [1, "desc"],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        // scrollX: true,
        ajax: baseUrl + "fisioterapi/laporan-drop-out-pasien/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Permintaan",
                data: "tgl_rujukan",
                searchable: true,
                orderable: true,
                visible: true,
                render: (data, type, row, meta) => data ? data : '-'
            },
            {
                title: "Data Pasien",
                data: "nama_pasien",
                searchable: false,
                render: (data, type, row, meta) => {
                    let jenis_kelamin = row.jenis_kelamin ? row.jenis_kelamin : '-';
                    let namaPasien = data ? `<b>` + data + `</b>` : '-';
                    let tgl_lahir = row.tanggal_lahir;
                    let no_rekam_medik = row.no_rekam_medik ? row.no_rekam_medik : '-';
                    return namaPasien + ' ' + '(' + jenis_kelamin + ')' + '<br/>' + tgl_lahir + '<br/>' + no_rekam_medik
                }
            },
            {
                title: "Program Terapi",
                data: "daftartindakan_nama_view",
                searchable: false,
                orderable: false,
            },
            {
                title: "Frekuensi",
                data: "frekuensi",
                searchable: false,
                orderable: false
            },
            {
                title: "Realisasi",
                data: "realisasi",
                searchable: false,
                orderable: false,
            },
            {
                title: "Sisa Terapi",
                data: "sisa",
                searchable: false,
                orderable: false,
            },
            {
                title: "Tidak Hadir",
                data: "jumlah_ketidakhadiran",
                searchable: false,
                orderable: false,
            },
            {
                title: "Expired",
                data: "jumlah_expired",
                searchable: false,
                orderable: false,
            },
            {
                title: "Status Program",
                data: "status_program_fisio_id",
                searchable: true,
                orderable: true,
                render: (data, type, row, meta) => {
                    let statusProgram = row.status_program_fisio_nama;
                    return statusProgram;
                }
            },
            {
                title: "Nama Pasien / No Rekam Medik",
                data: "nama_pasien",
                searchable: true,
                orderable: false,
                visible: false,
            },
        ],
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(tableDropOut, [
        [1, '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>'],
        [9, formFilter.statusProgram],
    ], {
        0: 1,
        1: 10,
        2: 9,
    }, true);

    dateRangeHelper(".startDate", ".endDate", ".targetDate");
    // initInformation()
});


async function initChart(params = null) {
    let startDate = params?.startDate
    let endDate = params?.endDate
    let withParams = ``
    let isEmptyStart = startDate === undefined || startDate === null
    let isEmptyEnd = endDate === undefined || endDate === null
    if (!isEmptyStart & !isEmptyEnd) withParams = `?startDate=${startDate}&endDate=${endDate}`
    async function getDataChart() {
        resultResponse = null;
        await $.ajax({
            type: 'GET',
            url: `laporan-drop-out-pasien/get-data-chart${withParams}`,
            contentType: 'application/json',
            success: function (res) {
                resultResponse = res
                hideLoader()
            },
        });
        return resultResponse
    }
    const result = await getDataChart()
    const labels = result.chart.labels;
    const values = result.chart.values;

    const data = {
        labels: labels,
        datasets: [{
            label: 'Persentase Drop Out',
            backgroundColor: 'rgb(3, 173, 40)',
            borderColor: 'rgb(3, 173, 40)',
            data: values,
        }],
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Grafik Presentase Drop Out'
                }
            }
        },
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            scales: {
                yAxis: {
                    min: 0,
                    max: 100,
                    display: true,
                    ticks: {
                        callback: function (tick) {
                            return tick + '%';
                        }
                    }
                }
            }
        }
    };
    $('#myChart').remove();
    $('.panel-body-chart').append('<canvas id="myChart"></canvas>');
    var myChart = new Chart(
        document.getElementById('myChart'),
        config
    )
}

function handlerSearch() {
    $(`.data-filter-chart`).on(`click`, function () {
        const startDate = $(`#myMonthRangePickerCustomStart`).val();
        const endDate = $(`#myMonthRangePickerCustomEnd`).val();
        let isEmptyStart = startDate === undefined || startDate === null
        let isEmptyEnd = endDate === undefined || endDate === null
        const isExist = !isEmptyStart & !isEmptyEnd
        if (isExist) initChart({ startDate: startDate, endDate: endDate })
    })
}

function handlerExportPdf() {
    $(`.export-pdf-chart`).on(`click`, function () {
        const startDate = $(`#myMonthRangePickerCustomStart`).val();
        const endDate = $(`#myMonthRangePickerCustomEnd`).val();
        let isEmptyStart = startDate === undefined || startDate === null
        let isEmptyEnd = endDate === undefined || endDate === null
        const isExist = !isEmptyStart & !isEmptyEnd
        if (isExist) callExportPdf({ startDate: startDate, endDate: endDate })
    })
}

function callExportPdf({ startDate, endDate }) {
    const dataUriChart = document.getElementById(`myChart`).toDataURL()
    // Ajax
    let targetUrl = `/fisioterapi/laporan-drop-out-pasien/export-pdf-chart`
    $.ajax({
        type: "POST",
        url: targetUrl,
        data: {
            startDate: startDate,
            endDate: endDate,
            imgChart: dataUriChart,
            data: resultResponse
        },
        success: function (res) {
            const blob = dataURItoBlobPdf(res);
            const url = URL.createObjectURL(blob);
            var downloadLink = document.createElement("a");
            downloadLink.href = url;
            downloadLink.download = `Laporan_Dropout_Pasien_Chart.pdf`;
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
            // window.open(targetUrl);
        },
    });
}

function initInformation() {
    $(`.btn-information`).trigger('click');
}

$(document).on('scroll', function() {
    $(`.btn-information`).popover('hide');
});