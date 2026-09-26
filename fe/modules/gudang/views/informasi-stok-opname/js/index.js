$("#data-lihat").on("click", function() {
    var primary = $(this).attr("data-id");
    if(primary == "" || typeof primary == 'undefined') {
    	docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
    	return false;
    }
    else {
    	initDatatable(true, primary);
	    // getHeaderPenerimaan(primary);
	    $("#modalLihat").modal({
	        backdrop: "static",
	        keyboard: false
	    });
	    $("#cetak-pdf").attr("data-target", baseUrl+"gudang/informasi-stok-opname/print?id=" + primary);
    }
});
function initDatatable(reinit = false, primary) {
    if ($("#tableDetailPenerimaan").hasClass(".dataTable")) {
        $('#tableDetailPenerimaan').DataTable().clear().destroy();
    }
    else {
        var tableDetail;
        tableDetail = $('#tableDetailPenerimaan').docoTabel({
            filter: false,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollY: true,
            scrollX: true,
            destroy: true,
            paging: false,
            info: false,
            ajax: baseUrl+"gudang/informasi-stok-opname/get-data-detail-modal?primary=" + primary,
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
                    title: "Kelompok Barang", 
                    data: "kelompok_barang"
                },
                {
                    title: "Sub Kelompok Barang", 
                    data: "subkelompok_barang"
                },
                {
                    title: "Tanggal Kadaluarsa", 
                    data: "tglkadaluarsa"
                },
                {
                    title: "Stok Sistem", 
                    data: "volume_sistem",
                },
                {
                    title: "Stok Fisik", 
                    data: "volume_fisik",
                },
                {
                    title: "Selisih", 
                    data: "selisih"
                },
                {
                    title: "Kondisi", 
                    data: "kondisibarang"
                },
            ],
            drawCallback: (setting, a, b) => {
                stokTotal = {
                    fisik: 0,
                    sistem: 0,
                    selisih: 0,
                }
                setting.json.data.map((itemData) => {
                    stokTotal.fisik += itemData.volume_fisik
                    stokTotal.sistem += itemData.volume_sistem
                    stokTotal.selisih += itemData.selisih !== null ? itemData.selisih : 0
                })

                $(".stok_sistem").html(stokTotal.sistem);
                $(".stok_fisik").html(stokTotal.fisik);
                $(".stok_selisih").html(stokTotal.selisih);
            }
        })
    }
}

$('#example tbody').on('click', 'tr', function(){
    try {
        primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
    } catch (e) {
        primaryKey = false;
    }
    if (primaryKey) {
        getHeaderPenerimaan(table.row('.selected').data())
        $('#data-lihat').attr('data-id', primaryKey);
    } else {
        $('#data-lihat').removeAttr('data-id');
    }
});

function getHeaderPenerimaan(data) {
    $(".tglstokopname").html(data.tglstokopname);
    $(".tglformulir").html(data.tglformulir);
    $(".nostokopname").html(data.nostokopname);
    $(".noformulir").html(data.noformulir);
    $(".jenis_stokopname").html(data.jenis_stokopname);

    // $.ajax({
    //     url: baseUrl+"gudang/informasi-stok-opname/view-modal?id=" + primary,
    //     type: "GET",
    //     success: function(data) {
    //         // var tgl_penerimaan = convertTanggalView(data.tgl_penerimaan);
            
    //         // $(".tgl_penerimaan").html(tgl_penerimaan);
    //         // $(".supplier_nama").html(data.supplier_nama);
    //         // $(".no_penerimaan").html(data.no_penerimaan);
    //         // $(".no_faktur").html(data.no_faktur);
    //         // $(".no_surat_jalan").html(data.no_suratjalan);
    //         // $(".tarif_pajak").html(data.pajak_label);
    //         // $(".payment_term").html(data.payterm_nama);

    //         $(".tglstokopname").html(data.tglstokopname);
    //         $(".tglformulir").html(data.tglformulir);
    //         $(".nostokopname").html(data.nostokopname);
    //         $(".noformulir").html(data.noformulir);
    //         $(".jenis_stokopname").html(data.jenis_stokopname);

    //     }
    // });
}