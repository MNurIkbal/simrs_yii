<?php

use yii\db\Migration;

/**
 * Class m220930_033621_alter_modul_k_add_url_page
 */
class m220930_033621_alter_modul_k_add_url_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE modul_k ADD IF NOT EXISTS url_page varchar(100) NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220930_033621_alter_modul_k_add_url_page cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220930_033621_alter_modul_k_add_url_page cannot be reverted.\n";

        return false;
    }
    */
}
