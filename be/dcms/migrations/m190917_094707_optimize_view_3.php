<?php

use yii\db\Migration;

/**
 * Class m190917_094707_optimize_view_3
 */
class m190917_094707_optimize_view_3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('DROP VIEW if exists public.dashboardkamardetail_v;');


        $this->execute("
            CREATE OR REPLACE VIEW public.dashboardkamardetail_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pasienadmisi_t.pasienpulang_id,
    pasienpulang_t.tglpasienpulang,
    pindahkamar_t.pindahkamar_id,
    pindahkamar_t.tgl_pindahkamar,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.alamat_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pasienadmisi_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    pasienadmisi_t.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dr_dpjp,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN pindahkamar_t ON pasienadmisi_t.pasienadmisi_id = pindahkamar_t.pasienadmisi_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id;");


        $this->execute('ALTER TABLE public.dashboardkamardetail_v
  OWNER TO postgres;');

    $this->execute('DROP VIEW if exists public.hasillab_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.hasillab_v AS 
 SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
    hasilpemeriksaanlab_t.nohasilperiksalab,
    hasilpemeriksaanlab_t.tgl_hasilpemeriksaanlab,
    pegawailab.nama_pegawai AS penganggung_jawab,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    dokter.nama_pegawai,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    hasilpemeriksaanlab_t.expertise,
    ambilsample_t.samplelab_id,
    samplelab_m.nama_sample,
    hasilpemeriksaanlab_t.tgl_verifikasi
   FROM hasilpemeriksaanlab_t
     JOIN pegawai_m pegawailab ON hasilpemeriksaanlab_t.pegawailab_id = pegawailab.pegawai_id
     JOIN pasienmasukpenunjang_t ON hasilpemeriksaanlab_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dokter ON pasienmasukpenunjang_t.pegawai_id = dokter.pegawai_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN ambilsample_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id
     JOIN samplelab_m ON ambilsample_t.ambilsample_id = ambilsample_t.ambilsample_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamarruangan_id;
");

    $this->execute('ALTER TABLE public.hasillab_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_094707_optimize_view_3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_094707_optimize_view_3 cannot be reverted.\n";

        return false;
    }
    */
}
