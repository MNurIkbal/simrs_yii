<?php

use yii\db\Migration;

/**
 * Class m210113_015556_migrate_20200113_int_obat_v
 */
class m210113_015556_migrate_20200113_int_obat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.int_obat_v;');
        
        $this->execute("
            CREATE VIEW \"public\".\"int_obat_v\" AS  SELECT concat('OBT', obatalkes_r.obatalkes_id) AS sync_id_api,
    true AS active,
    true AS sale_ok,
    true AS purchase_ok,
    obatalkes_r.obatalkes_nama AS name,
    concat('OBT', obatalkes_r.jenisobatalkes_id) AS categ_id,
    COALESCE(obatalkes_r.satuanbesar_id, obatalkes_r.satuankecil_id) AS uom_po_id,
    COALESCE(obatalkes_r.satuanbesar_id, obatalkes_r.satuankecil_id) AS uom2_id,
    obatalkes_r.satuankecil_id AS uom_id,
    obatalkes_r.obatalkes_kode AS default_code,
    'product'::text AS type,
        CASE
            WHEN obatalkes_r.is_active IS TRUE AND obatalkes_r.is_deleted IS TRUE THEN true
            WHEN obatalkes_r.is_active IS TRUE AND obatalkes_r.is_deleted IS FALSE THEN false
            WHEN obatalkes_r.is_active IS FALSE AND obatalkes_r.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    obatalkes_r.strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
        CASE
            WHEN obatalkes_r.satuanbesar_id IS NULL THEN 1
            ELSE obatalkes_r.kemasan_besar
        END AS conversion_rate,
    6 AS sync_type,
    obatalkes_r.keterangan_rekap,
    obatalkes_r.id,
    obatalkes_r.is_sent,
    obatalkes_r.is_sending
   FROM obatalkes_r;");
        
        $this->execute('ALTER TABLE "public"."int_obat_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210113_015556_migrate_20200113_int_obat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210113_015556_migrate_20200113_int_obat_v cannot be reverted.\n";

        return false;
    }
    */
}
