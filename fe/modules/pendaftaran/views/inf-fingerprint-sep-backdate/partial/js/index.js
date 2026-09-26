const tableId = 'tb-fingerprint-sep-backdate';
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
            url: '/pendaftaran/inf-fingerprint-sep-backdate/get-data',
            error: function (data) {
                if (typeof data.responseJSON.metaData != 'undefined') {
                    var message = data.responseJSON.metaData.message;
                    docoNotification("warning", 'Error Server BPJS', message);
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
            },
            {
                data: "noKartu",
                searchable: false,
                orderable: false,                
            },
            {
                data: "nama",
                searchable: false,
                orderable: false,
            },
            {
                data: "tglsep",
                orderable: false,
            },
            {
                data: "jnspelayanan",
                searchable: false,
                orderable: false,
            },
            {
                data: "persetujuan",
                searchable: false,
                orderable: false,
            },
            {
                data: "status",
                searchable: false,
                orderable: false,
            },
        ],
        formFilters: [
            {
                fieldName: 'tglsep',
                label: 'Tanggal Pengajuan SEP',
            },
        ],
        filterRendered: (wrapper) => {
            $(wrapper).find('[name="filter"]').val($('[name="filter"] option:eq(1)').val()).trigger('change')
            $(wrapper).find('[name="filter"] option:eq(0)').remove()
        },
    })

    setfilterMonthYearDate('#tb-fingerprint-sep-backdate-tglsep--form');

    $("#approval").on("click", function(e) {
        e.preventDefault();
        var data = table.row(".selected").data();
    
        if (data == undefined) {
            docoNotification("warning", 'Proses Gagal', 'Belum ada data yang dipilih!');
            return true;
        }
    
        $.ajax({
            url: '/pendaftaran/inf-fingerprint-sep-backdate/approval',
            type: "POST",
            dataType: "JSON",
            data: data,
            success: function(res) {
                docoNotification("success", 'Proses Berhasil', '');
                table.ajax.reload();
            },
            error: function(err) {
                console.log(err);
                if (typeof err.responseJSON.metaData != 'undefined') {
                    var message = err.responseJSON.metaData.message;
                    docoNotification("error", 'Error Server BPJS', message);
                } else {
                    var error = err.responseJSON.response;
                    var errMsg = error.message ? error.message : '';
                    docoNotification("error", 'Proses Gagal', errMsg);
                }
            }
        });
    
        return false;
    });
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

function setfilterMonthYearDate(id) {
    var tglsepField = $(id);
    var monthYearFormat = '%b-%Y';
    var monthYearConv = new AnyTime.Converter({
        format: monthYearFormat,
        moment: moment(),
    });

    $('#rangeDemoToday').click(function (e) {
        tglsepField.val(monthYearConv.format(new Date())).change();
    });
    // Clear dates
    $('#rangeDemoClear').click(function (e) {
        tglsepField.val('').change();
    });
    // Start date
    tglsepField.AnyTime_noPicker().AnyTime_picker({
        format: monthYearFormat,
    });
}