<?php

use yii\db\Migration;

/**
 * Class m230105_152227_migrate_hotfix_view_infopindahkamar_v
 */
class m230105_152227_migrate_hotfix_view_infopindahkamar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopindahkamar_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopindahkamar_v" AS  SELECT pindahkamar_t.pindahkamar_id,
    pasienadmisi_t.pasien_id,
    pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.pegawai_id AS pegawaipendaftaran_id,
    pasienadmisi_t.pegawai_id AS pegawaiadmisi_id,
    pindahkamar_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id AS ruangan_sekarang_id,
    pindahkamar_t.ruangan_id AS ruangan_pindah_id,
    pasienadmisi_t.tgl_admisi,
    pindahkamar_t.tgl_pindahkamar,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran, 
    pasien_m.nama_pasien,
    lkp_jk.lookup_name AS jenis_kelamin,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_asal.ruangan_nama AS ruangan_sekarang,
    kamar_asal.kamarruangan_nokamar AS kamar_sekarang,
    tempattidur_asal.no_tempattidur AS tempattidur_sekarang,
    ruangan_pindah.ruangan_nama AS ruangan_pindah,
    kamar_pindah.kamarruangan_nokamar AS kamar_pindah,
    tempattidur_pindah.no_tempattidur AS tempattidur_pindah,
    pasienadmisi_t.is_stoptitipan,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    pindahkamar_t.is_stoptitipan AS is_stoptitipan_pk,
    pindahkamar_t.is_pasientitipan AS is_pasientitipan_pk,
    pindahkamar_t.kelas_ditagihkan_id AS kelas_ditagihkan_id_pk,
    kelas_ditagihkan_pk.kelaspelayanan_nama AS kelas_ditagihkan_nama_pk
   FROM pasienadmisi_t
     JOIN ( SELECT a.pendaftaran_id,
            a.jeniskasuspenyakit_id,
            a.pegawai_id,
            a.no_pendaftaran,
            a.pasienadmisi_id,
            a.pasien_id
           FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.jeniskelamin
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.pasienadmisi_id,
            a.pindahkamar_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id
           FROM masukkamar_t a) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
     JOIN ( SELECT a.pindahkamar_id,
            a.kelaspelayanan_id,
            a.ruangan_id,
            a.tgl_pindahkamar,
            a.is_stoptitipan,
            a.is_pasientitipan,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.kelas_ditagihkan_id,
            a.is_deleted,
            a.is_active
           FROM pindahkamar_t a) pindahkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
     JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pindahkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ( SELECT ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama
           FROM ruangan_m) ruangan_asal ON masukkamar_t.ruangan_id = ruangan_asal.ruangan_id
     JOIN ( SELECT kamarruangan_m.kamarruangan_id,
            kamarruangan_m.kamarruangan_nokamar
           FROM kamarruangan_m) kamar_asal ON masukkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id
     JOIN ( SELECT kamartempattidur_m.kamarruangan_id,
            kamartempattidur_m.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur
           FROM kamartempattidur_m) tempattidur_asal ON masukkamar_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id
     JOIN ( SELECT ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama
           FROM ruangan_m) ruangan_pindah ON pindahkamar_t.ruangan_id = ruangan_pindah.ruangan_id
     JOIN ( SELECT kamarruangan_m.kamarruangan_id,
            kamarruangan_m.kamarruangan_nokamar
           FROM kamarruangan_m) kamar_pindah ON pindahkamar_t.kamarruangan_id = kamar_pindah.kamarruangan_id
     JOIN ( SELECT kamartempattidur_m.kamarruangan_id,
            kamartempattidur_m.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur
           FROM kamartempattidur_m) tempattidur_pindah ON pindahkamar_t.kamartempattidur_id = tempattidur_pindah.kamartempattidur_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelas_ditagihkan_pk ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_pk.kelaspelayanan_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) lkp_jk ON pasien_m.jeniskelamin::integer = lkp_jk.lookup_id
  WHERE pindahkamar_t.is_active = true AND pindahkamar_t.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230105_152227_migrate_hotfix_view_infopindahkamar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230105_152227_migrate_hotfix_view_infopindahkamar_v cannot be reverted.\n";

        return false;
    }
    */
}
