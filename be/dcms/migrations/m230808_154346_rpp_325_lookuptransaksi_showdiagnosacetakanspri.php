<?php

use yii\db\Migration;

/**
 * Class m230808_154346_rpp_325_lookuptransaksi_showdiagnosacetakanspri
 */
class m230808_154346_rpp_325_lookuptransaksi_showdiagnosacetakanspri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'show_diagnosa_cetakan_spri';");

        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi,additional_value) VALUES
        ('show_diagnosa_cetakan_spri',1,'konfig untuk memunculkan atau tidak diagnosa di cetakan SPRI','true');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230808_154346_rpp_325_lookuptransaksi_showdiagnosacetakanspri cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230808_154346_rpp_325_lookuptransaksi_showdiagnosacetakanspri cannot be reverted.\n";

        return false;
    }
    */
}
