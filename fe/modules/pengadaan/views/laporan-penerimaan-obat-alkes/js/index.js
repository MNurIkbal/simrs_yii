var table;
var payloadSupplier = payloadPayterm = {};
$(() => {
    const _baseUrl = baseUrl + 'pengadaan/laporan-penerimaan-obat-alkes';
    let payloadSupplier = filter.listSupplier;
    let payloadPayterm = filter.listPayterm;

    table = $("#laporan-penerimaan-obat-alkes").docoTabel({
    filter: false,
    sorting: [[5, "desc"], [6, "asc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    scrollX: true,
    ajax: {
        'url': baseUrl + "pengadaan/laporan-penerimaan-obat-alkes/get-data",
        'type': 'POST'
    },
    columns: [
        {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
                var tableInfo = table.page.info()
                return tableInfo.start + rowAdditionalData.row + 1
            }
        },
        {
            title: "Tanggal PR", 
            data: "tgl_pr", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
            }
        },
        {
            title: "Tanggal Approve PR", 
            data: "tgl_approve", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
            }
        },
        {
            title: "Kode Supplier",
            data: "supplier_kode",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Nama Supplier",
            data: "supplier_nama",
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Nama Manufaktur",
            data: "nama_manufaktur",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Payment Term",
            data: "payterm_nama",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Tanggal Penerimaan",
            data: "tgl_penerimaan",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
            }
        },
        {
            title: "Nomor Penerimaan",
            data: "no_penerimaan",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Diterima Oleh",
            data: "diterima_oleh",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Status Penerimaan",
            data: "status_penerimaan",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Tanggal PO",
            data: "tgl_po",
            searchable: true,
            render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
            }
        },
        {
            title: "Tanggal Validasi PO",
            data: "tgl_validasi_po",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
            }
        },
        {
            title: "Nomor PO",
            data: "nomor_po",
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Kode Obat Alkes",
            data: "kode_item",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Nama Obat Alkes",
            data: "obatalkes_nama",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Jenis Obat Alkes",
            data: "jenisobatalkes_nama",
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Qty PO", 
            data: "qty_po", 
            searchable: false,
            className: "text-right",
            render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
            title: "Satuan Besar PO", 
            data: "satuan_po", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Qty Penerimaan",
            data: "qty_penerimaan",
            searchable: false,
            render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
            title: "Qty Return",
            data: "qty_retur",
            searchable: false,
            render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
            title: "Qty Diterima", 
            data: "qty_diterima", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Satuan Besar Terima", 
            data: "satuan_terima", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "PO Balance", 
            data: "po_balance", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Satuan Besar PO Balance", 
            data: "satuan_balance", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Nilai Konversi", 
            data: "nilai_konversi", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Qty Konversi", 
            data: "qty_konversi", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Satuan Kecil", 
            data: "satuan_kecil", 
            searchable: false ,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Harga Netto (Rp.)",
            data: "harganetto",
            searchable: false,
            className: "text-right",
            render: (data) => {
                return docoHelper.convertToRupiah(data);
            }
        },
        {
            title: "Harga (Rp.)", 
            data: "harga", 
            searchable: false,
            className: "text-right",
        },
        {
            title: "Discount (%)", 
            data: "discount", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "PPN (%)", 
            data: "ppn_persen", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Subtotal (Rp.)", 
            data: "sub_total", 
            searchable: false,
            className: "text-right",
        },
        {
            title: "Total (Rp.)", 
            data: "total", 
            searchable: false,
            className: "text-right",
        },
        {
            title: "Catatan PO", 
            data: "catatan_po", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : data
            }
        },
        {
            title: "Nomor PR", 
            data: "no_pr", 
            searchable: false,
            render: (data) => {
                return data == null ? '-' : data;
            }
        },
        {
            title: "Nomor Batch", 
            data: "no_batch", 
            searchable: false,
            render: (data) => {
                return data == null ? '-' : data;
            }
        },
        {
            title: "Tanggal Kadaluarsa", 
            data: "tgl_kadaluarsa", 
            searchable: false,
            render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
            }
        },
        {
            title: "No. Surat Jalan", 
            data: "no_suratjalan", 
            searchable: false,
            render: (data) => {
                return data == null ? '-' : data;
            }
        },
        {
            title: "No. Faktur", 
            data: "no_faktur", 
            searchable: false,
            render: (data) => {
                return data == null ? '-' : data;
            }
        },
    ],
    formFilters: [
        {
            fieldName: 'tgl_penerimaan',
            label: 'Tanggal Penerimaan',
            type: {
                name: 'rangeDate',
            }
        },
        {
            fieldName: 'supplier_id',
            label: 'Nama Supplier',
            type: {
                name: 'selectMultiple',
                payload: payloadSupplier
            }
        },
        'nomor_po',
        {
            fieldName: 'payterm_id',
            label: 'Payment Term',
            type: {
                name: 'select',
                payload: payloadPayterm
            }
        },
        {
            fieldName: 'obatalkes_nama',
            label: 'Nama Obat Alkes',
            type: {
                name: 'text',
                payload: 'obatalkes_nama'
            }
        },
        'no_penerimaan'
    ]
   });
   
    $('#btn-search__laporan-penerimaan-obat-alkes').css('display', 'none')
    $('#btn-reset__laporan-penerimaan-obat-alkes').css('display', 'none');

    function RemoveFilterSession(tableId = null) {
        let _sessionKey = "FilterTable/"
        if (tableId) {
            _sessionKey = _sessionKey + tableId
        }
        for (var i = sessionStorage.length; i--;) {
            if (sessionStorage.key(i).includes(_sessionKey)) {
            sessionStorage.removeItem(sessionStorage.key(i));
            }
        }
    }
      
    $(document).on('click', '.btn-reset', ({ currentTarget }) => {
        const tableId = $(currentTarget).data('table-id');
        RemoveFilterSession(tableId)
        const element = $(`#filter-section__${tableId}`);
        const formWrapper = $(`#form-filter__${tableId}`);
        element.find('input').val('');
        element.find('select').val(null).trigger('change', {'elemfrom':currentTarget});
        element.find('input.startDate').val(moment().locale('en').format('DD-MMM-YYYY'));
        element.find('input.endDate').val(moment().locale('en').format('DD-MMM-YYYY'));
        element.find('input[name="tgl_penerimaan"]').val(moment().locale('en').format('DD-MMM-YYYY') + ' - ' + moment().locale('en').format('DD-MMM-YYYY'));
        const tableElement = $(`#${tableId}`).DataTable();
        showLoader();
        tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper);
        tableElement.ajax.reload();
    });
})
