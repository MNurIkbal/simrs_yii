$(document).ready(function () {
    $('#approvalproduksiobatform-pegawai_id').select2({
        ajax: {
            url: '/apotek/inf-produksi-obat/list-pegawai',
            dataType: 'json',
            quietMillis: 250,
            data: function (params) {
                var query = {
                    search: params,
                }
                return params;
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
        },
    });
});
    
$(document).on("click","#btn-simpan-alert", function (event) {
    event.preventDefault();
    $().docoForm("click",{
        url : "/apotek/inf-produksi-obat/save-produksi-obat?id=" + id,
        method : "POST",
        data : {
            pegawai_id: $("#approvalproduksiobatform-pegawai_id").val(),
            tgl_produksi: $("#approvalproduksiobatform-tgl_produksi").val(),
            tgl_kadaluarsa: $("#approvalproduksiobatform-tgl_kadaluarsa").val(),
            batch_number: $("#approvalproduksiobatform-batch_number").val(),
        },
        success : function (data) {
            docoNotification("success", 'Berhasil', data['text']);
            $('.btn-tidak').trigger('click');
            $.ajax({
                type: "GET",
                url: "/apotek/inf-produksi-obat/get-alert-harga?id="+id,
                success: function (response) {
                    if(response?.recordsTotal > 0) {
                        setTimeout(function () {
                            $("#btn-alert").trigger('click');       
                        }, 500)
                    }else{
                        window.location.replace('/apotek/inf-produksi-obat/index-produksi');
                    }
                }
            });
        },
        error :function(data){
            if(data.responseJSON.message != undefined){
                docoNotification('error', 'Proses Gagal', data.responseJSON.message);
                $('.btn-tidak').trigger('click');
            }
        }

    });
});