$("#data-lihat").on("click", function() {
    var primary = $(this).attr("data-id");

    if(primary == "" || typeof primary == 'undefined') {
    	docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
    	return false;
    }
    else {
    	initDatatable(true, primary);
	    getHeaderPenerimaan(primary);
	    $("#modalLihat").modal({
	        backdrop: "static",
	        keyboard: false
	    });
	    $("#cetak-pdf").attr("data-target", baseUrl+"gudang/inf-penerimaan-barang-manual/export-pdf?id=" + primary);
    }
});

function initDatatable(reinit = false, primary) {
	if ($("#tableDetailPenerimaan").hasClass(".dataTable")) {
	    $('#tableDetailPenerimaan').DataTable().clear().destroy();
	    
	}
	else {
		var	tableDetail;
			tableDetail = $('#tableDetailPenerimaan').docoTabel({
			filter: false,
			sorting: [[1, "asc"]],
	        displayLength: 10,
	        processing: true,
	        serverSide: true,
	        scrollY: "200px",
	        scrollX: true,
	        destroy: true,
	        paging: false,
	        ajax: baseUrl+"gudang/inf-penerimaan-barang-manual/get-data-detail?primary=" + primary,
	        columns: [
	            {
	                title: "No.",
	                data: "rowNum",
	                searchable: false,
	                orderable: false
	            },
	            {
	                title: "Nama Barang", 
	                data: "barang_nama"
	            },
	            {
	                title: "Qty Penerimaan", 
	                data: "qty_besar"
	            },
	            {
	                title: "Qty Konversi", 
	                data: "qty_kecil"
	            },
	            {
	                title: "Tanggal Kadaluarsa", 
	                data: "tgl_kadaluarsa"
	            },
	            {
	                title: "Harga Netto", 
	                data: "harga_netto",
	                className: "text-right",
	                render: $.fn.dataTable.render.number( '.', ',', 2 )
	            },{
	                title: "Diskon (%)", 
	                data: "diskon",
	                className: "text-right",
	                render: $.fn.dataTable.render.number( '.', ',', 2 )
	            },{
	                title: "No Batch", 
	                data: "no_batch"
	            },{
	                title: "Keterangan", 
	                data: "keterangan"
	            },
	        ],
		})
	}
}

function getHeaderPenerimaan(primary) {
	$.ajax({
		url: baseUrl+"gudang/inf-penerimaan-barang-manual/view?id=" + primary,
		type: "GET",
		success: function(data) {
			var tgl_penerimaan = convertTanggalView(data.tgl_penerimaan);
			
			$(".tgl_penerimaan").html(tgl_penerimaan);
			$(".supplier_nama").html(data.supplier_nama);
			$(".no_penerimaan").html(data.no_penerimaan);
			$(".no_faktur").html(data.no_faktur);
			$(".no_surat_jalan").html(data.no_suratjalan);
			$(".tarif_pajak").html(data.pajak_label);
			$(".payment_term").html(data.payterm_nama);
		}
	})
}