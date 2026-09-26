<?php

use yii\db\Migration;

/**
 * Class m230725_094406_alter_sy_kunjungan
 */
class m230725_094406_alter_sy_kunjungan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE sy_kunjungan 
            ADD IF NOT EXISTS caramasuk varchar(20) NULL;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230725_094406_alter_sy_kunjungan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230725_094406_alter_sy_kunjungan cannot be reverted.\n";

        return false;
    }
    */
}
