const baseUrlService = '/gudang/informasi-formulir-so-barang'
$(() => {
    dateRangeHelper(".startDate", ".endDate", ".targetDate")

    $("#tanggalFormulir").daterangepicker({
        startDate: new Date(),
        autoUpdateInput: true,
        endDate: new Date(),
        maxDate: new Date(),
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MM-YYYY"
        }
    });
    loadTable(true)
})


function loadTable(initDatatable = false) {
    generateDatatable($('#datatableWrapper'), {
        url: `${baseUrlService}/datatable`,
        columns: [
            { data: 'no', orderable: 0, isSerialNumber: 1 },
            { data: 'checkbox', orderable: 0 },
            { data: 'tgl_mutasibarang' },
            { data: 'nomutasi_barang' },
            { data: 'tujuan', orderable: 0 },
            { data: 'status', orderable: 0 },
        ],
        payload: {
            tanggalFormulir: $("#tanggalFormulir"),
            nomorFormulir: $("#nomorFormulir"),
        },
        withHeader: false,
        initDatatable,
        bindElement: (record, element) => {
            $(element).addClass('clickable')
            $(element).bind('click', () => {
                element.find('input[type="checkbox"]').trigger('click')
            })
        }
    }, () => {
        $('#datatableWrapper table tbody input[type="checkbox"]').uniform({
            radioClass: 'choice'
        });
        $('#datatableWrapper table tbody input[type="checkbox"]').bind('click', ({ delegateTarget }) => {
            $('#datatableWrapper table tbody input[type="checkbox"]').not(delegateTarget).prop('checked', false).uniform('refresh')
        })
    })
}