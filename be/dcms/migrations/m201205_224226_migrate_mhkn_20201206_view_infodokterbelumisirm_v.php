<?php

use yii\db\Migration;

/**
 * Class m201205_224226_migrate_mhkn_20201206_view_infodokterbelumisirm_v
 */
class m201205_224226_migrate_mhkn_20201206_view_infodokterbelumisirm_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infodokterbelumisirm_v;');
        $this->execute("CREATE VIEW \"public\".\"infodokterbelumisirm_v\" AS
             SELECT 'RJ'::text AS jenis,
    resume_medisrj.tgl_pendaftaran,
    resume_medisrj.instalasi_id,
    resume_medisrj.ruangan_id,
    resume_medisrj.ruangan,
    resume_medisrj.instalasi,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    resume_medisrj.no_pendaftaran AS jumlah
   FROM (pegawai_m
     JOIN ( SELECT (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama AS instalasi,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan
           FROM (((pendaftaran_t
             LEFT JOIN resumemedis_t ON (((pendaftaran_t.pendaftaran_id = resumemedis_t.pendaftaran_id) AND (resumemedis_t.is_deleted = false))))
             JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
          WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (resumemedis_t.resumemedis_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))) resume_medisrj ON ((pegawai_m.pegawai_id = resume_medisrj.pegawai_id)))
  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_deleted = false))
UNION ALL
 SELECT 'RD-RI'::text AS jenis,
    resume_medisrdri.tgl_pendaftaran,
    resume_medisrdri.instalasi_id,
    resume_medisrdri.ruangan_id,
    resume_medisrdri.ruangan,
    resume_medisrdri.instalasi,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    resume_medisrdri.no_pendaftaran AS jumlah
   FROM (pegawai_m
     JOIN ( SELECT (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN peg_rd.pegawai_id
                    ELSE peg_ri.pegawai_id
                END AS pegawai_id,
                CASE
                    WHEN (resumemedisri_t.pasienadmisi_id IS NULL) THEN rd.instalasi_id
                    WHEN (kesimpulanrd_t.pendaftaran_id IS NULL) THEN ri.instalasi_id
                    ELSE NULL::integer
                END AS instalasi_id,
                CASE
                    WHEN (resumemedisri_t.pasienadmisi_id IS NULL) THEN ins_rd.instalasi_nama
                    WHEN (kesimpulanrd_t.pendaftaran_id IS NULL) THEN ins_ri.instalasi_nama
                    ELSE NULL::character varying
                END AS instalasi,
                CASE
                    WHEN (resumemedisri_t.pasienadmisi_id IS NULL) THEN rd.ruangan_id
                    WHEN (kesimpulanrd_t.pendaftaran_id IS NULL) THEN ri.ruangan_id
                    ELSE NULL::integer
                END AS ruangan_id,
                CASE
                    WHEN (resumemedisri_t.pasienadmisi_id IS NULL) THEN rd.ruangan_nama
                    WHEN (kesimpulanrd_t.pendaftaran_id IS NULL) THEN ri.ruangan_nama
                    ELSE NULL::character varying
                END AS ruangan
           FROM (((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN resumemedisri_t ON ((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id)))
             LEFT JOIN kesimpulanrd_t ON ((pendaftaran_t.pendaftaran_id = kesimpulanrd_t.pendaftaran_id)))
             LEFT JOIN pegawai_m peg_rd ON ((pendaftaran_t.pegawai_id = peg_rd.pegawai_id)))
             LEFT JOIN pegawai_m peg_ri ON ((pasienadmisi_t.pegawai_id = peg_ri.pegawai_id)))
             LEFT JOIN ruangan_m rd ON ((pendaftaran_t.ruangan_id = rd.ruangan_id)))
             LEFT JOIN ruangan_m ri ON ((pasienadmisi_t.ruangan_id = ri.ruangan_id)))
             LEFT JOIN instalasi_m ins_rd ON ((rd.instalasi_id = ins_rd.instalasi_id)))
             LEFT JOIN instalasi_m ins_ri ON ((ri.instalasi_id = ins_ri.instalasi_id)))
          WHERE ((resumemedisri_t.resumemedisri_id IS NULL) AND (kesimpulanrd_t.kesimpulanrd_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL) AND (pendaftaran_t.instalasi_id <> 1))) resume_medisrdri ON ((pegawai_m.pegawai_id = resume_medisrdri.pegawai_id)))
  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_deleted = false))
UNION ALL
 SELECT 'RDRI'::text AS jenis,
    resume_medisrj.tgl_pendaftaran,
    resume_medisrj.instalasi_id,
    resume_medisrj.ruangan_id,
    resume_medisrj.ruangan,
    resume_medisrj.instalasi,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter,
    resume_medisrj.no_pendaftaran AS jumlah
   FROM (pegawai_m
     JOIN ( SELECT (to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.pegawai_id,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama AS instalasi,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan
           FROM ((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN resumemedis_t ON (((pendaftaran_t.pendaftaran_id = resumemedis_t.pendaftaran_id) AND (resumemedis_t.is_deleted = false))))
             JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
          WHERE ((ruangan_m.instalasi_id = 3) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (resumemedis_t.resumemedis_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))) resume_medisrj ON ((pegawai_m.pegawai_id = resume_medisrj.pegawai_id)))
  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_deleted = false))
            ;");
            $this->execute('ALTER TABLE public.infodokterbelumisirm_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201205_224226_migrate_mhkn_20201206_view_infodokterbelumisirm_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201205_224226_migrate_mhkn_20201206_view_infodokterbelumisirm_v cannot be reverted.\n";

        return false;
    }
    */
}
