<?php

use yii\db\Migration;

/**
 * Class m201229_082528_migrate_20201229_SY_view_asuransipasien_m
 */
class m201229_082528_migrate_20201229_SY_view_asuransipasien_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."asuransipasien_m"
ADD COLUMN IF NOT EXISTS "namabagian" varchar(50) COLLATE "pg_catalog"."default",
ADD COLUMN IF NOT EXISTS "noindukkaryawan" varchar(100) COLLATE "pg_catalog"."default",
ADD COLUMN IF NOT EXISTS "jpkm" varchar(100);');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201229_082528_migrate_20201229_SY_view_asuransipasien_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201229_082528_migrate_20201229_SY_view_asuransipasien_m cannot be reverted.\n";

        return false;
    }
    */
}
