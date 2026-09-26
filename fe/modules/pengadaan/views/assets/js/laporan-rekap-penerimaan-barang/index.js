var table;

$(document).on("click", ".data-reset", function () {
	$("#supplier").val(['0']).trigger("change");
});

$(document).ready(function() {
    // Example
    table = $("#rekap-penerimaan-barang").docoTabel({
        filter: true,
        sorting: [],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"pengadaan/laporan-rekap-penerimaan-barang/get-data",
        columns: [
            {
                title: "No.",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            { 
                title: "Kode Supplier", 
                data: "supplier_kode", 
                searchable: false 
            },
            { 
                title: "Nama Supplier", 
                data: "supplier_id", 
                searchable: true,
                render: function (data, type, row) {
                    return row.supplier_nama;
                }
            },
            { 
                title: "Tanggal Penerimaan", 
                data: "tgl_penerimaan", 
                searchable: true 
            },
            { 
                title: "Nomor Penerimaan", 
                data: "no_penerimaan", 
                searchable: true 
            },
            { 
                title: "Di Terima Oleh", 
                data: "diterima_oleh", 
                searchable: false 
            },
            { 
                title: "Status Penerimaan", 
                data: "status_penerimaan", 
                searchable: false 
            },
            { 
                title: "Tanggal PO", 
                data: "tgl_po", 
                searchable: false 
            },
            {
                title: "Tanggal Validasi PO", 
                data: "tgl_validasi_po", 
                searchable: false 
            },
            {
                title: "NO PO", 
                data: "nomor_po", 
                searchable: true 
            },
            {
                title: "Payment Term", 
                data: "payterm_nama", 
                searchable: true 
            },
            {
                title: "NO Surat Jalan", 
                data: "no_suratjalan", 
                searchable: false 
            },
            {
                title: "NO Faktur", 
                data: "no_faktur", 
                searchable: false 
            },
            {
                title: "Total Harga", 
                data: "total", 
                searchable: false,
                className : "text-right"
            },
        ]
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            3, '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>'
        ],
        [
            9, '<div class="form-group"><input type="text" class="form-control" placeholder="Cari Berdasarkan NO PO"></div>'
        ],
        [
            4, '<div class="form-group"><input type="text" class="form-control" placeholder="Cari Berdasarkan No Penerimaan"></div>'
        ],
        [
            2, '<div class="form-group"><select class="form-control input-xs" name="supplier_id[]" id="supplier" multiple="multiple"></select></div>'
        ],
    ], {
        3:0,
        9:1,
        4:3,
        2:2
    }, true);

    dateRangeHelper(".startDate", ".endDate", ".targetDate");
    
    $("#supplier").select2({
        placeholder: "Cari Berdasarkan Supplier",
        data: dropDownSupplier,
    });

    $("option:selected").prop("selected", false);

    $('#supplier').on("change", function () {
        var selected = $(this).val();
        if (selected[0] == '0') {
            var select = selected[0] == '0';
            $("#supplier").val(select).trigger("change");
        }
    });

});
