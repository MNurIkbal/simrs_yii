<?php

use yii\db\Migration;

/**
 * Class m220413_093101_migrate_DHC367_table_pasienpulang_t
 */
class m220413_093101_migrate_DHC367_table_pasienpulang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS kamarruangan_jenis int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220413_093101_migrate_DHC367_table_pasienpulang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220413_093101_migrate_DHC367_table_pasienpulang_t cannot be reverted.\n";

        return false;
    }
    */
}
