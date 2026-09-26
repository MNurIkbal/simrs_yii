<?php

use yii\db\Migration;

/**
 * Class m220408_065249_migrate_multypayer_table_carakeluar_m
 */
class m220408_065249_migrate_multypayer_table_carakeluar_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE carakeluar_m ADD IF NOT EXISTS is_freetext BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_065249_migrate_multypayer_table_carakeluar_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_065249_migrate_multypayer_table_carakeluar_m cannot be reverted.\n";

        return false;
    }
    */
}
