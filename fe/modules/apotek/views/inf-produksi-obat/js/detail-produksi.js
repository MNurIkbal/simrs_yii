
$(document).ready(function() {
    if(status_produksi == 'Define Material'){
        if(obatTidakTersedia){
            $("#btn-produksi").attr("disabled", true);
        }else{
            $("#btn-produksi").attr("disabled", false)
        }
    }else{
        $("#btn-produksi").attr("disabled", true);
    }

    /**
     * Validasi button table
     */
    validateButton(statusProduksiId)
    
    if(status_produksi == 'Produksi'){
        $("#btn-produksi").attr("disabled", true);
    }else{
        $("#btn-produksi").attr("disabled", false);
    }
    // Generate Table
    table_data = $("#pemesanan-produksi-obat-alkes").docoTabel({
        select: {
            style:    'os',
            selector: 'tr'
        },
        filter: true,
        sorting: [[2,'desc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '/apotek/inf-produksi-obat/get-list-detail-produksi?id='+id,
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                sortable: false
            },
            {
                title: "Nama Obat Produksi",
                data: "obatalkes_nama"
            },
            {
                title: "Qty",
                data: "qty_produksi",
                searchable:false,
            },
            {
                title: "Satuan",
                data: "satuan",
            },
            {
                title: "Pemesan",
                data: "pegawai_pemesanan"
            },
            {
                title: "Tanggal Pemesanan",
                data: "tglpemesanan",
            },
            {
                title: "Status",
                data: "status_produksi",
            },
            {
                title: "Harga Netto  (Rp.)",
                data: "harga_netto",
            },
            {
                title: 'Detail',
                data: 'detail',
                searchable: false,
                orderable: false,
                width:'1%',
                class: 'text-center'
            },
        ],
        createdRow: function ( row, data, index ) {
            for (var i = 0; i < row.childNodes.length; ++i) {
                if (i != row.childNodes) {
                    $(row.childNodes[i]).addClass('detail'+data.produksiobatalkesdetail_id)
                }
            }
        },
        initComplete: function (settings, json) {
            $.each(detailKetersediaan, function(key, value) {
                $(".detail"+value).css("background-color", "#f4baba");
            });
        }
    });
    $(".dataTables_filter").hide();

    $("#btn-edit").click(function (e) { 
        e.preventDefault();
        window.location = `/apotek/inf-produksi-obat/define-material?pemesananproduksi_id=${pemesananProdukid}&isEdit=1`;
    });

    $("#data-batal").click(function (e) {
        confirmationDialog("Apakah anda yakin akan membatalkan produksi obat ?", function (condition) {
            if(condition) {
                $().docoForm('click',{
                    url: "/apotek/inf-produksi-obat/batal-produksi",
                    type: "POST",
                    skipConfirm: true,
                    skipNotifyMessage: true,
                    data: {
                        pemesananproduksi_id: pemesananProdukid
                    },
                    success: function(res) {
                        if(res?.meta?.code == 200) {
                            docoNotification('success', 'Proses Berhasil!', `Proses pembatalan telah berhasil dengan nomor pemesanan: <b>${noPemesanan}</b>`);
                            location.reload();
                        }
                    }
                })
            }
        });
    });
});

const validateButton = (statusProduksiId) => {
    if(statusProduksiId == statusVerifikasi || statusProduksiId == statusDefineMaterial) {
        $("#data-batal").attr("disabled", false)
    }

    if(statusProduksiId == statusDefineMaterial) {
        $("#btn-edit").attr("disabled", false)
    }
}