var table;

$(document).on("click", ".data-reset", function () {
	$("#supplier").val(['0']).trigger("change");
});

$(document).ready(function() {
	table = $("#laporan-po").docoTabel({
		filter: true,
		sorting: [4, "DESC"],
		displayLength: 10,
		processing: true,
		serverSide: true,
		scrollX: true,
		ajax: baseUrl + "pengadaan/laporan-rekap-purchase-order-obat/get-data",
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
				searchable: false,
				orderable: false
			},
			{ 
                title: "Nama Supplier", 
                data: "supplier_id", 
                render: function (data, type, row) {
                    return row.supplier_nama;
                }
            },
			{ 
				title: "No. PO", 
				data: "no_po",
				searchable: true,
				orderable: true
			},
			{ 
				title: "Tanggal PO", 
				data: "tgl_po", 
				searchable: true,
				orderable: true 
			},
			{ 
				title: "Tanggal Validasi PO", 
				data: "tgl_validasi",
				searchable: false,
				orderable: false 
			},
			{ 
				title: "Status PO", 
				data: "status_po", 
				searchable: false,
				orderable: false
			},
			{ 
				title: "Tanggal Batal PO", 
				data: "tgl_batal_po", 
				searchable: false,
				orderable: false 
			},
			{ 
				title: "Alasan Batal", 
				data: "alasan_batal_po", 
				searchable: false,
				orderable: false 
			},
			{ 
				title: "Total Harga (Rp.)", 
				data: "total_harga", 
				searchable: false,
				orderable: false,
				className: "text-right"
			}
		]
	});

	$(".dataTables_filter").hide();
	
	$(".filter-form").datatableBootstrapFilter(table, [
		[
			4, '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>'
		],
		[
            3, '<div class="form-group"><input type="text" class="form-control" placeholder="Cari Berdasarkan NO PO"></div>'
        ],
        [
            2, '<div class="form-group"><select class="form-control input-xs" name="supplier_id[]" id="supplier" multiple="multiple"></select></div>'
        ],
	], {
		4:0,
        3:1,
        2:2
	}, true);

	dateRangeHelper(".startDate", ".endDate", ".targetDate");

	$(document).on("change", ".startDate, .endDate", function(){
		$(".data-filter").click();
	});

	$("#supplier").select2({
        placeholder: "Cari berdasarkan supplier",
        data: _dataSuppliers,
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