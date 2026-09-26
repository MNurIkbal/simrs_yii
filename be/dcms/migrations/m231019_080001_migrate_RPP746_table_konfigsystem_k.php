<?php

use yii\db\Migration;

/**
 * Class m231019_080001_migrate_RPP746_table_konfigsystem_k
 */
class m231019_080001_migrate_RPP746_table_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE konfigsystem_k ADD IF NOT EXISTS klasifikasi_pasien_umum int4 DEFAULT 2;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231019_080001_migrate_RPP746_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231019_080001_migrate_RPP746_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
