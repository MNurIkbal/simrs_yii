<?php

use yii\db\Migration;

/**
 * Class m221205_024351_migrate_GB_52_lookuptransaksi_m
 */
class m221205_024351_migrate_GB_52_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
			INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
			  select 'config_zero_stock', 1, 'Status untuk menunjukan apakah pelayanan mengizinkan pemilihan obat dengan stok 0', NULL, NULL, NULL
				where not EXISTS ( SELECT kode_transaksi from lookuptransaksi_m WHERE kode_transaksi = 'config_zero_stock');
			" );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221205_024351_migrate_GB_52_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221205_024351_migrate_GB_52_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
