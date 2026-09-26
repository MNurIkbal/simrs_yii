<?php

use yii\db\Migration;

/**
 * Class m201207_095540_oddo_20201207_penyesuaianview
 */
class m201207_095540_oddo_20201207_penyesuaianview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_kelompokobat_v";');

        $this->execute("CREATE VIEW \"public\".\"int_kelompokobat_v\" AS  SELECT concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS sync_id_api,
    '-'::text AS parent_id,
    jenisobatalkes_m.jenisobatalkes_nama AS name,
    false AS sync_is_service,
    'normal'::text AS type,
    jenisobatalkes_m.is_active AS active,
    6 AS sync_type,
    jenisobatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.additional_data
   FROM jenisobatalkes_m
  WHERE jenisobatalkes_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."int_kelompokobat_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."int_ruangan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_ruangan_v\" AS  SELECT ruangan_m.ruangan_id AS sync_id_api,
    ruangan_m.ruangan_id AS parent_id,
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
        echo "m201207_095540_oddo_20201207_penyesuaianview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201207_095540_oddo_20201207_penyesuaianview cannot be reverted.\n";

        return false;
    }
    */
}
