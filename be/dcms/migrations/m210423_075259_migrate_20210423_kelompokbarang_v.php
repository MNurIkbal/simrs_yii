<?php

use yii\db\Migration;

/**
 * Class m210423_075259_migrate_20210423_kelompokbarang_v
 */
class m210423_075259_migrate_20210423_kelompokbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."kelompokbarang_v";');

    $this->execute("
        CREATE VIEW \"public\".\"kelompokbarang_v\" AS  SELECT kelompokbarang_m.kelompokbarang_id,
    kelompokbarang_m.kelompokbarang_kode,
    kelompokbarang_m.kelompokbarang_nama,
    kelompokbarang_m.kelompokbarang_namalain,
    kelompokbarang_m.is_active,
    kelompokbarang_m.is_deleted,
    kelompokbarang_m.servicecategory_id,
    servicecategory_m.servicecategory_nama
   FROM kelompokbarang_m
     LEFT JOIN servicecategory_m ON kelompokbarang_m.servicecategory_id = servicecategory_m.servicecategory_id;");

    $this->execute('ALTER TABLE "public"."kelompokbarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210423_075259_migrate_20210423_kelompokbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210423_075259_migrate_20210423_kelompokbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
