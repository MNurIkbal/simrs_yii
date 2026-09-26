<?php

use yii\db\Migration;

/**
 * Class m230918_095404_eklaim_gabung_billing_koreksidiagnosa_v
 */
class m230918_095404_eklaim_gabung_billing_koreksidiagnosa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS koreksidiagnosa_v");
        $koreksidiagnosa_v = file_get_contents(__DIR__ . '/definitions/koreksidiagnosa_v.sql');
        $this->execute($koreksidiagnosa_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230918_095404_eklaim_gabung_billing_koreksidiagnosa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230918_095404_eklaim_gabung_billing_koreksidiagnosa_v cannot be reverted.\n";

        return false;
    }
    */
}
