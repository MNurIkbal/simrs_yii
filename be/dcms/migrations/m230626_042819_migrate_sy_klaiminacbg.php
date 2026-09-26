<?php

use yii\db\Migration;

/**
 * Class m230626_042819_migrate_sy_klaiminacbg
 */
class m230626_042819_migrate_sy_klaiminacbg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE sy_klaiminacbg 
            ADD IF NOT EXISTS ventilator_start varchar(100) NULL,
            ADD IF NOT EXISTS ventilator_stop varchar(100) NULL,
            ADD IF NOT EXISTS number_pasientb varchar(100) NULL,
            ADD IF NOT EXISTS number_coinsidensecovid varchar(100) NULL,
            ADD IF NOT EXISTS is_pasientb bool NOT NULL DEFAULT false,
            ADD IF NOT EXISTS is_coinsidensecovid bool NOT NULL DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230626_042819_migrate_sy_klaiminacbg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230626_042819_migrate_sy_klaiminacbg cannot be reverted.\n";

        return false;
    }
    */
}
