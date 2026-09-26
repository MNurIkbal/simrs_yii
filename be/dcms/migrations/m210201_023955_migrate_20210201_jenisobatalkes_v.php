<?php

use yii\db\Migration;

/**
 * Class m210201_023955_migrate_20210201_jenisobatalkes_v
 */
class m210201_023955_migrate_20210201_jenisobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."jenisobatalkes_v";');

         $this->execute("
            CREATE VIEW \"public\".\"jenisobatalkes_v\" AS  SELECT jenisobatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.jenisobatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_kode,
    jenisobatalkes_m.is_sync,
    jenisobatalkes_m.group_jenisobat,
    fgetnamalookup(jenisobatalkes_m.group_jenisobat::integer) AS group_jenisobat_nama,
    jenisobatalkes_m.jenisobatalkes_namalain,
    jenisobatalkes_m.servicecategory_id,
    servicecategory_m.servicecategory_nama,
    jenisobatalkes_m.servicegroup_id,
    servicegroup_m.servicegroup_nama
   FROM jenisobatalkes_m
     LEFT JOIN servicecategory_m ON jenisobatalkes_m.servicecategory_id = servicecategory_m.servicecategory_id
     LEFT JOIN servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
  WHERE jenisobatalkes_m.is_deleted = false;");
         
         $this->execute('ALTER TABLE "public"."jenisobatalkes_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210201_023955_migrate_20210201_jenisobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210201_023955_migrate_20210201_jenisobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
