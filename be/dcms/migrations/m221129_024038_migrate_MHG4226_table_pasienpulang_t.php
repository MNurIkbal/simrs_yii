<?php

use yii\db\Migration;

/**
 * Class m221129_024038_migrate_MHG4226_table_pasienpulang_t
 */
class m221129_024038_migrate_MHG4226_table_pasienpulang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS dokterspesialis_id int4;
        ');

        $this->execute('
            ALTER TABLE pasienpulang_t ADD IF NOT EXISTS catatan_tindakan text;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221129_024038_migrate_MHG4226_table_pasienpulang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_024038_migrate_MHG4226_table_pasienpulang_t cannot be reverted.\n";

        return false;
    }
    */
}
