var { bulan, tahun} = phpVars
const tableId = 'tb-update-tanggal-pulang';
const enterKey = 13;

$(() => {
    table = $('#' + tableId).docoTabel({
        filter: false,
        sorting: [[1, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: false,
        pagination: true,
        scrollX: true,
        ajax: {
            url: '/pendaftaran/update-tanggal-pulang/get-data',
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
                data: "noSep",
                searchable: true,
                orderable: false,
            },
            {
                data: "noSepUpdating",
                orderable: false,
            },
            {
                data: "noKartu",
                searchable: true,
                orderable: false,
            },
            {
                data: "nama",
                searchable: true,
                orderable: false,
            },
            {
                data: "ppkTujuan",
                searchable: false,
                orderable: false,
            },
            {
                data: "tglPulang",
                searchable: true,
                orderable: false,
            },
            {
                data: "status",
                searchable: false,
                orderable: false,
            },
            {
                data: "noSurat",
                searchable: false,
                orderable: false,
            },
            {
                data: "tglMeninggal", 
                searchable: false,
                orderable: false,
            },
            {
                data: "jnsPelayanan", 
                searchable: false,
                orderable: false,
            },
            {
                data: "keterangan", 
                searchable: false,
                orderable: false,
            },
            {
                data: "user", 
                searchable: false,
                orderable: false,
            }
        ],
        formFilters: [
            {
                fieldName: 'bulan',
                label: 'Bulan',
                type: {
                    name: 'select',
                    payload: bulan
                }

            },
            {
                fieldName: 'tahun',
                label: 'Tahun',
                type: {
                    name: 'select',
                    payload: tahun
                }

            },
            // {
            //     fieldName: 'nama',
            //     label: 'Nama Pasien'
            // },
            // {
            //     fieldName: 'noKartu',
            //     label: 'No. Kartu'
            // },
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

$(document).on("click", "#tb-update-tanggal-pulang tbody tr", function () {
    // var bpjs_id = null;
    $("#data-edit").attr("disabled", false);
    try {
        noSep = table.row(".selected").data().noSep ? table.row(".selected").data().noSep : null;
        getInfoPasien(noSep);
    } catch (e) {
        noSep = null;
    }
    
    // if (bpjs_id != null ) {
    //     $("#btn-print-rencana").attr("disabled", false);
    // } else {
    //     $("#btn-print-rencana").attr("disabled", true);
    // }
});

function getInfoPasien(noSep) {  
    $.ajax({
        url: "/pendaftaran/update-tanggal-pulang/get-info-pasien",
        type: "GET",
        data: {
            noSep: noSep,
        },
        dataType: "JSON",
        success: function(response) {
            var can_update = response.can_update
            var error_message_1 = response.Error
            if (can_update == false) {
                $("#data-edit").attr("disabled", true);
                docoNotification("error", "Tidak bisa update", error_message_1);
            } else {
                $("#data-edit").attr("disabled", false);
            }
        }
    })
}