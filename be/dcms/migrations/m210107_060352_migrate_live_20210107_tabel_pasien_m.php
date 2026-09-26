<?php

use yii\db\Migration;

/**
 * Class m210107_060352_migrate_live_20210107_tabel_pasien_m
 */
class m210107_060352_migrate_live_20210107_tabel_pasien_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasien_m" 
  ADD COLUMN IF NOT EXISTS "catatanpenting_pasien" text COLLATE "pg_catalog"."default",
  ADD COLUMN IF NOT EXISTS "is_mergerm" text COLLATE "pg_catalog"."default";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210107_060352_migrate_live_20210107_tabel_pasien_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_060352_migrate_live_20210107_tabel_pasien_m cannot be reverted.\n";

        return false;
    }
    */
}
