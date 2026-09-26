<?php

use yii\db\Migration;

/**
 * Class m230203_060517_migrate_GB818_table_antrian_t
 */
class m230203_060517_migrate_GB818_table_antrian_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE antrian_t ADD IF NOT EXISTS "no_antrian_global" varchar(50) ;
        ');

        $this->execute('
            ALTER TABLE antrian_t ADD IF NOT EXISTS "no_antrian_global_with_date" varchar(50) ;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230203_060517_migrate_GB818_table_antrian_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230203_060517_migrate_GB818_table_antrian_t cannot be reverted.\n";

        return false;
    }
    */
}
