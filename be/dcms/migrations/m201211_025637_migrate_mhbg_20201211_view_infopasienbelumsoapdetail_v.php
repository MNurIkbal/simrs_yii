<?php

use yii\db\Migration;

/**
 * Class m201211_025637_migrate_mhbg_20201211_view_infopasienbelumsoapdetail_v
 */
class m201211_025637_migrate_mhbg_20201211_view_infopasienbelumsoapdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienbelumsoapdetail_v;');
        $this->execute("CREATE VIEW \"public\".\"infopasienbelumsoapdetail_v\" AS
             SELECT pendaftaran_t.pegawai_id,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.ruangan_id,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    date(pendaftaran_t.tgl_pendaftaran) AS tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.alamat_pasien
   FROM ((pendaftaran_t
     LEFT JOIN soaprj_t ON (((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id) AND (soaprj_t.is_deleted = false))))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (soaprj_t.soaprj_id IS NULL))
UNION ALL
 SELECT pendaftaran_t.pegawai_id,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.ruangan_id,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    date(pendaftaran_t.tgl_pendaftaran) AS tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.alamat_pasien
   FROM (((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN cppt_t ON ((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id)))
     LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
  WHERE ((pendaftaran_t.instalasi_id = 2) AND (cppt_t.cppt_id IS NULL) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (cppt_t.pasienadmisi_id IS NOT NULL))
UNION ALL
 SELECT pegawai_m.pegawai_id,
    cppt.instalasi_id,
    cppt.ruangan_id,
    cppt.no_rm,
        CASE
            WHEN ((cppt.cppt_id IS NOT NULL) AND (cppt.ket = 'f'::text)) THEN cppt.no_pendaftaran
            WHEN ((cppt.cppt_id IS NULL) AND (cppt.ket = 'f'::text)) THEN cppt.no_pendaftaran
            WHEN (cppt.cppt_id IS NOT NULL) THEN NULL::character varying
            ELSE NULL::character varying
        END AS no_pendaftaran,
    cppt.tgl_pendaftaran,
    cppt.nama_pasien,
    cppt.jenis_kelamin,
    cppt.alamat_pasien
   FROM (pegawai_m
     JOIN ( SELECT (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik AS no_rm,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pasien_m.alamat_pasien,
            COALESCE(cppt_t.pegawai_id, pasienadmisi_t.pegawai_id) AS pegawai_id,
            cppt_t.cppt_id,
                CASE
                    WHEN (pasienadmisi_t.pegawai_id = cppt_t.pegawai_id) THEN 't'::text
                    ELSE 'f'::text
                END AS ket
           FROM ((((pendaftaran_t
             JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = cppt_t.pasienadmisi_id) AND (pasienadmisi_t.pegawai_id = cppt_t.pegawai_id))))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))) cppt ON ((pegawai_m.pegawai_id = cppt.pegawai_id)))
            ;");
            $this->execute('ALTER TABLE public.infopasienbelumsoapdetail_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201211_025637_migrate_mhbg_20201211_view_infopasienbelumsoapdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201211_025637_migrate_mhbg_20201211_view_infopasienbelumsoapdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
