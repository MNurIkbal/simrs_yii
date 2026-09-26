<?php

use yii\db\Migration;

/**
 * Class m250911_033126_RPP2197_invoicepasienbelumbayar_v
 */
class m250911_033126_RPP2197_invoicepasienbelumbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS invoicepasienbelumbayar_v");
        $invoicepasienbelumbayar_v = file_get_contents(__DIR__ . '/definitions/invoicepasienbelumbayar_v.view.sql');
        $this->execute($invoicepasienbelumbayar_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250911_033126_RPP2197_invoicepasienbelumbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250911_033126_RPP2197_invoicepasienbelumbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
