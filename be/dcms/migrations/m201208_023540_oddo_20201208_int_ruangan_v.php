<?php

use yii\db\Migration;

/**
 * Class m201208_023540_oddo_20201208_int_ruangan_v
 */
class m201208_023540_oddo_20201208_int_ruangan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_ruangan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_ruangan_v\" AS  SELECT ruangan_m.ruangan_id AS sync_id_api,
    '-'::text AS parent_id,
    ruangan_m.ruangan_nama AS name,
    true AS is_store,
    false AS is_mainstore,
    false AS is_substore,
    false AS is_cartstore,
    ruangan_m.is_active AS active,
    6 AS sync_type,
    ruangan_m.ruangan_id,
    ruangan_m.additional_data
   FROM ruangan_m
  WHERE ruangan_m.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."int_ruangan_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201208_023540_oddo_20201208_int_ruangan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201208_023540_oddo_20201208_int_ruangan_v cannot be reverted.\n";

        return false;
    }
    */
}
