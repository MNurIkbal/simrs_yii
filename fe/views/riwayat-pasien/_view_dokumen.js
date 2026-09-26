var tbLaporanTindakan;
$(document).ready(function() {
    tbLaporanTindakan = $('#tbl-view-dokumen').DataTable({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: $('#tbl-view-dokumen').data('href'),
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
                title: 'Jenis Dokumen',
                data: 'nama_dokumen',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Aksi',
                data: 'aksi',
                searchable: false,
                orderable: false,
            },
        ],
    })
})