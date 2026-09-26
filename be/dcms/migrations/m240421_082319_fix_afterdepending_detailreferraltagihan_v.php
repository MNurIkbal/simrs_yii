<?php

use yii\db\Migration;

/**
 * Class m240421_082319_fix_afterdepending_detailreferraltagihan_v
 */
class m240421_082319_fix_afterdepending_detailreferraltagihan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS detailreferraltagihan_v");

        
        $this->execute("DROP VIEW IF EXISTS infopasiensudahbayar_v");
        $infopasiensudahbayar_v = file_get_contents(__DIR__ . '/definitions/infopasiensudahbayar_v.sql');
        $this->execute($infopasiensudahbayar_v);
        
        $detailreferraltagihan_v = file_get_contents(__DIR__ . '/definitions/detailreferraltagihan_v.sql');
        $this->execute($detailreferraltagihan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240421_082319_fix_afterdepending_detailreferraltagihan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240421_082319_fix_afterdepending_detailreferraltagihan_v cannot be reverted.\n";

        return false;
    }
    */
}
