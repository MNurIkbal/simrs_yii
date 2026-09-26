<?php

use yii\db\Migration;

/**
 * Class m220715_101600_migrate_ACN70_anestesidetail_v
 */
class m220715_101600_migrate_ACN70_anestesidetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."anestesidetail_v";');
        $this->execute("CREATE OR REPLACE VIEW public.anestesidetail_v
        AS SELECT anestesidetail_t.anestesidetail_id,
            anestesidetail_t.anestesi_id,
            anestesi_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            anestesidetail_t.obatalkes_id,
            obatalkes_m.obatalkes_kode,
            obatalkes_m.obatalkes_nama,
            anestesidetail_t.dose,
            anestesidetail_t.time_delivery
           FROM anestesidetail_t
             JOIN ( SELECT a.anestesi_id,
                    a.pendaftaran_id
                   FROM anestesi_t a) anestesi_t ON anestesidetail_t.anestesi_id = anestesi_t.anestesi_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.pasien_id
                   FROM pendaftaran_t a) pendaftaran_t ON anestesi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_kode,
                    a.obatalkes_nama
                   FROM obatalkes_m a) obatalkes_m ON anestesidetail_t.obatalkes_id = obatalkes_m.obatalkes_id
          WHERE anestesidetail_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_101600_migrate_ACN70_anestesidetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_101600_migrate_ACN70_anestesidetail_v cannot be reverted.\n";

        return false;
    }
    */
}
