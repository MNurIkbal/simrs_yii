<?php

use yii\db\Migration;

/**
 * Class m220729_093440_migrate_odoo_view_int_barang_v
 */
class m220729_093440_migrate_odoo_view_int_barang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_barang_v;
        ');

        $this->execute('
            CREATE VIEW "public"."int_barang_v" AS  
            SELECT concat(\'BRG\', barang_m.barang_id) AS sync_id_api,
                true AS active,
                true AS sale_ok,
                true AS purchase_ok,
                barang_m.barang_nama AS name,
                concat(\'BRG\', barang_m.kelompokbarang_id) AS categ_id,
                COALESCE(barang_m.satuan1_id, barang_m.satuankecil_id)::character varying AS uom_po_id,
                COALESCE(barang_m.satuan1_id, barang_m.satuankecil_id)::character varying AS uom2_id,
                barang_m.satuankecil_id::character varying AS uom_id,
                barang_m.barang_kode AS default_code,
                \'product\'::text AS type,
                    CASE
                        WHEN barang_m.is_active IS TRUE AND barang_m.is_deleted IS TRUE THEN true
                        WHEN barang_m.is_active IS TRUE AND barang_m.is_deleted IS FALSE THEN false
                        WHEN barang_m.is_active IS FALSE AND barang_m.is_deleted IS FALSE THEN true
                        ELSE true
                    END AS wipro_block,
                0 AS strength,
                NULL::text AS catalog_code,
                \'-\'::text AS brand,
                NULL::text AS manufacturer_code,
                \'-\'::text AS manufacturer_name,
                NULL::text AS pharmacalogy,
                NULL::text AS shelf,
                    CASE
                        WHEN barang_m.satuan1_id IS NULL THEN 1
                        ELSE barang_m.isi_satuan1
                    END AS conversion_rate,
                \'6\'::text AS sync_type,
                barang_m.barang_id
               FROM barang_m;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_093440_migrate_odoo_view_int_barang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_093440_migrate_odoo_view_int_barang_v cannot be reverted.\n";

        return false;
    }
    */
}
