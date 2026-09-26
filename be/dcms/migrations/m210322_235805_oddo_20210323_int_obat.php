<?php

use yii\db\Migration;

/**
 * Class m210322_235805_oddo_20210323_int_obat
 */
class m210322_235805_oddo_20210323_int_obat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_obat";');
        
        $this->execute("
            CREATE VIEW \"public\".\"int_obat\" AS  SELECT concat('OBT', obatalkes_m.obatalkes_id) AS sync_id_api,
    true AS active,
    true AS sale_ok,
    true AS purchase_ok,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS categ_id,
    COALESCE(obatalkes_m.satuanbesar_id, obatalkes_m.satuankecil_id) AS uom_po_id,
    COALESCE(obatalkes_m.satuanbesar_id, obatalkes_m.satuankecil_id) AS uom2_id,
    obatalkes_m.satuankecil_id AS uom_id,
    obatalkes_m.obatalkes_kode AS default_code,
    'product'::text AS type,
        CASE
            WHEN obatalkes_m.is_active IS TRUE AND obatalkes_m.is_deleted IS TRUE THEN true
            WHEN obatalkes_m.is_active IS TRUE AND obatalkes_m.is_deleted IS FALSE THEN false
            WHEN obatalkes_m.is_active IS FALSE AND obatalkes_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    obatalkes_m.strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
        CASE
            WHEN obatalkes_m.satuanbesar_id IS NULL THEN 1
            ELSE obatalkes_m.kemasan_besar
        END AS conversion_rate,
    6 AS sync_type,
    'OBAT'::text AS jenis,
    obatalkes_m.obatalkes_id AS id,
    obatalkes_m.additional_data,
        CASE
            WHEN (obatalkes_m.additional_data::json ->> 'is_sending'::text) = 'true'::text THEN 'SUKSES'::text
            WHEN (obatalkes_m.additional_data::json ->> 'is_sending'::text) = 'false'::text THEN 'GAGAL'::text
            ELSE 'DALAM PROSES'::text
        END AS status,
    obatalkes_m.obatalkes_id
   FROM obatalkes_m;");

        $this->execute('ALTER TABLE "public"."int_obat" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210322_235805_oddo_20210323_int_obat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210322_235805_oddo_20210323_int_obat cannot be reverted.\n";

        return false;
    }
    */
}
