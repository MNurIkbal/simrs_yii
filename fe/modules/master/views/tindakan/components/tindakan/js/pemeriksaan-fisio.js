/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */
 $(document).ready(function() {
    var jenisPemeriksaan = $(`.field-jenis_tindakan_id`);
    var kelompokPemeriksaan = $(`.field-kelompok_pemeriksaan_id`);
    var listRuangan = $(`.field-list_ruangan`);
    var namaKategori = $(`.field-daftartindakanform-kategoritindakan_id`);
    var namaKelompok = $(`.field-kelompoktindakan_id`);
    if (scenario == 'create') {
        if (jenisPemeriksaan && kelompokPemeriksaan) {
            jenisPemeriksaan.addClass('required');
            kelompokPemeriksaan.addClass('required');
            jenisPemeriksaan.hide();
            kelompokPemeriksaan.hide();
            listRuangan.hide();
        }
        $(`[name='DaftarTindakanForm[is_fisio]']`).on('change', function(){
            var isPemeriksaanFisio = $(this).val();
            if (parseInt(isPemeriksaanFisio) == 1) {
                $(`#btn-save`).attr('action', "/master/tindakan/save-tindakan-fisio");
                jenisPemeriksaan.show();
                kelompokPemeriksaan.show();
                listRuangan.show();
                namaKelompok.hide();
                namaKategori.hide();
            }else{
                $(`#btn-save`).attr('action', '/master/tindakan/create-tindakan');
                jenisPemeriksaan.hide();
                kelompokPemeriksaan.hide();
                listRuangan.hide();
                namaKelompok.show();
                namaKategori.show();
            }
        });
    }else if (scenario == 'update' && tindakanFisio == true){
        $(`#btn-save`).attr('action', '/master/tindakan/update-tindakan-fisio');
        if (jenisPemeriksaan && kelompokPemeriksaan) {
            jenisPemeriksaan.addClass('required');
            kelompokPemeriksaan.addClass('required');
            jenisPemeriksaan.show();
            kelompokPemeriksaan.show();
            listRuangan.show();
            namaKelompok.hide();
            namaKategori.hide();
        }
    }
});