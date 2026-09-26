<?php

use yii\db\Migration;

/**
 * Class m220906_063147_migrate_hotfix_view_int_paket_v
 */
class m220906_063147_migrate_hotfix_view_int_paket_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_paket_v;
        ');

        $this->execute('
            CREATE VIEW "public"."int_paket_v" AS  SELECT concat(\'PKT\', tipepaket_m.tipepaket_id) AS sync_id_api,
                true AS active,
                true AS sale_ok,
                false AS purchase_ok,
                tipepaket_m.tipepaket_nama AS name,
                concat(\'CATEG\', 10) AS categ_id,
                351 AS uom_po_id,
                351 AS uom2_id, 
                351 AS uom_id,
                tipepaket_m.tipepaket_kode AS default_code,
                \'service\'::text AS type,
                    CASE
                        WHEN tipepaket_m.is_active IS TRUE AND tipepaket_m.is_deleted IS TRUE THEN true
                        WHEN tipepaket_m.is_active IS TRUE AND tipepaket_m.is_deleted IS FALSE THEN false
                        WHEN tipepaket_m.is_active IS FALSE AND tipepaket_m.is_deleted IS FALSE THEN true
                        ELSE true
                    END AS wipro_block,
                NULL::text AS strength,
                NULL::text AS catalog_code,
                NULL::text AS brand,
                NULL::text AS manufacturer_code,
                NULL::text AS manufacturer_name,
                NULL::text AS pharmacalogy,
                NULL::text AS shelf,
                1 AS conversion_rate,
                6 AS sync_type,
                \'PAKET\'::text AS jenis,
                tipepaket_m.tipepaket_id,
                tipepaket_m.additional_data,
                true AS sync_is_package,
                false AS sync_is_service
               FROM tipepaket_m;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220906_063147_migrate_hotfix_view_int_paket_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220906_063147_migrate_hotfix_view_int_paket_v cannot be reverted.\n";

        return false;
    }
    */
}
