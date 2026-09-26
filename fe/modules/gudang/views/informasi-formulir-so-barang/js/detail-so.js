$(document).ready(function() {
    table = $("#detailSo").docoTabel({
        searching: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"gudang/informasi-formulir-so-barang/get-data-detail-so?stokopnamebarang_id="+stokopnamebarang_id,
        order: [],
        columns: [
            {
                title: "No", 
                data: "rowNum",
                searchable: false,
                orderable: false,
            },
            {
                title: "Nama Barang",
                data: "barang_nama",
                searchable: false,
                orderable: true,
            },
            {
                title: "Stok Saat Stok Opname", 
                data: "volume_sistem", 
                searchable: false, 
                orderable: false,
                class: "text-right"
            },
            {
                title: "Stok Fisik", 
                data: "volume_fisik",
                orderable: false,
                orderable: false,
                class: "text-right"
            },
            {
                title: "Selisih Stok Opname", 
                data: "selisih_so", 
                searchable: false, 
                orderable: false, 
                class: "text-right"
            },
            {
                title: is_verifikasi ? "Stok Akhir" : "Stok Saat Ini", 
                data: "stok_sistem", 
                searchable: false, 
                orderable: false, 
                class: "text-right"
            },
            {
                title: is_verifikasi ? "Selisih Akhir" : "Selisih Saat ini", 
                data: "stok_selisih", 
                searchable: false, 
                orderable: false, 
                class: "text-right"
            },
            {
                title: "Harga Netto", 
                data: "harganetto", 
                searchable: false, 
                orderable: false, 
                class: "text-right"
            },
            {
                title: "Total Harga Netto", 
                data: "harga_netto_fisik", 
                searchable: false, 
                orderable: false, 
                class: "text-right"
            },
            {
                title: "Total Selisih", 
                data: "total_selisih", 
                searchable: false, 
                orderable: false, 
                class: "text-right"
            },
        ]
    });

    $("#verifikasi").on("click",function(){
        var target = "/gudang/informasi-formulir-so-barang/verifikasi?id="+stokopnamebarang_id;
        var messageText = "Apakah Anda yakin ingin melakukan verifikasi stok opname ini?";

        $(this).attr("action", target);
        $(this).docoForm("delete", {
            confirmMessage: messageText,
            success: function (data) {
                docoNotification('success','Verifikasi Data SO','Verifikasi SO Barang Berhasil')
                setTimeout(() => {
                    window.location.href = "/gudang/informasi-formulir-so-barang";
                }, 150);
            },
            error: function(data) {
                var response = data.responseJSON.data;
                let message = [];

                if (response.data != undefined) {
                    var data = response.data
                    for (const [key, value] of Object.entries(data)) {
                        message.push(value);
                    }
                    docoNotification("error", "Proses Gagal", message.join( "<br />" ))
                } else {
                    docoNotification("error", response.data.meta.title, response.message)
                }
            }
        });
    });
});