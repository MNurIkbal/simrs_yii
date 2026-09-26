var table

$(() => {
    table = $('#zat-aktif-obat').docoTabel({
        filter: false,
        sorting: [],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
        },
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'os',
            selector: 'td'
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                defaultContent: ''
            },
            {
                data: null,
                orderable: false,
                class: 'text-center',
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = table.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                data: 'obatalkes_kode',
            },
            {
                data: 'obatalkes_nama',
            },
            {
                data: 'jenisobatalkes_nama',
                orderable: false
            },
            { 
                data: 'jumlah_zataktif',
                orderable: false,
                render: (data, rowElement, rowData) => {
                    return `<p style="text-align: right"> ${rowData.jumlah_zataktif != null ? rowData.jumlah_zataktif : '0'} </p>`
                }
            }
        ],
        formFilters: [
            {
                fieldName: 'obatalkes_kode',
                label: 'Kode Obat'
            },
            {
                fieldName: 'obatalkes_nama',
                label: 'Nama Obat'
            },
            {
                fieldName: 'jenisobatalkes',
                label: 'Jenis Obat Alkes',
                type: {
                    name: 'select',
                    payload: dropdownData.jenisobatalkes
                }
            }

        ],
    })
})

$(document).on('keypress',function(e) {
    if(e.which == 13) {
        $("#btn-search__zat-aktif-obat").click();
    }
});
