<?php

use yii\db\Migration;

/**
 * Class m230704_091012_DSV314_improve_sy_kunjungan
 */
class m230704_091012_DSV314_improve_sy_kunjungan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE sy_kunjungan 
            ADD IF NOT EXISTS sistole int2 NULL,
            ADD IF NOT EXISTS diastole int2 NULL;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230704_091012_DSV314_improve_sy_kunjungan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230704_091012_DSV314_improve_sy_kunjungan cannot be reverted.\n";

        return false;
    }
    */
}
