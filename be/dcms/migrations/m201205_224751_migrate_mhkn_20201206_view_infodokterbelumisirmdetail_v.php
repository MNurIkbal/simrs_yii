<?php

use yii\db\Migration;

/**
 * Class m201205_224751_migrate_mhkn_20201206_view_infodokterbelumisirmdetail_v
 */
class m201205_224751_migrate_mhkn_20201206_view_infodokterbelumisirmdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infodokterbelumisirmdetail_v;');
        $this->execute("CREATE VIEW \"public\".\"infodokterbelumisirmdetail_v\" AS
             SELECT pendaftaran_t.pegawai_id,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.ruangan_id,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.alamat_pasien
   FROM ((pendaftaran_t
     LEFT JOIN resumemedis_t ON ((pendaftaran_t.pendaftaran_id = resumemedis_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (resumemedis_t.resumemedis_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
UNION ALL
 SELECT
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN peg_rd.pegawai_id
            ELSE peg_ri.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN (resumemedisri_t.pasienadmisi_id IS NULL) THEN rd.instalasi_id
            WHEN ((kesimpulanrd_t.pendaftaran_id IS NULL) OR (resumemedisri_t.pasienadmisi_id IS NOT NULL)) THEN ri.instalasi_id
            ELSE NULL::integer
        END AS instalasi_id,
        CASE
            WHEN (resumemedisri_t.pasienadmisi_id IS NULL) THEN rd.ruangan_id
            WHEN ((kesimpulanrd_t.pendaftaran_id IS NULL) OR (resumemedisri_t.pasienadmisi_id IS NOT NULL)) THEN ri.instalasi_id
            ELSE NULL::integer
        END AS ruangan_id,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.alamat_pasien
   FROM ((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN resumemedisri_t ON ((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id)))
     LEFT JOIN kesimpulanrd_t ON ((pendaftaran_t.pendaftaran_id = kesimpulanrd_t.pendaftaran_id)))
     LEFT JOIN pegawai_m peg_rd ON ((pendaftaran_t.pegawai_id = peg_rd.pegawai_id)))
     LEFT JOIN pegawai_m peg_ri ON ((pasienadmisi_t.pegawai_id = peg_ri.pegawai_id)))
     LEFT JOIN ruangan_m ri ON ((pasienadmisi_t.ruangan_id = ri.ruangan_id)))
     LEFT JOIN ruangan_m rd ON ((pendaftaran_t.ruangan_id = rd.ruangan_id)))
  WHERE ((resumemedisri_t.resumemedisri_id IS NULL) AND (kesimpulanrd_t.kesimpulanrd_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL) AND (pendaftaran_t.instalasi_id <> 1))
UNION ALL
 SELECT pasienadmisi_t.pegawai_id,
    ruangan_m.instalasi_id,
    pasienadmisi_t.ruangan_id,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    (to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.alamat_pasien
   FROM ((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN resumemedis_t ON ((pendaftaran_t.pendaftaran_id = resumemedis_t.pendaftaran_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((ruangan_m.instalasi_id = 3) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (resumemedis_t.resumemedis_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            ;");
            $this->execute('ALTER TABLE public.infodokterbelumisirmdetail_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201205_224751_migrate_mhkn_20201206_view_infodokterbelumisirmdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201205_224751_migrate_mhkn_20201206_view_infodokterbelumisirmdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
