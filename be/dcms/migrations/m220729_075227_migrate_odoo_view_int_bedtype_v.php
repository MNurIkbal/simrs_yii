<?php

use yii\db\Migration;

/**
 * Class m220729_075227_migrate_odoo_view_int_bedtype_v
 */
class m220729_075227_migrate_odoo_view_int_bedtype_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_bedtype_v;
        ');

        $this->execute('
            CREATE VIEW "public"."int_bedtype_v" AS  
            SELECT kelaspelayanan_m.kelaspelayanan_id::text AS sync_id_api,
                kelaspelayanan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama AS name,
                kelaspelayanan_m.kelaspelayanan_kode AS code,
                6 AS sync_type,
                kelaspelayanan_m.additional_data,
                    CASE
                        WHEN kelaspelayanan_m.is_active IS TRUE AND kelaspelayanan_m.is_deleted IS TRUE THEN false
                        WHEN kelaspelayanan_m.is_active IS TRUE AND kelaspelayanan_m.is_deleted IS FALSE THEN true
                        WHEN kelaspelayanan_m.is_active IS FALSE AND kelaspelayanan_m.is_deleted IS FALSE THEN false
                        ELSE false
                    END AS active,
                    CASE
                        WHEN kelaspelayanan_m.is_active IS TRUE AND kelaspelayanan_m.is_deleted IS TRUE THEN true
                        WHEN kelaspelayanan_m.is_active IS TRUE AND kelaspelayanan_m.is_deleted IS FALSE THEN false
                        WHEN kelaspelayanan_m.is_active IS FALSE AND kelaspelayanan_m.is_deleted IS FALSE THEN true
                        ELSE true
                    END AS wipro_block,
                    CASE
                        WHEN (kelaspelayanan_m.additional_data::json ->> \'is_error\'::text) = \'false\'::text THEN \'SUKSES\'::text
                        WHEN (kelaspelayanan_m.additional_data::json ->> \'is_error\'::text) = \'true\'::text THEN \'GAGAL\'::text
                        ELSE \'BELUM PROSES\'::text
                    END AS status_proses,
                    CASE
                        WHEN kelaspelayanan_m.instalasi_id IS NULL THEN 0
                        ELSE kelaspelayanan_m.instalasi_id
                    END AS patient_type
               FROM kelaspelayanan_m;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_075227_migrate_odoo_view_int_bedtype_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_075227_migrate_odoo_view_int_bedtype_v cannot be reverted.\n";

        return false;
    }
    */
}
