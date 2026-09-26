<?php

use yii\db\Migration;

/**
 * Class m241128_103645_RPP1950_infopasiensudahbayar_v
 */
class m241128_103645_RPP1950_infopasiensudahbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS detailreferraltagihan_v");
        $detailreferraltagihan_v = file_get_contents(__DIR__ . '/definitions/detailreferraltagihan_v.sql');
        $this->execute($detailreferraltagihan_v);

        $this->execute("DROP VIEW IF EXISTS infopasiensudahbayar_v");
        $infopasiensudahbayar_v = file_get_contents(__DIR__ . '/definitions/infopasiensudahbayar_v.sql');
        $this->execute($infopasiensudahbayar_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241128_103645_RPP1950_infopasiensudahbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241128_103645_RPP1950_infopasiensudahbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
