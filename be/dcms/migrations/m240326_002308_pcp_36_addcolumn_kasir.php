<?php

use yii\db\Migration;

/**
 * Class m240326_002308_pcp_36_addcolumn_kasir
 */
class m240326_002308_pcp_36_addcolumn_kasir extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infotagihanpasienpulang_v");
        $infotagihanpasienpulang_v = file_get_contents(__DIR__ . '/definitions/infotagihanpasienpulang_v.sql');
        $this->execute($infotagihanpasienpulang_v);
        
        $this->execute("DROP VIEW IF EXISTS infopasienbelumbayar_v");
        $infopasienbelumbayar_v = file_get_contents(__DIR__ . '/definitions/infopasienbelumbayar_v.sql');
        $this->execute($infopasienbelumbayar_v);
        
        $this->execute("DROP VIEW IF EXISTS infopasiensudahbayar_v");
        $infopasiensudahbayar_v = file_get_contents(__DIR__ . '/definitions/infopasiensudahbayar_v.sql');
        $this->execute($infopasiensudahbayar_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240326_002308_pcp_36_addcolumn_kasir cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240326_002308_pcp_36_addcolumn_kasir cannot be reverted.\n";

        return false;
    }
    */
}
