<?php

use yii\db\Migration;

/**
 * Class m240116_051537_rpp_1051_845_migrate_akunting
 */
class m240116_051537_rpp_1051_845_migrate_akunting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_grnreceipt_v");
        $newodoo_grnreceipt_v = file_get_contents(__DIR__ . '/definitions/newodoo_grnreceipt_v.sql');
        $this->execute($newodoo_grnreceipt_v);
        
        $this->execute("DROP VIEW IF EXISTS newodoo_scroll_v");
        $newodoo_scroll_v = file_get_contents(__DIR__ . '/definitions/newodoo_scroll_v.sql');
        $this->execute($newodoo_scroll_v);
        
        $this->execute("DROP VIEW IF EXISTS newodoo_tindakanpaket_v");
        $newodoo_tindakanpaket_v = file_get_contents(__DIR__ . '/definitions/newodoo_tindakanpaket_v.sql');
        $this->execute($newodoo_tindakanpaket_v);
        
        $this->execute("DROP VIEW IF EXISTS int_barang");
        $int_barang = file_get_contents(__DIR__ . '/definitions/int_barang.sql');
        $this->execute($int_barang);
        
        $this->execute("DROP VIEW IF EXISTS int_obat");
        $int_obat = file_get_contents(__DIR__ . '/definitions/int_obat.sql');
        $this->execute($int_obat);
        
        $this->execute("DROP VIEW IF EXISTS newodoo_groupinacbg");
        $newodoo_groupinacbg = file_get_contents(__DIR__ . '/definitions/newodoo_groupinacbg.sql');
        $this->execute($newodoo_groupinacbg);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240116_051537_rpp_1051_845_migrate_akunting cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240116_051537_rpp_1051_845_migrate_akunting cannot be reverted.\n";

        return false;
    }
    */
}
