<?php

use yii\db\Migration;

/**
 * Class m221102_063813_migrate_plafon_table_pembayaran_t
 */
class m221102_063813_migrate_plafon_table_pembayaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pembayaran_t  ADD IF NOT EXISTS is_plafon BOOLEAN DEFAULT FALSE;
        ');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221102_063813_migrate_plafon_table_pembayaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221102_063813_migrate_plafon_table_pembayaran_t cannot be reverted.\n";

        return false;
    }
    */
}
