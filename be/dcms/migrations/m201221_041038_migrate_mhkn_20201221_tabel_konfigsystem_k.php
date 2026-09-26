<?php

use yii\db\Migration;

/**
 * Class m201221_041038_migrate_mhkn_20201221_tabel_konfigsystem_k
 */
class m201221_041038_migrate_mhkn_20201221_tabel_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k"
ADD COLUMN IF NOT EXISTS "jenis_print_sep" text COLLATE "pg_catalog"."default";
            ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201221_041038_migrate_mhkn_20201221_tabel_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201221_041038_migrate_mhkn_20201221_tabel_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
