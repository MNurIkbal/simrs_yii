var { list_ruangan } = pageVars;
const tableId = 'tb-jadwal-cuti';
const enterKey = 13;

$(() => {
    table = $('#' + tableId).docoTabel({
        filter: false,
        sorting: [],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
            url: '/master/jadwal-dokter/get-data-jadwal-cuti',
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
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                data: "dokter_nama",                
            },
            {
                data: "spesialis_nama",
                searchable: false,
                orderable: false,
            },
            {
                data: "ruangan_nama",
                orderable: false,
            },
            {
                data: "tgl_cuti",
                orderable: false,
            },
            {
                data: "lama_cuti",
                orderable: false,
            },
            {
                data: "tgl_cuti_awal",
                visible: false,
            },
        ],
        formFilters: [
            {
                fieldName: 'tgl_cuti',
                label: 'Tanggal Cuti',
                type : {
                    name : 'rangeDate'
                }
            },
            {
                fieldName: 'dokter_nama',
                label: 'Nama Dokter'
            }, 
            {
                fieldName: 'ruangan_nama',
                label: 'Ruangan Nama',
                type: {
                    name: 'select',
                    payload: list_ruangan
                }
            },

        ],
        filterRendered: (wrapper) => {
        },
    })
})

$(document).on('keypress',function(e) {
    if(e.which == enterKey) {
        $("#btn-search__" + tableId).click();
    }
});

function tabelErrorHandling(id) {
    $('#' + id + '_processing').hide();
    $('.dataTables_empty').html('Data tidak ditemukan.');
}