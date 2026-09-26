<?php

use yii\db\Migration;

/**
 * Class m230304_021121_alter_klaiminacbg_norekammedik
 */
class m221213_021121_alter_klaiminacbg_norekammedik extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS no_rekam_medik varchar(150) NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230304_021121_alter_klaiminacbg_norekammedik cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230304_021121_alter_klaiminacbg_norekammedik cannot be reverted.\n";

        return false;
    }
    */
}
