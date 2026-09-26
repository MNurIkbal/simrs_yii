<?php

use yii\db\Migration;

/**
 * Class m210319_041841_oddo_20200319_int_tindakanpaket
 */
class m210319_041841_oddo_20200319_int_tindakanpaket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.int_tindakanpaket;');

        $this->execute("
            CREATE VIEW \"public\".\"int_tindakanpaket\" AS  SELECT concat('TND', daftartindakan_m.daftartindakan_id) AS sync_id_api,
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
    6 AS sync_type,
    'TINDAKAN'::text AS jenis,
    daftartindakan_m.daftartindakan_id AS id,
    daftartindakan_m.additional_data,
        CASE
            WHEN (daftartindakan_m.additional_data::json ->> 'is_sending'::text) = 'true'::text THEN 'SUKSES'::text
            WHEN (daftartindakan_m.additional_data::json ->> 'is_sending'::text) = 'false'::text THEN 'GAGAL'::text
            ELSE 'DALAM PROSES'::text
        END AS status
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
    6 AS sync_type,
    'PAKET'::text AS jenis,
    tipepaket_m.tipepaket_id AS id,
    tipepaket_m.additional_data,
        CASE
            WHEN (tipepaket_m.additional_data::json ->> 'is_sending'::text) = 'true'::text THEN 'SUKSES'::text
            WHEN (tipepaket_m.additional_data::json ->> 'is_sending'::text) = 'false'::text THEN 'GAGAL'::text
            ELSE 'DALAM PROSES'::text
        END AS status
   FROM tipepaket_m;");
        
        $this->execute('ALTER TABLE "public"."int_tindakanpaket" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210319_041841_oddo_20200319_int_tindakanpaket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210319_041841_oddo_20200319_int_tindakanpaket cannot be reverted.\n";

        return false;
    }
    */
}
