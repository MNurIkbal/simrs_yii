<?php

use yii\db\Migration;

/**
 * Class m231025_111334_hotfix_invoicepasienbelumbayar_v
 */
class m231025_111334_hotfix_invoicepasienbelumbayar_v extends Migration
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
        echo "m231025_111334_hotfix_invoicepasienbelumbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231025_111334_hotfix_invoicepasienbelumbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
