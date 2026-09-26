<?php

use yii\db\Migration;

/**
 * Class m201215_034950_migrate_mhkn_20201215_tabel_pasien_m
 */
class m201215_034950_migrate_mhkn_20201215_tabel_pasien_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasien_m" 
  ADD COLUMN IF NOT EXISTS "is_mergerm" text COLLATE "pg_catalog"."default";');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201215_034950_migrate_mhkn_20201215_tabel_pasien_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201215_034950_migrate_mhkn_20201215_tabel_pasien_m cannot be reverted.\n";

        return false;
    }
    */
}
