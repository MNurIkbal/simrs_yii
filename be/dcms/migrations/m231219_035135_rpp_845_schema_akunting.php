<?php

use yii\db\Migration;

/**
 * Class m231219_035135_rpp_845_schema_akunting
 */
class m231219_035135_rpp_845_schema_akunting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_grnissue_v");
        $newodoo_grnissue_v = file_get_contents(__DIR__ . '/definitions/newodoo_grnissue_v.sql');
        $this->execute($newodoo_grnissue_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_grnissuedetail_v");
        $newodoo_grnissuedetail_v = file_get_contents(__DIR__ . '/definitions/newodoo_grnissuedetail_v.sql');
        $this->execute($newodoo_grnissuedetail_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_grnreceipt_v");
        $newodoo_grnreceipt_v = file_get_contents(__DIR__ . '/definitions/newodoo_grnreceipt_v.sql');
        $this->execute($newodoo_grnreceipt_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_grnreceiptdetail_v");
        $newodoo_grnreceiptdetail_v = file_get_contents(__DIR__ . '/definitions/newodoo_grnreceiptdetail_v.sql');
        $this->execute($newodoo_grnreceiptdetail_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_inpatientdeposit_v");
        $newodoo_inpatientdeposit_v = file_get_contents(__DIR__ . '/definitions/newodoo_inpatientdeposit_v.sql');
        $this->execute($newodoo_inpatientdeposit_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_pasien_v");
        $newodoo_pasien_v = file_get_contents(__DIR__ . '/definitions/newodoo_pasien_v.sql');
        $this->execute($newodoo_pasien_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_patientdebt");
        $newodoo_patientdebt = file_get_contents(__DIR__ . '/definitions/newodoo_patientdebt.sql');
        $this->execute($newodoo_patientdebt);

        $this->execute("DROP VIEW IF EXISTS newodoo_productcategory_v");
        $newodoo_productcategory_v = file_get_contents(__DIR__ . '/definitions/newodoo_productcategory_v.sql');
        $this->execute($newodoo_productcategory_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_saleorder_v");
        $newodoo_saleorder_v = file_get_contents(__DIR__ . '/definitions/newodoo_saleorder_v.sql');
        $this->execute($newodoo_saleorder_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_saleorderbill_v");
        $newodoo_saleorderbill_v = file_get_contents(__DIR__ . '/definitions/newodoo_saleorderbill_v.sql');
        $this->execute($newodoo_saleorderbill_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_saleorderline_v");
        $newodoo_saleorderline_v = file_get_contents(__DIR__ . '/definitions/newodoo_saleorderline_v.sql');
        $this->execute($newodoo_saleorderline_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_saleorderlineobat_v");
        $newodoo_saleorderlineobat_v = file_get_contents(__DIR__ . '/definitions/newodoo_saleorderlineobat_v.sql');
        $this->execute($newodoo_saleorderlineobat_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_scroll_v");
        $newodoo_scroll_v = file_get_contents(__DIR__ . '/definitions/newodoo_scroll_v.sql');
        $this->execute($newodoo_scroll_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_stockscrap_v");
        $newodoo_stockscrap_v = file_get_contents(__DIR__ . '/definitions/newodoo_stockscrap_v.sql');
        $this->execute($newodoo_stockscrap_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_tindakanpaket_v");
        $newodoo_tindakanpaket_v = file_get_contents(__DIR__ . '/definitions/newodoo_tindakanpaket_v.sql');
        $this->execute($newodoo_tindakanpaket_v);

        $this->execute("DROP VIEW IF EXISTS newodoo_uom_v");
        $newodoo_uom_v = file_get_contents(__DIR__ . '/definitions/newodoo_uom_v.sql');
        $this->execute($newodoo_uom_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231219_035135_rpp_845_schema_akunting cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231219_035135_rpp_845_schema_akunting cannot be reverted.\n";

        return false;
    }
    */
}
