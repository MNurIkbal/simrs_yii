var { status_anestesi, list_sts_op } = pageVars;
const tableId = 'tb-pasien-anestesi';
const enterKey = 13;

$(() => {
    table = $('#' + tableId).docoTabel({
        filter: false,
        sorting: [5, "desc"],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollY: true,
        ajax: {
            url: '/bedah/informasi-pasien-anestesi/get-data',
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
                data: "nama_pasien",
                orderable: false,                
            },
            {
                data: "no_rekam_medik",
                orderable: false,
            },
            {
                data: "no_pendaftaran",
                orderable: false,
            },
            {
                data: "tgl_operasi",
            },
            {
                data: "list_pemeriksaan",
                orderable: false,
                searchable: false,
            },
            {
                data: "status",
                orderable: false,
                searchable: false,
            },
            {
                data: "proses_status_anestesi",
                orderable: false,
            },
        ],
        formFilters: [
            {
                fieldName: 'tgl_operasi',
                label: 'Tanggal Rencana',
                type : {
                    name : 'rangeDate'
                }
            },
            {
                fieldName: 'nama_pasien',
                label: 'Nama Pasien'
            }, 
            {
                fieldName: 'no_rekam_medik',
                label: 'No. Rekam Medik'
            }, 
            {
                fieldName: 'no_pendaftaran',
                label: 'No. Pendaftaran'
            }, 
            {
                fieldName: 'status_periksa',
                label: 'Status',
                type: {
                    name: 'select',
                    payload: list_sts_op
                }
            },
            {
                fieldName: 'proses_status_anestesi',
                label: 'Proses',
                type: {
                    name: 'select',
                    payload: status_anestesi
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