var tableMonitoringInfus;
$(document).ready(function(){
    tableMonitoringInfus =  $('#table-monitoring-infus').docoTabel({

        cacheFilter: true,
        filter: false,
        info: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollY: false,
        scrollX: true,
        ordering: false,
        ajax: {
            url: `/ranap/pemeriksaan-rawat-inap/get-data-pemberian-infus?id=` + pendaftaran_id,
        },
        columns: [
            {
                data: null,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = tableMonitoringInfus.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                data: 'tgl_pemasangan',
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    let strData = data == null ? '-' : moment(data).format('DD-MM-YYYY HH:mm:ss')
                    strData += '<hr style="margin-top: 3px; margin-bottom: 3px;">' + rowData.kelompokpegawai_nama + '<br>' + rowData.nama_pegawai
                    return strData
                }
            },
            {
                data: 'daftartindakan_nama',
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    let listObat = JSON.parse(rowData.obatalkes_nama)
                    let obat = listObat.join('<br>')
                    return data + ' /<br>' + obat
                }
            },
            {
                data: 'volume',
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    return data + ' ml'
                }
            },
            {
                data: 'durasi',
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    return data + ' menit'
                }
            },
            {
                data: 'jumlah_tetesan',
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    return data + ' ml/menit'
                }
            },
            {
                data: 'aksi',
                className: 'text-center',
                orderable: false,
            },
        ]
    });
})

function reloadForm() {
    $('#div-form').docoLoad({
      url:
        '/ranap/pemeriksaan-rawat-inap/form-pemberian-infus?id=' + pendaftaran_id,
      dataType: 'html',
      success: function (data) { },
    })
    tableMonitoringInfus.draw()
}