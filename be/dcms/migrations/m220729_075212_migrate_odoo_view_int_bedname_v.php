<?php

use yii\db\Migration;

/**
 * Class m220729_075212_migrate_odoo_view_int_bedname_v
 */
class m220729_075212_migrate_odoo_view_int_bedname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_bedname_v; 
        ');

        $this->execute('
            CREATE VIEW "public"."int_bedname_v" AS  
            SELECT kamartempattidur_m.kamartempattidur_id::text AS sync_id_api,
                kamartempattidur_m.kamartempattidur_id,
                kamartempattidur_m.no_tempattidur AS name,
                kamartempattidur_m.kamartempattidur_kode AS code,
                kamartempattidur_m.kamarruangan_id::text AS room_id,
                kamarruangan_m.ruangan_id::text AS ward_id,
                6 AS sync_type,
                kamartempattidur_m.additional_data,
                    CASE
                        WHEN kamartempattidur_m.is_active IS TRUE AND kamartempattidur_m.is_deleted IS TRUE THEN false
                        WHEN kamartempattidur_m.is_active IS TRUE AND kamartempattidur_m.is_deleted IS FALSE THEN true
                        WHEN kamartempattidur_m.is_active IS FALSE AND kamartempattidur_m.is_deleted IS FALSE THEN false
                        ELSE false
                    END AS active,
                    CASE
                        WHEN kamartempattidur_m.is_active IS TRUE AND kamartempattidur_m.is_deleted IS TRUE THEN true
                        WHEN kamartempattidur_m.is_active IS TRUE AND kamartempattidur_m.is_deleted IS FALSE THEN false
                        WHEN kamartempattidur_m.is_active IS FALSE AND kamartempattidur_m.is_deleted IS FALSE THEN true
                        ELSE true
                    END AS wipro_block,
                    CASE
                        WHEN (kamartempattidur_m.additional_data::json ->> \'is_error\'::text) = \'false\'::text THEN \'SUKSES\'::text
                        WHEN (kamartempattidur_m.additional_data::json ->> \'is_error\'::text) = \'true\'::text THEN \'GAGAL\'::text
                        ELSE \'BELUM PROSES\'::text
                    END AS status_proses
               FROM kamartempattidur_m
                 JOIN kamarruangan_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_075212_migrate_odoo_view_int_bedname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_075212_migrate_odoo_view_int_bedname_v cannot be reverted.\n";

        return false;
    }
    */
}
