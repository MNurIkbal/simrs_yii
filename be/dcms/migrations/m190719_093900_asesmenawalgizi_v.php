<?php

use yii\db\Migration;

/**
 * Class m190719_093900_asesmenawalgizi_v
 */
class m190719_093900_asesmenawalgizi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
         DROP VIEW if exists public.asesmenawalgizi_v;
        ');

          $this->execute('
         CREATE OR REPLACE VIEW public.asesmenawalgizi_v AS 
 SELECT pasien.no_rekam_medik,
    pasien.pendaftaran_id,
    pasien.tgl_pendaftaran,
    pasien.no_pendaftaran,
    pasien.nama_pasien,
    pasien.jenis_kelamin,
    pasien.jeniskasuspenyakit_nama,
    pasien.tanggal_lahir,
    pasien.umur,
    pasien.dokter_admisi,
    pasien.kelaspelayanan_nama,
    pasien.kamarruangan_nokamar,
    pasien.no_tempattidur,
    pasien.carabayar_nama,
    pasien.penjamin_nama,
    t.bb_biasanya,
    t.bb_saatini,
    t.perubahan_kg,
    t.perubahan_persen,
    l_perubahan_hasil.lookup_name AS v_perubahan_hasil,
    l_kategori_bb.lookup_name AS v_kategori_bb,
    l_asupanmkn.lookup_name AS v_asupanmkn,
    l_kategori_asupanmkn.lookup_name AS v_kategori_asupanmkn,
    l_gastrointestinal_mual.lookup_name AS v_gastrointestinal_mual,
    l_gastrointestinal_muntah.lookup_name AS v_gastrointestinal_muntah,
    l_gastrointestinal_diare.lookup_name AS v_gastrointestinal_diare,
    l_gastrointestinal_anoreksia.lookup_name AS v_gastrointestinal_anoreksia,
    l_kategori_gastrointestinal.lookup_name AS v_kategori_gastrointestinal,
    l_fungsional.lookup_name AS v_fungsional,
    l_kategori_fungsional.lookup_name AS v_kategori_fungsional,
    t.diagnosa_medis,
    l_keb_metabolik.lookup_name AS v_keb_metabolik,
    l_kategori_hubungan.lookup_name AS v_kategori_hubungan,
    t.fisik_lemak,
    t.fisik_otot,
    t.fisik_udem,
    t.fisik_asites,
    l_kategori_fisik.lookup_name AS v_kategori_fisik,
    l_penilaian_sga.lookup_name AS v_penilaian_sga,
    t.diet,
    t.pagt,
    t.saran_terapi,
    pegawai_m.nama_pegawai,
    t.created_date
   FROM asesmenawalgizi_t t
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            kelaspelayanan_m.kelaspelayanan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id) pasien ON t.pendaftaran_id = pasien.pendaftaran_id
     JOIN lookupkeperawatan_m l_sumberdata ON t.sumberdata = l_sumberdata.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_perubahan_hasil ON t.perubahan_hasil = l_perubahan_hasil.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_kategori_bb ON t.kategori_bb = l_kategori_bb.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_asupanmkn ON t.asupanmkn = l_asupanmkn.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_kategori_asupanmkn ON t.kategori_asupanmkn = l_kategori_asupanmkn.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_gastrointestinal_mual ON t.gastrointestinal_mual = l_gastrointestinal_mual.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_gastrointestinal_muntah ON t.gastrointestinal_muntah = l_gastrointestinal_muntah.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_gastrointestinal_diare ON t.gastrointestinal_diare = l_gastrointestinal_diare.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_gastrointestinal_anoreksia ON t.gastrointestinal_anoreksia = l_gastrointestinal_anoreksia.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_kategori_gastrointestinal ON t.kategori_gastrointestinal = l_kategori_gastrointestinal.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_fungsional ON t.fungsional = l_fungsional.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_kategori_fungsional ON t.kategori_fungsional = l_kategori_fungsional.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_keb_metabolik ON t.keb_metabolik = l_keb_metabolik.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_kategori_hubungan ON t.kategori_hubungan = l_kategori_hubungan.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_kategori_fisik ON t.kategori_fisik = l_kategori_fisik.lookupkeperawatan_id
     JOIN lookupkeperawatan_m l_penilaian_sga ON t.penilaian_sga = l_penilaian_sga.lookupkeperawatan_id
     JOIN loginpemakai_k ON t.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id;
        ');

           $this->execute('
         ALTER TABLE public.asesmenawalgizi_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190719_093900_asesmenawalgizi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190719_093900_asesmenawalgizi_v cannot be reverted.\n";

        return false;
    }
    */
}
