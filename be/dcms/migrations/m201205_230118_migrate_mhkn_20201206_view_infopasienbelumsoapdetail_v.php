<?php

use yii\db\Migration;

/**
 * Class m201205_230118_migrate_mhkn_20201206_view_infopasienbelumsoapdetail_v
 */
class m201205_230118_migrate_mhkn_20201206_view_infopasienbelumsoapdetail_v extends Migration
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
  WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[1])) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (soaprj_t.soaprj_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
UNION ALL
 SELECT
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN peg_rd.pegawai_id
            ELSE peg_ri.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN (cppt_t.pasienadmisi_id IS NULL) THEN rd.instalasi_id
            ELSE ri.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN (cppt_t.pasienadmisi_id IS NULL) THEN rd.ruangan_id
            ELSE ri.ruangan_id
        END AS ruangan_id,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    date(pendaftaran_t.tgl_pendaftaran) AS tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.alamat_pasien
   FROM (((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (cppt_t.is_deleted = false))))
     LEFT JOIN pegawai_m peg_rd ON ((pendaftaran_t.pegawai_id = peg_rd.pegawai_id)))
     LEFT JOIN pegawai_m peg_ri ON ((pasienadmisi_t.pegawai_id = peg_ri.pegawai_id)))
     LEFT JOIN ruangan_m rd ON ((pendaftaran_t.ruangan_id = rd.ruangan_id)))
     LEFT JOIN ruangan_m ri ON ((pasienadmisi_t.ruangan_id = ri.ruangan_id)))
  WHERE ((cppt_t.cppt_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL) AND (pendaftaran_t.instalasi_id <> 1))
UNION ALL
 SELECT pasienadmisi_t.pegawai_id,
    ruangan_m.instalasi_id,
    pasienadmisi_t.ruangan_id,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    date(pasienadmisi_t.tgl_pendaftaran) AS tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.alamat_pasien
   FROM (((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN soaprj_t ON (((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id) AND (soaprj_t.is_deleted = false))))
     LEFT JOIN cppt_t ON ((pasienadmisi_t.pasienadmisi_id = cppt_t.pasienadmisi_id)))
     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((ruangan_m.instalasi_id = 3) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (soaprj_t.soaprj_id IS NULL) AND (cppt_t.cppt_id IS NULL) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            ;");
            $this->execute('ALTER TABLE public.infopasienbelumsoapdetail_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201205_230118_migrate_mhkn_20201206_view_infopasienbelumsoapdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201205_230118_migrate_mhkn_20201206_view_infopasienbelumsoapdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
