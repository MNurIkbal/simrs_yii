var { bulan, tahun} = phpVars
const enterKey = 13;

$(() => {
    table = $('#' + tableId).docoTabel({
        filter: false,
        sorting: [[1, 'desc']],
        displayLength: 10,
        processing: true,
        serverSide: false,
        pagination: true,
        scrollX: true,
        ajax: {
            url: '/pendaftaran/rujukan-khusus/get-data',
            error: function (data) {
                if (typeof data.responseJSON.metaData != 'undefined') {
                    var message = data.responseJSON.metaData.message;
                    docoNotification("warning", message, '');
                }
                tabelErrorHandling(tableId);
            }
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
                data: "no",
                searchable: false,
                orderable: false,
            },
            {
                data: "norujukan",
                searchable: false,
                orderable: false,
            },
            {
                data: "nokapst",
                orderable: false,
            },
            {
                data: "nmpst",
                orderable: false,
            },
            {
                data: "diagppk",
                orderable: false,
            },
            {
                data: "tglrujukan_awal",
                orderable: false,
            },
            {
                data: "tglrujukan_berakhir",
                searchable: false,
                orderable: false,
            },
            {
                data: "idrujukan", 
                visible : false            
            }
        ],
        formFilters: [
            {
                fieldName: 'blnRujukan',
                label: 'Bulan',
                type: {
                    name: 'select',
                    payload: bulan
                }

            },
            {
                fieldName: 'thnRujukan',
                label: 'Tahun',
                type: {
                    name: 'select',
                    payload: tahun
                }

            },
            {
                fieldName: 'norujukan',
                label: 'Nomor Rujukan'
            },
            {
                fieldName: 'nokapst',
                label: 'Nomor Kartu'
            },
            {
                fieldName: 'nmpst',
                label: 'Nama Pasien'
            },
        ],
        filterRendered: (wrapper) => {
            $(wrapper).find('[name="filter"]').val($('[name="filter"] option:eq(1)').val()).trigger('change')
            $(wrapper).find('[name="filter"] option:eq(0)').remove()
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
