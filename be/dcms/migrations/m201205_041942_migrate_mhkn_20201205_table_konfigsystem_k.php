<?php

use yii\db\Migration;

/**
 * Class m201205_041942_migrate_mhkn_20201205_table_konfigsystem_k
 */
class m201205_041942_migrate_mhkn_20201205_table_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD IF NOT EXISTS "jenis_label_pendaftaran" text COLLATE "pg_catalog"."default";');
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD IF NOT EXISTS "jumlah_label_pendaftaran" int2;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201205_041942_migrate_mhkn_20201205_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201205_041942_migrate_mhkn_20201205_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
