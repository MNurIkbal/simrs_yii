<?php

use yii\db\Migration;

/**
 * Class m210112_025044_migrate_20210112_asuransipasien_m
 */
class m210112_025044_migrate_20210112_asuransipasien_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."asuransipasien_m"
ADD COLUMN IF NOT EXISTS "namabagian" varchar(50) COLLATE "pg_catalog"."default",
ADD COLUMN IF NOT EXISTS "noindukkaryawan" varchar(100) COLLATE "pg_catalog"."default",
ADD COLUMN IF NOT EXISTS "jpkm" varchar(100),
ADD COLUMN IF NOT EXISTS "nama_asuransi" text COLLATE "pg_catalog"."default";');
    }
    

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210112_025044_migrate_20210112_asuransipasien_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210112_025044_migrate_20210112_asuransipasien_m cannot be reverted.\n";

        return false;
    }
    */
}
