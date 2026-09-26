<?php

use yii\db\Migration;

/**
 * Class m230918_094850_eklaim_gabung_billing_sy_infopasienbpjsklaimlist_v
 */
class m230918_094850_eklaim_gabung_billing_sy_infopasienbpjsklaimlist_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_infopasienbpjsklaimlist_v");
        $sy_infopasienbpjsklaimlist_v = file_get_contents(__DIR__ . '/definitions/sy_infopasienbpjsklaimlist_v.sql');
        $this->execute($sy_infopasienbpjsklaimlist_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230918_094850_eklaim_gabung_billing_sy_infopasienbpjsklaimlist_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230918_094850_eklaim_gabung_billing_sy_infopasienbpjsklaimlist_v cannot be reverted.\n";

        return false;
    }
    */
}
