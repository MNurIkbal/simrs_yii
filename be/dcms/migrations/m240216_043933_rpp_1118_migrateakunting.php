<?php

use yii\db\Migration;

/**
 * Class m240216_043933_rpp_1118_migrateakunting
 */
class m240216_043933_rpp_1118_migrateakunting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_saleorderbill_v");
        $newodoo_saleorderbill_v = file_get_contents(__DIR__ . '/definitions/newodoo_saleorderbill_v.sql');
        $this->execute($newodoo_saleorderbill_v);
        
        $this->execute("DROP VIEW IF EXISTS newodoo_scroll_v");
        $newodoo_scroll_v = file_get_contents(__DIR__ . '/definitions/newodoo_scroll_v.sql');
        $this->execute($newodoo_scroll_v);

        $pembayaran_r_nontunai = file_get_contents(__DIR__ . '/definitions/pembayaran_r_nontunai.fn.sql');
        $this->execute($pembayaran_r_nontunai);

        $tindakansudahbayar_t_insert = file_get_contents(__DIR__ . '/definitions/tindakansudahbayar_t_insert.fn.sql');
        $this->execute($tindakansudahbayar_t_insert);

        $this->execute("DROP VIEW IF EXISTS newodoo_grnreceipt_v");
        $newodoo_grnreceipt_v = file_get_contents(__DIR__ . '/definitions/newodoo_grnreceipt_v.sql');
        $this->execute($newodoo_grnreceipt_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240216_043933_rpp_1118_migrateakunting cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240216_043933_rpp_1118_migrateakunting cannot be reverted.\n";

        return false;
    }
    */
}
