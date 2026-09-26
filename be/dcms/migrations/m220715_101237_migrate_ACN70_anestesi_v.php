<?php

use yii\db\Migration;

/**
 * Class m220715_101237_migrate_ACN70_anestesi_v
 */
class m220715_101237_migrate_ACN70_anestesi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."anestesi_v";');
        $this->execute("CREATE OR REPLACE VIEW public.anestesi_v
        AS SELECT anestesi_t.anestesi_id,
            anestesi_t.pasienmasukpenunjang_id,
            anestesi_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            anestesi_t.anestesi_result AS anestesi_result_id,
            look_anestesiresult.lookup_name AS anestesi_result_nama,
            anestesi_t.anestesi_regional AS anestesi_regional_id,
            look_anestesiregional.lookup_name AS anestesi_regional_nama,
            anestesi_t.anestesi_regional_other,
            anestesi_t.type_needle_size,
            anestesi_t.lenght_catheter_isertion,
            anestesi_t.anestesi_general AS anestesi_general_id,
            look_anestesigeneral.lookup_name AS anestesi_general_nama,
            anestesi_t.patient_position,
            anestesi_t.preinduction,
            anestesi_t.induction,
            anestesi_t.maintenance,
            anestesi_t.recovery
           FROM anestesi_t
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.pasien_id,
                    a.no_pendaftaran
                   FROM pendaftaran_t a) pendaftaran_t ON anestesi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.lookupkeperawatan_id,
                    a.lookup_name
                   FROM lookupkeperawatan_m a) look_anestesiresult ON anestesi_t.anestesi_result = look_anestesiresult.lookupkeperawatan_id
             LEFT JOIN ( SELECT a.lookupkeperawatan_id,
                    a.lookup_name
                   FROM lookupkeperawatan_m a) look_anestesiregional ON anestesi_t.anestesi_regional = look_anestesiregional.lookupkeperawatan_id
             LEFT JOIN ( SELECT a.lookupkeperawatan_id,
                    a.lookup_name
                   FROM lookupkeperawatan_m a) look_anestesigeneral ON anestesi_t.anestesi_general = look_anestesigeneral.lookupkeperawatan_id
          WHERE anestesi_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_101237_migrate_ACN70_anestesi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_101237_migrate_ACN70_anestesi_v cannot be reverted.\n";

        return false;
    }
    */
}
