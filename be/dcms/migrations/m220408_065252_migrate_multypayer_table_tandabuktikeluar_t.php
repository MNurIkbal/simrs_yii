<?php

use yii\db\Migration;

/**
 * Class m220408_065252_migrate_multypayer_table_tandabuktikeluar_t
 */
class m220408_065252_migrate_multypayer_table_tandabuktikeluar_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE tandabuktikeluar_t ADD IF NOT EXISTS pembatalanpembayaran_id int8;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_065252_migrate_multypayer_table_tandabuktikeluar_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_065252_migrate_multypayer_table_tandabuktikeluar_t cannot be reverted.\n";

        return false;
    }
    */
}
