<?php

use yii\db\Migration;

/**
 * Class m230222_152623_GB_1100_pasienkunjungan_v
 */
class m230222_152623_GB_1100_pasienkunjungan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."pasienkunjunganakhir_v";');
        $this->execute("CREATE OR REPLACE VIEW public.pasienkunjunganakhir_v
                        AS SELECT pasien_m.pasien_id,
                        pasien_m.jenisidentitas,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasien_m.tanggal_lahir,
                        pasien_m.alamat_pasien,
                        pasien_m.nopeserta_bpjs,
                        pendaftaran_t.tgl_pendaftaran,
                        pasien_m.is_aps
                        FROM pasien_m
                        LEFT JOIN ( SELECT pendaftaran_t_1.pasien_id, max(pendaftaran_t_1.tgl_pendaftaran) AS tgl_pendaftaran
                        FROM pendaftaran_t pendaftaran_t_1
                        GROUP BY pendaftaran_t_1.pasien_id) pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                        WHERE pasien_m.is_deleted = false AND pasien_m.is_active = true
                       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230222_152623_GB_1100_pasienkunjungan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230222_152623_GB_1100_pasienkunjungan_v cannot be reverted.\n";

        return false;
    }
    */
}
