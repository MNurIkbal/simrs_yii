<?php

use yii\db\Migration;

/**
 * Class m230509_090142_odoo_cutoff_tindakanpaket
 */
class m230509_090142_odoo_cutoff_tindakanpaket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_tindakanpaket");

        $this->execute("
        CREATE OR REPLACE VIEW public.newodoo_tindakanpaket
        AS SELECT concat('TND', daftartindakan_m.daftartindakan_id) AS sync_id_api,
            true AS active,
            true AS sale_ok,
            false AS purchase_ok,
            daftartindakan_m.daftartindakan_nama AS name,
            concat('TND', daftartindakan_m.kelompoktindakan_id) AS categ_id,
            351 AS uom_po_id,
            351 AS uom2_id,
            351 AS uom_id,
            daftartindakan_m.daftartindakan_kode AS default_code,
            'service'::text AS type,
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
            true as sync_is_service,
            false as sync_is_package,
            'TINDAKAN'::text AS jenis,
            daftartindakan_m.daftartindakan_id AS origin_id
        FROM daftartindakan_m
        UNION ALL
        SELECT concat('PKT', tipepaket_m.tipepaket_id) AS sync_id_api,
            tipepaket_m.is_active AS active,
            true AS sale_ok,
            false AS purchase_ok,
            tipepaket_m.tipepaket_nama AS name,
            concat('CATEG', 10) AS categ_id,
            351 AS uom_po_id,
            351 AS uom2_id,
            351 AS uom_id,
            tipepaket_m.tipepaket_kode AS default_code,
            'service'::text AS type,
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
            true as sync_is_service,
            true as sync_is_package,
            'PAKET'::text AS jenis,
            tipepaket_m.tipepaket_id AS origin_id
        FROM tipepaket_m;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_090142_odoo_cutoff_tindakanpaket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_090142_odoo_cutoff_tindakanpaket cannot be reverted.\n";

        return false;
    }
    */
}
