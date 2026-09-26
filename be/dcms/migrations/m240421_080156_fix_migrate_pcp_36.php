<?php

use yii\db\Migration;

/**
 * Class m240421_080156_fix_migrate_pcp_36
 */
class m240421_080156_fix_migrate_pcp_36 extends Migration
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
        echo "m240421_080156_fix_migrate_pcp_36 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240421_080156_fix_migrate_pcp_36 cannot be reverted.\n";

        return false;
    }
    */
}
