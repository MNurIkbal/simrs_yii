<?php

use yii\db\Migration;

/**
 * Class m221110_040050_migrate_mhg4681_table_konfigsystem_k
 */
class m221110_040050_migrate_mhg4681_table_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        ALTER TABLE konfigsystem_k ADD IF NOT EXISTS is_show_obat_form_penatajasa BOOLEAN DEFAULT TRUE;
    ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221110_040050_migrate_mhg4681_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221110_040050_migrate_mhg4681_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
