<?php

use yii\db\Migration;

/**
 * Class m220620_102442_migrate_MHG2441_table_periksatubuh_t
 */
class m220620_102442_migrate_MHG2441_table_periksatubuh_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE periksatubuh_t ADD IF NOT EXISTS jenis_gambar_id int4,
                ADD IF NOT EXISTS jenis_gambar_nama VARCHAR(100),
                ADD IF NOT EXISTS berat_luka_bakar TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220620_102442_migrate_MHG2441_table_periksatubuh_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220620_102442_migrate_MHG2441_table_periksatubuh_t cannot be reverted.\n";

        return false;
    }
    */
}
