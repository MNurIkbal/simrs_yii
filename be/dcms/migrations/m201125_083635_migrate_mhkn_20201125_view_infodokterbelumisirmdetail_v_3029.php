<?php

use yii\db\Migration;

/**
 * Class m201125_083635_migrate_mhkn_20201125_view_infodokterbelumisirmdetail_v_3029
 */
class m201125_083635_migrate_mhkn_20201125_view_infodokterbelumisirmdetail_v_3029 extends Migration
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
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.alamat_pasien
   FROM ((pendaftaran_t
     LEFT JOIN resumemedis_t ON ((pendaftaran_t.pendaftaran_id = resumemedis_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.pegawai_id IS NOT NULL) AND (resumemedis_t.resumemedis_id IS NULL))
UNION ALL
 SELECT pendaftaran_t.pegawai_id,
        CASE
            WHEN (resumemedisri_t.pasienadmisi_id IS NULL) THEN rd.instalasi_id
            ELSE ri.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN (resumemedisri_t.pasienadmisi_id IS NULL) THEN rd.ruangan_id
            ELSE ri.ruangan_id
        END AS ruangan_id,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.alamat_pasien
   FROM (((((pendaftaran_t
     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN resumemedisri_t ON ((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id)))
     LEFT JOIN ruangan_m ri ON ((pendaftaran_t.ruangan_id = ri.ruangan_id)))
     LEFT JOIN ruangan_m rd ON ((pendaftaran_t.ruangan_id = rd.ruangan_id)))
  WHERE ((pendaftaran_t.pegawai_id IS NOT NULL) AND (resumemedisri_t.resumemedisri_id IS NULL))
            ;");
            $this->execute('ALTER TABLE public.infodokterbelumisirmdetail_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_083635_migrate_mhkn_20201125_view_infodokterbelumisirmdetail_v_3029 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_083635_migrate_mhkn_20201125_view_infodokterbelumisirmdetail_v_3029 cannot be reverted.\n";

        return false;
    }
    */
}
