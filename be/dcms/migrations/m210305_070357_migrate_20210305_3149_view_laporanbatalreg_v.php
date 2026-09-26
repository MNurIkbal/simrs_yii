<?php

use yii\db\Migration;

/**
 * Class m210305_070357_migrate_20210305_3149_view_laporanbatalreg_v
 */
class m210305_070357_migrate_20210305_3149_view_laporanbatalreg_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanbatalreg_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanbatalreg_v\" AS
             SELECT 'RJ'::text AS jenis,
    pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienbatalperiksa_t.alasan_batal,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS nama_petugas,
    pasienbatalperiksa_t.created_date AS tgl_batal
   FROM (((((((pendaftaran_t
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
     JOIN loginpemakai_k ON ((pasienbatalperiksa_t.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NOT NULL))
UNION ALL
 SELECT 'RI'::text AS jenis,
    pasienadmisi_t.tgl_pendaftaran AS tgl_registrasi,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rd.ruangan_id
            ELSE ruangan_ri.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rd.ruangan_nama
            ELSE ruangan_ri.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_rd.instalasi_id
            ELSE ins_ri.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_rd.instalasi_nama
            ELSE ins_ri.instalasi_nama
        END AS instalasi_nama,
    pasienbatalperiksa_t.alasan_batal,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_rd.pegawai_id
            ELSE dok_ri.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_rd.nama_pegawai
            ELSE dok_ri.nama_pegawai
        END AS nama_petugas,
    pasienbatalperiksa_t.created_date AS tgl_batal
   FROM ((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasienpulang_t pulang_rd ON ((pendaftaran_t.pasienpulang_id = pulang_rd.pasienpulang_id)))
     LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN ruangan_m ruangan_rd ON ((pendaftaran_t.ruangan_id = ruangan_rd.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_ri ON ((pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id)))
     LEFT JOIN instalasi_m ins_rd ON ((pendaftaran_t.instalasi_id = ins_rd.instalasi_id)))
     LEFT JOIN instalasi_m ins_ri ON ((ruangan_ri.instalasi_id = ins_ri.instalasi_id)))
     LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
     LEFT JOIN loginpemakai_k ON ((pasienbatalperiksa_t.created_by = loginpemakai_k.loginpemakai_id)))
     LEFT JOIN pegawai_m dok_rd ON ((loginpemakai_k.pegawai_id = dok_rd.pegawai_id)))
     LEFT JOIN pegawai_m dok_ri ON ((loginpemakai_k.pegawai_id = dok_ri.pegawai_id)))
  WHERE ((ruangan_ri.instalasi_id = 3) AND (pendaftaran_t.pasienbatalperiksa_id IS NOT NULL))
UNION ALL
 SELECT 'RD'::text AS jenis,
    pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rd.ruangan_id
            ELSE ruangan_ri.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rd.ruangan_nama
            ELSE ruangan_ri.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_rd.instalasi_id
            ELSE ins_ri.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_rd.instalasi_nama
            ELSE ins_ri.instalasi_nama
        END AS instalasi_nama,
    pasienbatalperiksa_t.alasan_batal,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_rd.pegawai_id
            ELSE dok_ri.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dok_rd.nama_pegawai
            ELSE dok_ri.nama_pegawai
        END AS nama_petugas,
    pasienbatalperiksa_t.created_date AS tgl_batal
   FROM ((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasienpulang_t pulang_rd ON ((pendaftaran_t.pasienpulang_id = pulang_rd.pasienpulang_id)))
     LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN ruangan_m ruangan_rd ON ((pendaftaran_t.ruangan_id = ruangan_rd.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_ri ON ((pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id)))
     LEFT JOIN instalasi_m ins_rd ON ((pendaftaran_t.instalasi_id = ins_rd.instalasi_id)))
     LEFT JOIN instalasi_m ins_ri ON ((ruangan_ri.instalasi_id = ins_ri.instalasi_id)))
     LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
     LEFT JOIN loginpemakai_k ON ((pasienbatalperiksa_t.created_by = loginpemakai_k.loginpemakai_id)))
     LEFT JOIN pegawai_m dok_rd ON ((loginpemakai_k.pegawai_id = dok_rd.pegawai_id)))
     LEFT JOIN pegawai_m dok_ri ON ((loginpemakai_k.pegawai_id = dok_ri.pegawai_id)))
  WHERE ((pendaftaran_t.instalasi_id = 2) AND (pendaftaran_t.pasienbatalperiksa_id IS NOT NULL))
UNION ALL
 SELECT 'LAB'::text AS jenis,
    pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienbatalperiksa_t.alasan_batal,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS nama_petugas,
    pasienbatalperiksa_t.created_date AS tgl_batal
   FROM (((((((pendaftaran_t
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
     JOIN loginpemakai_k ON ((pasienbatalperiksa_t.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
  WHERE ((pendaftaran_t.instalasi_id = 4) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NOT NULL))
UNION ALL
 SELECT 'MCU'::text AS jenis,
    pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienbatalperiksa_t.alasan_batal,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS nama_petugas,
    pasienbatalperiksa_t.created_date AS tgl_batal
   FROM (((((((pendaftaran_t
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
     JOIN loginpemakai_k ON ((pasienbatalperiksa_t.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
  WHERE ((pendaftaran_t.instalasi_id = 21) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NOT NULL))
UNION ALL
 SELECT 'IBS'::text AS jenis,
    pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienbatalperiksa_t.alasan_batal,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS nama_petugas,
    pasienbatalperiksa_t.created_date AS tgl_batal
   FROM (((((((pendaftaran_t
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
     JOIN loginpemakai_k ON ((pasienbatalperiksa_t.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
  WHERE ((pendaftaran_t.instalasi_id = 12) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NOT NULL))
UNION ALL
 SELECT 'RAD'::text AS jenis,
    pendaftaran_t.tgl_pendaftaran AS tgl_registrasi,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS no_registrasi,
    pasien_m.nama_pasien,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienbatalperiksa_t.alasan_batal,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS nama_petugas,
    pasienbatalperiksa_t.created_date AS tgl_batal
   FROM (((((((pendaftaran_t
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
     JOIN loginpemakai_k ON ((pasienbatalperiksa_t.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
  WHERE ((pendaftaran_t.instalasi_id = 5) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NOT NULL))
            ;");
            $this->execute('
                ALTER TABLE public.laporanbatalreg_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210305_070357_migrate_20210305_3149_view_laporanbatalreg_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210305_070357_migrate_20210305_3149_view_laporanbatalreg_v cannot be reverted.\n";

        return false;
    }
    */
}
