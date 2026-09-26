<?php

use yii\db\Migration;

/**
 * Class m230920_153126_rpp_391_invoicepasienbelumbayar_v
 */
class m230920_153126_rpp_391_invoicepasienbelumbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE racikan_m ADD COLUMN IF NOT EXISTS namakelompokinvoice VARCHAR(255) NULL");

        $this->execute("DROP VIEW IF EXISTS invoicepasienbelumbayar_v");
        $invoicepasienbelumbayar_v = file_get_contents(__DIR__ . '/definitions/invoicepasienbelumbayar_v.view.sql');
        $this->execute($invoicepasienbelumbayar_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230920_153126_rpp_391_invoicepasienbelumbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230920_153126_rpp_391_invoicepasienbelumbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
