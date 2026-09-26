<?php

use yii\db\Migration;

/**
 * Class m220704_044521_migrate_BTS429_pegawai_m
 */
class m220704_044521_migrate_BTS429_pegawai_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."pegawai_m" 
            ADD COLUMN IF NOT EXISTS "photopegawai_blob" text COLLATE "pg_catalog"."default";
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220704_044521_migrate_BTS429_pegawai_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220704_044521_migrate_BTS429_pegawai_m cannot be reverted.\n";

        return false;
    }
    */
}
