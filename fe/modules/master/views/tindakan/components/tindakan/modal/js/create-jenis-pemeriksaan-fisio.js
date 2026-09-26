/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */
 $(function (){
    $(`.pemeriksaan-catatan`).on('change', function(){
        $(`#pelor`).remove();
        var catatan = $(this).val().length;
        if(catatan > 0 && catatan < 101){
            catatan = $(this).val().length;
        }else if(catatan > 100){
            catatan = $(this).val().length;
            selisih = catatan - 101;
            this.value = this.value.slice(0, -selisih);
            $('.pemeriksaan-catatan').after('<p class="text-error" id="pelor" style="color: red">Maksimal panjang karakter catatan adalah 100.</p>');
        }
    });
    $(`.pemeriksaan-kode`).on('change', function(){
        $(`#pelor`).remove();
        var kodeLen = $(this).val().length;
        if(kodeLen > 0 && kodeLen < 11){
            kodeLen = $(this).val().length;
        }else if(kodeLen > 10){
            $('.pemeriksaan-kode').after('<p class="text-error" id="pelor" style="color: red">Maksimal panjang karakter kode jenis pemeriksaan adalah 10.</p>');
        }
    });

    $(`.pemeriksaan-nama`).on('change', function(){
        var namaLainPemeriksaan = $(`.pemeriksaan-namaLain`).val();
        if(!namaLainPemeriksaan){
            $(`.pemeriksaan-namaLain`).val($(this).val());
        }
    });

    $(`#btn-simpan`).on('click', function(){
        $(`#pelor`).remove();
        $('#pelor-kel').hide();
        var kodePemeriksaan = $(`.pemeriksaan-kode`).val();
        var namaPemeriksaan = $(`.pemeriksaan-nama`).val();
        var namaLainPemeriksaan = $(`.pemeriksaan-namaLain`).val();
        var statusPemeriksaan = 0;
        if($(`#pemeriksaan-status`).prop("checked") == true){
            statusPemeriksaan = 1;
        }
        else if($(`#pemeriksaan-status`).prop("checked") == false){
            statusPemeriksaan = 0;
        }
        var catatanPemeriksaan = $(`.pemeriksaan-catatan`).val();
        var kelompokPemeriksaan = $(`.pemeriksaan-kelompokPemeriksaan`).val();
        if(!kodePemeriksaan){
            $('.pemeriksaan-kode').after('<p class="text-error" id="pelor" style="color: red">Kode jenis pemeriksaan tidak boleh kosong.</p>');
            return docoNotification('error', 'Proses Gagal.', 'Kode pemeriksaan tidak boleh kosong');
        }
        if(kodePemeriksaan.length > 10){
            $('.pemeriksaan-kode').after('<p class="text-error" id="pelor" style="color: red">Maksimal panjang karakter kode jenis pemeriksaan adalah 10.</p>');
            return docoNotification('error', 'Proses Gagal.', 'Maksimal panjang karakter kode jenis pemeriksaan adalah 10');
        }
        if(!namaPemeriksaan){
            $('.pemeriksaan-nama').after('<p class="text-error" id="pelor" style="color: red">Nama jenis pemeriksaan tidak boleh kosong.</p>');
            return docoNotification('error', 'Proses Gagal.', 'Nama pemeriksaan tidak boleh kosong');
        }
        if(!namaLainPemeriksaan){
            $('.pemeriksaan-namaLain').after('<p class="text-error" id="pelor" style="color: red">Nama lain jenis pemeriksaan tidak boleh kosong.</p>');
            return docoNotification('error', 'Proses Gagal.', 'Nama lain pemeriksaan tidak boleh kosong');
        }
        if(!kelompokPemeriksaan){
            $('#pelor-kel').show();
            return docoNotification('error', 'Proses Gagal.', 'Kelompok pemeriksaan tidak boleh kosong');
        }
        var data = {
            kodePemeriksaan : kodePemeriksaan,
            namaPemeriksaan : namaPemeriksaan,
            namaLainPemeriksaan : namaLainPemeriksaan,
            statusPemeriksaan : statusPemeriksaan,
            catatanPemeriksaan : catatanPemeriksaan,
            kelompokPemeriksaan: kelompokPemeriksaan,
            isNonpaket : true
        }
        $().docoForm("click", {
            url: "/master/tindakan/save-jenis-pemeriksaan-fisio",
            data: { data },
            confirmMessage:
              "Apakah Anda yakin akan menyimpan jenis tindakan fisioterapi ini?",
            success: (response) => {
              $("#modal-satu").modal("hide");
              if(response.response.data.is_aktif == true){
                $('#jenis_tindakan_id').select2({
                    allowClear: false,
                    data: [
                            {   
                                id: response.response.data.jenispemeriksaanfisio_id, 
                                text: response.response.data.jenispemeriksaanfisio_nama
                            },
                        ]
                });
              }
               
            },
            error: function (response) {
              response = response.responseJSON.response;
              docoNotification("error", response.title, response.message);
            },
          });
    });
});