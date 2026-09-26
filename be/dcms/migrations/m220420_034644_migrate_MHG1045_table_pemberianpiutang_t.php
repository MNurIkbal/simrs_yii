<?php

use yii\db\Migration;

/**
 * Class m220420_034644_migrate_MHG1045_table_pemberianpiutang_t
 */
class m220420_034644_migrate_MHG1045_table_pemberianpiutang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pemberianpiutang_t ADD IF NOT EXISTS alasan_batal TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220420_034644_migrate_MHG1045_table_pemberianpiutang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220420_034644_migrate_MHG1045_table_pemberianpiutang_t cannot be reverted.\n";

        return false;
    }
    */
}
