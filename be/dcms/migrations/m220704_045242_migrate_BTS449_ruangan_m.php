<?php

use yii\db\Migration;

/**
 * Class m220704_045242_migrate_BTS449_ruangan_m
 */
class m220704_045242_migrate_BTS449_ruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."ruangan_m" 
            ADD COLUMN IF NOT EXISTS "ruangan_image_blob" text COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "ruangan_filesuara_blob" text COLLATE "pg_catalog"."default";
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220704_045242_migrate_BTS449_ruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220704_045242_migrate_BTS449_ruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
