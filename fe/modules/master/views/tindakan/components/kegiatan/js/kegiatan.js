/*
* @Author: Sigit
* @Date:   2019-03-22 16:13:51
*/

$("#jeniskegiatanform-jeniskegiatantindakan_nama").on("change", function() {
    var nama = $(this).val();
    var nama_lainnya = $("#jeniskegiatanform-jeniskegiatan_namalainnya").val();

    if (nama_lainnya == "") {
        $("#jeniskegiatanform-jeniskegiatan_namalainnya").val(nama);
    } else {
        $("#jeniskegiatanform-jeniskegiatan_namalainnya").val(nama_lainnya);
    }
});