<?php

use yii\db\Migration;

/**
 * Class m201222_012832_oddo_20201222_penyesuaianviewmaster
 */
class m201222_012832_oddo_20201222_penyesuaianviewmaster extends Migration
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
    true AS active,
    6 AS sync_type,
    kelompoktindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.additional_data,
        CASE
            WHEN kelompoktindakan_m.is_active IS TRUE AND kelompoktindakan_m.is_deleted IS TRUE THEN true
            WHEN kelompoktindakan_m.is_active IS TRUE AND kelompoktindakan_m.is_deleted IS FALSE THEN false
            WHEN kelompoktindakan_m.is_active IS FALSE AND kelompoktindakan_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
   FROM kelompoktindakan_m;");

        $this->execute('ALTER TABLE "public"."int_kelompoktindakan_v" OWNER TO "postgres";');

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
    tipepaket_m.tipepaket_id,
    tipepaket_m.additional_data
   FROM tipepaket_m;");

        $this->execute('ALTER TABLE "public"."int_paket_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_tindakan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_tindakan_v\" AS  SELECT concat('TND', daftartindakan_m.daftartindakan_id) AS sync_id_api,
    true AS active,
    true AS sale_ok,
    true AS purchase_ok,
    daftartindakan_m.daftartindakan_nama AS name,
    concat('TND', daftartindakan_m.kelompoktindakan_id) AS categ_id,
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
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.additional_data
   FROM daftartindakan_m;");

        $this->execute('ALTER TABLE "public"."int_tindakan_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_kelompokobat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_kelompokobat_v\" AS  SELECT concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS sync_id_api,
    '-'::text AS parent_id,
    jenisobatalkes_m.jenisobatalkes_nama AS name,
    false AS sync_is_service,
    'normal'::text AS type,
    true AS active,
    6 AS sync_type,
    jenisobatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.additional_data,
        CASE
            WHEN jenisobatalkes_m.is_active IS TRUE AND jenisobatalkes_m.is_deleted IS TRUE THEN true
            WHEN jenisobatalkes_m.is_active IS TRUE AND jenisobatalkes_m.is_deleted IS FALSE THEN false
            WHEN jenisobatalkes_m.is_active IS FALSE AND jenisobatalkes_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
   FROM jenisobatalkes_m;");

        $this->execute('ALTER TABLE "public"."int_kelompokobat_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_ruangan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_ruangan_v\" AS  SELECT ruangan_m.ruangan_id AS sync_id_api,
    '-'::text AS parent_id,
    ruangan_m.ruangan_nama AS name,
    true AS active,
    6 AS sync_type,
    ruangan_m.ruangan_id,
    ruangan_m.additional_data,
    ruangan_m.is_store,
    ruangan_m.is_mainstore,
    ruangan_m.is_substore,
    ruangan_m.is_cartstore,
        CASE
            WHEN ruangan_m.is_active IS TRUE AND ruangan_m.is_deleted IS TRUE THEN true
            WHEN ruangan_m.is_active IS TRUE AND ruangan_m.is_deleted IS FALSE THEN false
            WHEN ruangan_m.is_active IS FALSE AND ruangan_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
   FROM ruangan_m;");

        $this->execute('ALTER TABLE "public"."int_ruangan_v" OWNER TO "postgres";');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201222_012832_oddo_20201222_penyesuaianviewmaster cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201222_012832_oddo_20201222_penyesuaianviewmaster cannot be reverted.\n";

        return false;
    }
    */
}
