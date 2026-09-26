<?php

use yii\db\Migration;

/**
 * Class m220613_092038_migrate_MHG2523_table_tindakanluaroperasi_t
 */
class m220613_092038_migrate_MHG2523_table_tindakanluaroperasi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE tindakanluaroperasi_t ADD IF NOT EXISTS parent_id int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220613_092038_migrate_MHG2523_table_tindakanluaroperasi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220613_092038_migrate_MHG2523_table_tindakanluaroperasi_t cannot be reverted.\n";

        return false;
    }
    */
}
