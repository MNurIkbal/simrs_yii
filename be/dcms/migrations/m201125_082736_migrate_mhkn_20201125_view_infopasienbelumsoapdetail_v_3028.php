<?php

use yii\db\Migration;

/**
 * Class m201125_082736_migrate_mhkn_20201125_view_infopasienbelumsoapdetail_v_3028
 */
class m201125_082736_migrate_mhkn_20201125_view_infopasienbelumsoapdetail_v_3028 extends Migration
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
   FROM (((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (cppt_t.is_deleted = false))))
     LEFT JOIN ruangan_m rd ON ((pendaftaran_t.ruangan_id = rd.ruangan_id)))
     LEFT JOIN ruangan_m ri ON ((pasienadmisi_t.ruangan_id = ri.ruangan_id)))
  WHERE ((pendaftaran_t.pegawai_id IS NOT NULL) AND (cppt_t.cppt_id IS NULL))
            ;");
            $this->execute('ALTER TABLE public.infopasienbelumsoapdetail_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_082736_migrate_mhkn_20201125_view_infopasienbelumsoapdetail_v_3028 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_082736_migrate_mhkn_20201125_view_infopasienbelumsoapdetail_v_3028 cannot be reverted.\n";

        return false;
    }
    */
}
