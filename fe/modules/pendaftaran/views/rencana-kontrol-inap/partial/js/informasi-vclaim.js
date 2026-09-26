const tableId = 'tb-rencana-kontrol-vclaim';
const enterKey = 13;

$(() => {
    table = $('#' + tableId).docoTabel({
        filter: false,
        cacheFilter: true,
        sorting: [[1, 'desc']],
        displayLength: 10,
        processing: true,
        serverSide: false,
        pagination: true,
        scrollX: true,
        ajax: {
            url: '/pendaftaran/rencana-kontrol-inap/get-data-vclaim',
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
                data: "tglRencanaKontrol",
            },
            {
                data: "jnsPelayanan",
                searchable: false,
                orderable: false,
            },
            {
                data: "noSepAsalKontrol",
                orderable: false,
            },
            {
                data: "noKartu",
                orderable: false,
            },
            {
                data: "nama",
                orderable: false,
            },
            {
                data: "noSuratKontrol",
                orderable: false,
            },
            {
                data: "namaPoliTujuan",
                searchable: false,
                orderable: false,
            },
            {
                data: "namaDokter",
                searchable: false,
                orderable: false,
            },
            {
                data: "jnsKontrol",
                visible : false
            },
            {
                data: "filter",
                visible : false
            },
        ],
        formFilters: [
            {
                fieldName: 'tglRencanaKontrol',
                label: 'Tanggal Rencana Kontrol',
                type : {
                    name : 'rangeDate'
                }
            },
            {
                fieldName: 'filter',
                label: 'Jenis Filter',
                type: {
                    name: 'select',
                    payload: [
                        {id: 2,text: "Tanggal Rencana Kontrol"},
                        {id: 1,text: "Tanggal Entri"}
                    ]
                }
            },
            {
                fieldName: 'jnsKontrol',
                label: 'Jenis Rencana',
                type: {
                    name: 'select',
                    payload: [
                        {id: '2',text: "Rencana Kontrol"},
                        {id: '1',text: "Rencana Inap"}
                    ]
                }
            },
            {
                fieldName: 'noSepAsalKontrol',
                label: 'No. SEP'
            },
            {
                fieldName: 'noKartu',
                label: 'No. Kartu'
            },
            {
                fieldName: 'noSuratKontrol',
                label: 'No. Surat Kontrol/SPRI'
            },
            {
                fieldName: 'nama',
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

$(document).on("click", `#${tableId} tbody tr`, function () {
    var primary = null;
    $("#data-hapus").attr("disabled", true);
    $("#btn-print-rencana").attr("disabled", true);
    $("#data-edit").attr("disabled", true);
    try {
        primary = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        getInfoPeserta(primary);
    } catch (e) {
        primary = null;
    }
});

function getInfoPeserta(primary) {
    $.ajax({
        url: "/pendaftaran/rencana-kontrol-inap/get-no-surat-kontrol",
        type: "GET",
        data: {
            no_surat_kontrol: primary,
        },
        dataType: "JSON",
        success: function(response) {
            var can_print = response.can_print
            var messageError = response.message
            if (can_print == false) {
                $("#btn-print-rencana").attr("disabled", true);
                $("#data-edit").attr("disabled", true);
                $("#data-hapus").attr("disabled", true);
                docoNotification('warning', 'Warning!', messageError);
            } else {
                $("#btn-print-rencana").attr("disabled", false);
                $("#data-edit").attr("disabled", false);
                $("#data-hapus").attr("disabled", false);
            }
        }
    })
}

function tabelErrorHandling(id) {
    $('#' + id + '_processing').hide();
    $('.dataTables_empty').html('Data tidak ditemukan.');
}
