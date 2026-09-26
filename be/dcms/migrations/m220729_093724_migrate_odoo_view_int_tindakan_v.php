<?php

use yii\db\Migration;

/**
 * Class m220729_093724_migrate_odoo_view_int_tindakan_v
 */
class m220729_093724_migrate_odoo_view_int_tindakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_tindakan_v;
        ');

        $this->execute('
            CREATE VIEW "public"."int_tindakan_v" AS  SELECT concat(\'TND\', daftartindakan_m.daftartindakan_id) AS sync_id_api,
                true AS active,
                true AS sale_ok,
                false AS purchase_ok,
                daftartindakan_m.daftartindakan_nama AS name,
                concat(\'TND\', daftartindakan_m.kelompoktindakan_id) AS categ_id,
                351 AS uom_po_id,
                351 AS uom2_id,
                351 AS uom_id,
                daftartindakan_m.daftartindakan_kode AS default_code,
                \'service\'::text AS type,
                    CASE
                        WHEN daftartindakan_m.is_active IS TRUE AND daftartindakan_m.is_deleted IS TRUE THEN true
                        WHEN daftartindakan_m.is_active IS TRUE AND daftartindakan_m.is_deleted IS FALSE THEN false
                        WHEN daftartindakan_m.is_active IS FALSE AND daftartindakan_m.is_deleted IS FALSE THEN true
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
                \'TINDAKAN\'::text AS jenis,
                daftartindakan_m.daftartindakan_id,
                daftartindakan_m.additional_data,
                false AS sync_is_package,
                true AS sync_is_service
               FROM daftartindakan_m;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_093724_migrate_odoo_view_int_tindakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_093724_migrate_odoo_view_int_tindakan_v cannot be reverted.\n";

        return false;
    }
    */
}
