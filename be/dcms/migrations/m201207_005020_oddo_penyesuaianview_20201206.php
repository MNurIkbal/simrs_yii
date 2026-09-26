<?php

use yii\db\Migration;

/**
 * Class m201207_005020_oddo_penyesuaianview_20201206
 */
class m201207_005020_oddo_penyesuaianview_20201206 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_kelompoktindakan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_kelompoktindakan_v\" AS  SELECT concat('TND', kelompoktindakan_m.kelompoktindakan_id) AS sync_id_api,
    '-'::text AS parent_id,
    kelompoktindakan_m.kelompoktindakan_nama AS name,
    true AS sync_is_service,
    'normal'::text AS type,
    kelompoktindakan_m.is_active AS active,
    6 AS sync_type,
    kelompoktindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.additional_data
   FROM kelompoktindakan_m
  WHERE kelompoktindakan_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."int_kelompoktindakan_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_servicecategory_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_servicecategory_v\" AS  SELECT
        CASE
            WHEN servicecategory_m.is_obat = false THEN concat('CATEG', servicecategory_m.servicecategory_id)
            ELSE concat('CATEG', servicecategory_m.servicecategory_id)
        END AS sync_id_api,
    '-'::text AS parent_id,
    servicecategory_m.servicecategory_nama AS name,
        CASE
            WHEN servicecategory_m.is_obat = false THEN true
            ELSE false
        END AS sync_is_service,
        CASE
            WHEN servicecategory_m.is_obat = false THEN 'normal'::text
            ELSE 'normal'::text
        END AS type,
    servicecategory_m.is_active AS active,
    6 AS sync_type,
    servicecategory_m.servicecategory_id,
    servicecategory_m.additional_data
   FROM servicecategory_m;");

        $this->execute('ALTER TABLE "public"."int_servicecategory_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_paket_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_paket_v\" AS  SELECT concat('PKT', tipepaket_m.tipepaket_id) AS sync_id_api,
    tipepaket_m.is_active AS active,
    true AS sale_ok,
    true AS purchase_ok,
    tipepaket_m.tipepaket_nama AS name,
    351 AS uom_id,
    tipepaket_m.tipepaket_kode AS default_code,
    'service'::text AS type,
    false AS wipro_block,
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
    tipepaket_m.tipepaket_id,
    tipepaket_m.additional_data
   FROM tipepaket_m
  WHERE tipepaket_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."int_paket_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_tindakan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_tindakan_v\" AS  SELECT concat('TND', daftartindakan_m.daftartindakan_id) AS sync_id_api,
    daftartindakan_m.is_active AS active,
    true AS sale_ok,
    true AS purchase_ok,
    daftartindakan_m.daftartindakan_nama AS name,
    concat('TND', daftartindakan_m.kelompoktindakan_id) AS categ_id,
    351 AS uom_id,
    daftartindakan_m.daftartindakan_kode AS default_code,
    'service'::text AS type,
    false AS wipro_block,
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
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.additional_data
   FROM daftartindakan_m
  WHERE daftartindakan_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."int_tindakan_v" OWNER TO "postgres";');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201207_005020_oddo_penyesuaianview_20201206 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201207_005020_oddo_penyesuaianview_20201206 cannot be reverted.\n";

        return false;
    }
    */
}
