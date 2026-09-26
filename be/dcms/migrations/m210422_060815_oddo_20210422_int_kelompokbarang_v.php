<?php

use yii\db\Migration;

/**
 * Class m210422_060815_oddo_20210422_int_kelompokbarang_v
 */
class m210422_060815_oddo_20210422_int_kelompokbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
 $this->execute('DROP VIEW IF EXISTS int_kelompokbarang_v;');

 $this->execute("
    CREATE VIEW \"public\".\"int_kelompokbarang_v\" AS  SELECT concat('BRG', kelompokbarang_m.kelompokbarang_id) AS sync_id_api,
    '-'::text AS parent_id,
    kelompokbarang_m.kelompokbarang_nama AS name,
    false AS sync_is_service,
    'normal'::text AS type,
    true AS active,
    6 AS sync_type,
    kelompokbarang_m.kelompokbarang_id,
    kelompokbarang_m.additional_data,
        CASE
            WHEN kelompokbarang_m.is_active IS TRUE AND kelompokbarang_m.is_deleted IS TRUE THEN true
            WHEN kelompokbarang_m.is_active IS TRUE AND kelompokbarang_m.is_deleted IS FALSE THEN false
            WHEN kelompokbarang_m.is_active IS FALSE AND kelompokbarang_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block
   FROM kelompokbarang_m;");
 
 $this->execute('ALTER TABLE "public"."int_kelompokbarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210422_060815_oddo_20210422_int_kelompokbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210422_060815_oddo_20210422_int_kelompokbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
